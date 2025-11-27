<?php


require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = get_db();

// ===============================================================
// Helper
// ===============================================================
function jsonRes($arr, $code = 200)
{
    http_response_code($code);
    echo json_encode($arr);
    exit;
}

// ===============================================================
// Validasi user login
// ===============================================================
$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) jsonRes(['success' => false, 'message' => 'Harus login'], 401);

// ===============================================================
// Ambil body request (JSON)
// ===============================================================
$body = json_decode(file_get_contents("php://input"), true);

$addressId = $body['address_id'] ?? null;
$paymentMethodId = $body['payment_method'] ?? null; // harus ID metode_pembayaran
$voucherCode = trim($body['voucher'] ?? "");

// ===============================================================
// Cek jika address tidak dikirim → fallback default
// ===============================================================
if (!$addressId) {
    $stmt = $db->prepare("
        SELECT id 
        FROM user_addresses
        WHERE user_id = ?
        ORDER BY is_default DESC, id DESC
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $defaultAddress = $stmt->fetch(PDO::FETCH_ASSOC);

    $addressId = $defaultAddress['id'] ?? null;
}

if (!$addressId) jsonRes(['success' => false, 'message' => 'Alamat tidak ditemukan'], 400);
if (!$paymentMethodId) jsonRes(['success' => false, 'message' => 'Metode pembayaran wajib dipilih'], 400);

try {
    $db->beginTransaction();

    // ===============================================================
    // Ambil data alamat (kota untuk ongkir)
    // ===============================================================
    $stmt = $db->prepare("
        SELECT id, kota_id 
        FROM user_addresses
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$addressId, $userId]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$address) jsonRes(['success' => false, 'message' => 'Alamat tidak valid'], 400);

    // ===============================================================
    // Ambil cart user
    // ===============================================================
    $stmt = $db->prepare("
        SELECT c.id as cart_id, c.product_id, c.quantity,
               p.nama, p.harga, p.stok
        FROM carts c
        JOIN produk p ON p.id = c.product_id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$items) jsonRes(['success' => false, 'message' => 'Keranjang kosong'], 400);

    // ===============================================================
    // Hitung subtotal
    // ===============================================================
    $subtotal = 0;

    foreach ($items as $item) {
        if ($item['quantity'] > $item['stok']) {
            jsonRes([
                'success' => false,
                'message' => "Stok produk '{$item['nama']}' tidak cukup"
            ], 400);
        }

        $price = $item['harga'];
        $subtotal += $price * $item['quantity'];
    }

    // ===============================================================
    // Ambil ongkir berdasarkan kota
    // ===============================================================
    $stmt = $db->prepare("SELECT ongkir FROM cities WHERE id = ?");
    $stmt->execute([$address['kota_id']]);
    $ongkir = floatval($stmt->fetchColumn() ?? 0);

    // ===============================================================
    // Hitung potongan voucher
    // ===============================================================
    $discount = 0;

    if ($voucherCode !== "") {
        $v = $db->prepare("
            SELECT * FROM tb_voucher
            WHERE kode_voucher = ? AND status = 'Aktif'
            LIMIT 1
        ");
        $v->execute([$voucherCode]);
        $voucher = $v->fetch(PDO::FETCH_ASSOC);

        if ($voucher && $subtotal >= floatval($voucher['minimal_belanja'])) {

            if ($voucher['tipe_diskon'] === 'persen') {
                $discount = round($subtotal * floatval($voucher['diskon']) / 100);

                // maksimal diskon
                if (
                    floatval($voucher['maksimal_diskon']) > 0 &&
                    $discount > floatval($voucher['maksimal_diskon'])
                ) {
                    $discount = floatval($voucher['maksimal_diskon']);
                }

            } else {
                $discount = floatval($voucher['diskon']);
            }
        }
    }

    // ===============================================================
    // Hitung total akhir
    // ===============================================================
    $total = $subtotal + $ongkir - $discount;

    // ===============================================================
    // Generate kode order invoice
    // ===============================================================
    $invoice = "INV-" . date("YmdHis") . "-" . rand(100, 999);

    // ===============================================================
    // INSERT orders (Sesuai table kamu)
    // ===============================================================
    $stmt = $db->prepare("
        INSERT INTO orders (
            user_id,
            address_id,
            metode_pembayaran_id,
            subtotal,
            ongkir,
            potongan_voucher,
            kode_voucher,
            total_harga,
            status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    $stmt->execute([
        $userId,
        $addressId,
        $paymentMethodId,
        $subtotal,
        $ongkir,
        $discount,
        $voucherCode,
        $total
    ]);

    $orderId = $db->lastInsertId();

    // ===============================================================
    // Insert order_items dan update stok
    // ===============================================================
    foreach ($items as $item) {
        $price = $item['harga'];

        // simpan ke order_items
        $oi = $db->prepare("
            INSERT INTO order_items (order_id, produk_id, qty, harga_satuan, subtotal)
            VALUES (?, ?, ?, ?, ?)
        ");
        $oi->execute([
            $orderId,
            $item['product_id'],
            $item['quantity'],
            $price,
            $price * $item['quantity']
        ]);

        // kurangi stok
        $upd = $db->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?");
        $upd->execute([$item['quantity'], $item['product_id']]);
    }

    // ===============================================================
    // Hapus cart user
    // ===============================================================
    $del = $db->prepare("DELETE FROM carts WHERE user_id = ?");
    $del->execute([$userId]);

    // Commit transaksi
    $db->commit();

    jsonRes([
        "success" => true,
        "message" => "Checkout berhasil",
        "order_id" => $orderId,
        "invoice" => $invoice,
        "total" => $total
    ]);

} catch (Exception $e) {
    $db->rollBack();
    jsonRes([
        "success" => false,
        "message" => $e->getMessage(),
        "trace" => $e->getTraceAsString()
    ], 500);
}


?>
