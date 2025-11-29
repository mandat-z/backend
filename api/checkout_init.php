<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../config/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$db = get_db();

/* ============================================================================
    1. VALIDASI LOGIN
============================================================================ */
$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Harus login']);
    exit;
}

/* ============================================================================
    2. CEK MODE BUY NOW vs NORMAL CART
============================================================================ */
$isBuyNow = isset($_SESSION['buy_now']) && !empty($_SESSION['buy_now']);

/* ============================================================================
    3. AMBIL SEMUA ALAMAT USER
============================================================================ */
$addrStmt = $db->prepare("
    SELECT id, nama_penerima, phone, jalan, rt_rw, kelurahan,
           kecamatan, kota_id, provinsi, kode_pos, is_default
    FROM user_addresses
    WHERE user_id = ?
    ORDER BY is_default DESC, id DESC
");
$addrStmt->execute([$userId]);
$addresses = $addrStmt->fetchAll(PDO::FETCH_ASSOC);

/* Ambil alamat default */
$defaultAddress = null;

foreach ($addresses as $addr) {
    if ($addr['is_default'] == 1) {
        $defaultAddress = $addr;
        break;
    }
}

if (!$defaultAddress && count($addresses) > 0) {
    $defaultAddress = $addresses[0];
}

/* ============================================================================
    4. AMBIL ITEMS (BUY NOW ATAU CART)
============================================================================ */
$items = [];
$subtotal = 0;

if ($isBuyNow) {

    /* ---------------- BUY NOW MODE ---------------- */
    $bn = $_SESSION['buy_now'];

    $p = $db->prepare("SELECT id, nama, harga, foto FROM produk WHERE id = ?");
    $p->execute([$bn['product_id']]);
    $product = $p->fetch(PDO::FETCH_ASSOC);

    if ($product) {

        $lineSubtotal = $product['harga'] * $bn['qty'];

        $items[] = [
            "product_id"    => $product['id'],
            "nama"          => $product['nama'],
            "harga"         => $product['harga'],
            "quantity"      => $bn['qty'],
            "foto"          => $product['foto'],
            "line_subtotal" => $lineSubtotal
        ];

        $subtotal = $lineSubtotal;
    }

} else {

    /* ---------------- NORMAL CART MODE ---------------- */
    $cartStmt = $db->prepare("
        SELECT c.id, c.quantity,
               p.id AS product_id, p.nama, p.harga, p.foto
        FROM carts c
        JOIN produk p ON p.id = c.product_id
        WHERE c.user_id = ?
    ");
    $cartStmt->execute([$userId]);
    $items = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as &$it) {
        $it['line_subtotal'] = $it['harga'] * $it['quantity'];
        $subtotal += $it['line_subtotal'];
    }
}

/* ============================================================================
    5. HITUNG ONGKIR
============================================================================ */
$shipping = 0;

if ($defaultAddress) {
    $c = $db->prepare("SELECT ongkir FROM cities WHERE id = ?");
    $c->execute([$defaultAddress['kota_id']]);
    $shipping = floatval($c->fetchColumn() ?? 0);
}

/* ============================================================================
    6. HITUNG VOUCHER (BuyNow tidak bisa voucher)
============================================================================ */
$voucherCode = (!$isBuyNow) ? ($_SESSION['voucher_code'] ?? "") : "";
$discount = 0;

if ($voucherCode && !$isBuyNow) {

    $v = $db->prepare("
        SELECT * FROM tb_voucher
        WHERE kode_voucher = :kode AND status = 'Aktif'
        LIMIT 1
    ");
    $v->execute([':kode' => $voucherCode]);
    $voucher = $v->fetch(PDO::FETCH_ASSOC);

    if ($voucher && $subtotal >= floatval($voucher['minimal_belanja'])) {

        if ($voucher['tipe_diskon'] === 'persen') {

            $discount = round($subtotal * floatval($voucher['diskon']) / 100);

            /* batas maksimal diskon */
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
    7. METODE PEMBAYARAN
============================================================================ */
$pm = $db->query("SELECT * FROM metode_pembayaran WHERE status = 'Aktif'");
$paymentMethods = $pm->fetchAll(PDO::FETCH_ASSOC);

/* ============================================================================
    8. HITUNG TOTAL FINAL
============================================================================ */
$total = $subtotal + $shipping - $discount;

/* ============================================================================
    9. OUTPUT JSON
============================================================================ */
echo json_encode([
    "success"          => true,
    "mode"             => $isBuyNow ? "buynow" : "cart",
    "addresses"        => $addresses,
    "default_address"  => $defaultAddress,
    "payment_methods"  => $paymentMethods,
    "summary" => [
        "items"     => $items,
        "subtotal"  => $subtotal,
        "shipping"  => $shipping,
        "discount"  => $discount,
        "total"     => $total
    ]
]);
