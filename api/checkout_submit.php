<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = get_db();

/* ============================================================================
 *  HELPER RESPONSE
 * ==========================================================================*/
function jsonRes(array $arr, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($arr);
    exit;
}

/* ============================================================================
 *  VALIDASI LOGIN USER
 * ==========================================================================*/
$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    jsonRes(['success' => false, 'message' => 'Harus login'], 401);
}

/* ============================================================================
 *  AMBIL BODY REQUEST
 * ==========================================================================*/
$body = json_decode(file_get_contents("php://input"), true);

$addressId       = $body['address_id']      ?? null;
$paymentMethodId = $body['payment_method']  ?? null;
$voucherCode     = trim($body['voucher']    ?? "");

/* ============================================================================
 *  CEK MODE BUY NOW
 * ==========================================================================*/
$isBuyNow = !empty($_SESSION['buy_now']);

if ($isBuyNow) {
    $voucherCode = "";
}

/* ============================================================================
 *  GENERATOR ORDER CODE
 * ==========================================================================*/
function generateOrderCode($db)
{
    $today = date("Ymd");

    $q = $db->prepare("
        SELECT COUNT(*) AS total
        FROM orders
        WHERE DATE(tanggal_pesan) = CURDATE()
    ");
    $q->execute();
    $row = $q->fetch(PDO::FETCH_ASSOC);

    $urutan = str_pad($row["total"] + 1, 4, "0", STR_PAD_LEFT);

    return "ORD{$today}-{$urutan}";
}

/* ============================================================================
 *  PROSES CHECKOUT
 * ==========================================================================*/
try {
    $db->beginTransaction();

    /* -------- Validasi alamat -------- */
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

    if (!$addressId) {
        jsonRes(['success' => false, 'message' => 'Alamat tidak ditemukan'], 400);
    }

    if (!$paymentMethodId) {
        jsonRes(['success' => false, 'message' => 'Metode pembayaran wajib dipilih'], 400);
    }

    $stmt = $db->prepare("
        SELECT id, kota_id
        FROM user_addresses
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$addressId, $userId]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$address) {
        jsonRes(['success' => false, 'message' => 'Alamat tidak valid'], 400);
    }

    /* ============================================================================
     *  AMBIL ITEM
     * ==========================================================================*/
    $items = [];

    if ($isBuyNow) {
        $bn        = $_SESSION['buy_now'];
        $productId = intval($bn['product_id'] ?? 0);
        $qty       = intval($bn['qty'] ?? ($bn['quantity'] ?? 1));

        if ($productId <= 0 || $qty <= 0) {
            jsonRes(['success' => false, 'message' => 'Data Buy Now tidak valid'], 400);
        }

        $stmt = $db->prepare("SELECT id, nama, harga, stok FROM produk WHERE id = ?");
        $stmt->execute([$productId]);
        $produk = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produk) {
            jsonRes(['success' => false, 'message' => 'Produk Buy Now tidak ditemukan'], 400);
        }

        if ($qty > $produk['stok']) {
            jsonRes(['success' => false, 'message' => "Stok produk '{$produk['nama']}' tidak cukup"], 400);
        }

        $items[] = [
            "product_id" => $produk["id"],
            "nama"       => $produk["nama"],
            "harga"      => $produk["harga"],
            "stok"       => $produk["stok"],
            "quantity"   => $qty
        ];

    } else {

        $stmt = $db->prepare("
            SELECT 
                c.id AS cart_id,
                c.product_id,
                c.quantity,
                p.nama,
                p.harga,
                p.stok
            FROM carts c
            JOIN produk p ON p.id = c.product_id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$items) {
            jsonRes(['success' => false, 'message' => 'Keranjang kosong'], 400);
        }
    }

    /* ============================================================================
     *   HITUNG SUBTOTAL + CEK STOK
     * ==========================================================================*/
    $subtotal = 0;

    foreach ($items as $i) {
        if ($i['quantity'] > $i['stok']) {
            jsonRes(['success' => false, 'message' => "Stok produk '{$i['nama']}' tidak cukup"], 400);
        }

        $subtotal += $i['harga'] * $i['quantity'];
    }

    /* ============================================================================
     *   AMBIL ONGKIR
     * ==========================================================================*/
    $stmt = $db->prepare("SELECT ongkir FROM cities WHERE id = ?");
    $stmt->execute([$address['kota_id']]);
    $ongkir = floatval($stmt->fetchColumn() ?? 0);

    /* ============================================================================
     *   HITUNG DISKON VOUCHER
     * ==========================================================================*/
    $discount = 0;

    if (!$isBuyNow && $voucherCode !== "") {

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

    /* ============================================================================
     *   TOTAL AKHIR
     * ==========================================================================*/
    $total = $subtotal + $ongkir - $discount;

    /* ============================================================================
     *   GENERATE ORDER CODE
     * ==========================================================================*/
    $orderCode = generateOrderCode($db);

    /* ============================================================================
     *   INSERT ORDERS
     * ==========================================================================*/
    $stmt = $db->prepare("
        INSERT INTO orders (
            order_code,
            user_id,
            address_id,
            metode_pembayaran_id,
            subtotal,
            ongkir,
            potongan_voucher,
            kode_voucher,
            total_harga,
            status,
            tanggal_pesan
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");

    $stmt->execute([
        $orderCode,
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

    /* ============================================================================
     *   INSERT ORDER ITEMS + KURANGI STOK
     * ==========================================================================*/
    foreach ($items as $i) {

        $oi = $db->prepare("
            INSERT INTO order_items (
                order_id,
                produk_id,
                qty,
                harga_satuan,
                subtotal
            )
            VALUES (?, ?, ?, ?, ?)
        ");
        $oi->execute([
            $orderId,
            $i['product_id'],
            $i['quantity'],
            $i['harga'],
            $i['harga'] * $i['quantity']
        ]);

        $upd = $db->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?");
        $upd->execute([$i['quantity'], $i['product_id']]);
    }

    /* ============================================================================
     *   CLEAR CART / BUY NOW
     * ==========================================================================*/
    if ($isBuyNow) {
        unset($_SESSION['buy_now']);
    } else {
        $del = $db->prepare("DELETE FROM carts WHERE user_id = ?");
        $del->execute([$userId]);
    }

    $db->commit();

    jsonRes([
        "success"     => true,
        "message"     => "Checkout berhasil",
        "order_id"    => $orderId,
        "order_code"  => $orderCode,
        "total"       => $total,
        "mode"        => $isBuyNow ? 'buynow' : 'cart'
    ]);

} catch (Exception $e) {
    $db->rollBack();
    jsonRes([
        "success" => false,
        "message" => $e->getMessage()
    ], 500);
}
