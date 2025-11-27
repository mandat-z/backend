<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$db = get_db();

$user = $_SESSION['user'] ?? null;
if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Harus login']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// --- ambil json input ---
$raw = json_decode(file_get_contents('php://input'), true);
$raw = is_array($raw) ? $raw : [];

$user_id = intval($user['id']);
$address_id = intval($raw['address_id'] ?? 0);
$payment_method_id = intval($raw['payment_method_id'] ?? 0);  // HARUS ID
$voucherCode = trim($raw['voucher'] ?? '');
$providedItems = $raw['items'] ?? null;

if (!$address_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Alamat belum dipilih']);
    exit;
}

try {
    $db->beginTransaction();

    // --- ambil item ---
    if (is_array($providedItems) && count($providedItems) > 0) {
        $cartItems = [];
        foreach ($providedItems as $it) {
            $pid = intval($it['produk_id'] ?? 0);
            $qty = intval($it['qty'] ?? 0);
            if ($pid > 0 && $qty > 0) {
                $cartItems[] = ['product_id' => $pid, 'quantity' => $qty];
            }
        }
    } else {
        // ambil dari carts
        $stmt = $db->prepare("
            SELECT c.id AS cart_id, c.product_id, c.quantity 
            FROM carts c 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $r) {
            $cartItems[] = [
                'product_id' => intval($r['product_id']),
                'quantity' => intval($r['quantity']),
                'cart_id' => intval($r['cart_id'])
            ];
        }
    }

    if (empty($cartItems)) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Keranjang kosong']);
        exit;
    }

    // --- ambil produk untuk validasi ---
    $productIds = array_column($cartItems, 'product_id');
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));

    $stmt = $db->prepare("SELECT * FROM produk WHERE id IN ($placeholders) FOR UPDATE");
    $stmt->execute($productIds);
    $products = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $p) {
        $products[$p['id']] = $p;
    }

    // --- hitung subtotal ---
    $subtotal = 0;
    foreach ($cartItems as $it) {
        $pid = $it['product_id'];
        $qty = $it['quantity'];

        $p = $products[$pid] ?? null;
        if (!$p) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => "Produk id $pid tidak ditemukan"]);
            exit;
        }

        if ($qty > $p['stok']) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => "Stok produk {$p['nama']} tidak cukup"]);
            exit;
        }

        $subtotal += $p['harga'] * $qty;
    }

    // --- ongkir ---
    $stmt = $db->prepare("SELECT kota_id FROM user_addresses WHERE id = ? AND user_id = ?");
    $stmt->execute([$address_id, $user_id]);
    $a = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$a) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Alamat tidak valid']);
        exit;
    }

    $stmt = $db->prepare("SELECT ongkir FROM cities WHERE id = ?");
    $stmt->execute([$a['kota_id']]);
    $ongkir = floatval($stmt->fetchColumn() ?? 0);

    // --- voucher ---
    $discount = 0;
    $applied_voucher = null;

    if ($voucherCode !== '') {
        $stmt = $db->prepare("SELECT * FROM tb_voucher WHERE kode_voucher = ? AND status = 'Aktif' LIMIT 1");
        $stmt->execute([$voucherCode]);
        $v = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($v && $subtotal >= $v['minimal_belanja']) {
            if ($v['tipe_diskon'] == 'persen') {
                $discount = round($subtotal * ($v['diskon'] / 100));
                if ($v['maksimal_diskon'] > 0 && $discount > $v['maksimal_diskon']) {
                    $discount = $v['maksimal_diskon'];
                }
            } else {
                $discount = $v['diskon'];
            }

            $applied_voucher = $voucherCode;
        }
    }

    // --- total ---
    $total_harga = $subtotal + $ongkir - $discount;
    if ($total_harga < 0) $total_harga = 0;

    // --- insert order ---
    $stmt = $db->prepare("
        INSERT INTO orders 
            (user_id, address_id, metode_pembayaran_id, subtotal, ongkir, potongan_voucher, kode_voucher, total_harga, status, tanggal_pesan)
        VALUES
            (:uid, :aid, :pm, :sub, :ongkir, :diskon, :kode, :total, 'pending', NOW())
    ");

    $stmt->execute([
        ':uid'   => $user_id,
        ':aid'   => $address_id,
        ':pm'    => $payment_method_id ?: null,
        ':sub'   => $subtotal,
        ':ongkir'=> $ongkir,
        ':diskon'=> $discount,
        ':kode'  => $applied_voucher,
        ':total' => $total_harga
    ]);

    $order_id = $db->lastInsertId();

    // --- insert order items ---
    $stmtItem = $db->prepare("
        INSERT INTO order_items (order_id, produk_id, qty, harga_satuan, subtotal)
        VALUES (:oid, :pid, :qty, :harga, :sub)
    ");

    $stmtStock = $db->prepare("
        UPDATE produk SET stok = stok - :qty WHERE id = :pid
    ");

    foreach ($cartItems as $it) {
        $pid = $it['product_id'];
        $qty = $it['quantity'];
        $harga = $products[$pid]['harga'];
        $line = $harga * $qty;

        $stmtItem->execute([
            ':oid' => $order_id,
            ':pid' => $pid,
            ':qty' => $qty,
            ':harga' => $harga,
            ':sub' => $line
        ]);

        $stmtStock->execute([':qty' => $qty, ':pid' => $pid]);
    }

    // hapus cart jika diambil dari carts
    $db->prepare("DELETE FROM carts WHERE user_id = ?")->execute([$user_id]);

    $db->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'total' => $total_harga,
        'redirect_url' => 'payment_instruction.php?order_id=' . $order_id
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
