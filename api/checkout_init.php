<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../config/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$db = get_db();

// Ambil user
$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Harus login']);
    exit;
}

/* --------------------------------------------------------------------------
    1. Ambil semua alamat user
-------------------------------------------------------------------------- */
$addrStmt = $db->prepare("
    SELECT id, nama_penerima, phone, jalan, rt_rw, kelurahan, kecamatan, kota_id, provinsi, kode_pos, is_default
    FROM user_addresses 
    WHERE user_id = ? 
    ORDER BY is_default DESC, id DESC
");
$addrStmt->execute([$userId]);
$addresses = $addrStmt->fetchAll(PDO::FETCH_ASSOC);

/* Ambil alamat default */
$defaultAddress = null;
foreach ($addresses as $a) {
    if ($a['is_default'] == 1) {
        $defaultAddress = $a;
        break;
    }
}
if (!$defaultAddress && count($addresses) > 0) {
    $defaultAddress = $addresses[0];
}

/* --------------------------------------------------------------------------
    2. Hitung Cart Summary
-------------------------------------------------------------------------- */
$cartStmt = $db->prepare("
    SELECT c.id, c.quantity, p.nama, p.harga
    FROM carts c 
    JOIN produk p ON p.id = c.product_id
    WHERE c.user_id = ?
");
$cartStmt->execute([$userId]);
$items = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0;
foreach ($items as &$it) {
    $price = $it['harga'];
    $it['line_subtotal'] = $price * $it['quantity'];
    $subtotal += $it['line_subtotal'];
}

/* --------------------------------------------------------------------------
    3. Hitung Ongkir
-------------------------------------------------------------------------- */
$shipping = 0;

if ($defaultAddress) {
    $city = $db->prepare("SELECT ongkir FROM cities WHERE id = ?");
    $city->execute([$defaultAddress['kota_id']]);
    $shipping = floatval($city->fetchColumn() ?? 0);
}

/* --------------------------------------------------------------------------
    4. Hitung Voucher (ambil dari SESSION)
-------------------------------------------------------------------------- */
$voucherCode = $_SESSION['voucher_code'] ?? "";
$discount = 0;

if ($voucherCode) {
    $stmt = $db->prepare("SELECT * FROM tb_voucher WHERE kode_voucher = :kode AND status = 'Aktif' LIMIT 1");
    $stmt->execute([':kode' => $voucherCode]);
    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($voucher && $subtotal >= floatval($voucher['minimal_belanja'])) {

        if ($voucher['tipe_diskon'] === 'persen') {

            $discount = round($subtotal * floatval($voucher['diskon']) / 100);

            // batas maksimal
            if (floatval($voucher['maksimal_diskon']) > 0 &&
                $discount > floatval($voucher['maksimal_diskon'])) {
                $discount = floatval($voucher['maksimal_diskon']);
            }

        } else { 
            $discount = floatval($voucher['diskon']);
        }
    }
}

/* --------------------------------------------------------------------------
    5. Metode Pembayaran
-------------------------------------------------------------------------- */
$paymentMethods = [];
$stmt = $db->query("SELECT * FROM metode_pembayaran WHERE status='Aktif'");
$paymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* --------------------------------------------------------------------------
    6. Total Akhir
-------------------------------------------------------------------------- */
$total = $subtotal + $shipping - $discount;

/* --------------------------------------------------------------------------
    7. Response JSON
-------------------------------------------------------------------------- */
echo json_encode([
    "success" => true,
    "addresses" => $addresses,
    "default_address" => $defaultAddress,
    "payment_methods" => $paymentMethods,
    "summary" => [
        "items" => $items,
        "subtotal" => $subtotal,
        "shipping" => $shipping,
        "discount" => $discount,
        "total" => $total
    ]
]);
