<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../config/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$db = get_db();

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Harus login']);
    exit;
}

/* --------------------------------------------------------------------------
    READ JSON BODY
-------------------------------------------------------------------------- */
$body = json_decode(file_get_contents("php://input"), true);
$address_id = $body['address_id'] ?? null;
$voucherCode = trim($body['voucher'] ?? "");

/* Jika voucher kosong → hapus session */
if ($voucherCode === "") {
    unset($_SESSION['voucher_code']);
}

/* --------------------------------------------------------------------------
    VALIDATE ADDRESS
-------------------------------------------------------------------------- */
$address = null;

if ($address_id) {
    $stmt = $db->prepare("
        SELECT id, nama_penerima, phone, jalan, rt_rw, kelurahan, kecamatan, kota_id, provinsi, kode_pos, is_default
        FROM user_addresses 
        WHERE id = ? AND user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$address_id, $userId]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$address) {
    $stmt = $db->prepare("
        SELECT id, nama_penerima, phone, jalan, rt_rw, kelurahan, kecamatan, kota_id, provinsi, kode_pos, is_default
        FROM user_addresses 
        WHERE user_id = ?
        ORDER BY is_default DESC, id DESC
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* --------------------------------------------------------------------------
    CALCULATE SUBTOTAL
-------------------------------------------------------------------------- */
$cartStmt = $db->prepare("
    SELECT c.id, c.quantity, p.harga, p.nama
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
    SHIPPING
-------------------------------------------------------------------------- */
$shipping = 0;

if ($address) {
    $city = $db->prepare("SELECT ongkir FROM cities WHERE id = ?");
    $city->execute([$address['kota_id']]);
    $shipping = floatval($city->fetchColumn() ?? 0);
}

/* --------------------------------------------------------------------------
    VOUCHER DISCOUNT
-------------------------------------------------------------------------- */
$discount = 0;

if ($voucherCode) {

    $stmt = $db->prepare("
        SELECT * FROM tb_voucher 
        WHERE kode_voucher = :kode AND status = 'Aktif'
        LIMIT 1
    ");
    $stmt->execute([':kode' => $voucherCode]);
    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($voucher && $subtotal >= floatval($voucher['minimal_belanja'])) {

        // SIMPAN voucher ke session
        $_SESSION['voucher_code'] = $voucherCode;

        if ($voucher['tipe_diskon'] === 'persen') {
            $discount = round($subtotal * floatval($voucher['diskon']) / 100);

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
    TOTAL
-------------------------------------------------------------------------- */
$total = $subtotal + $shipping - $discount;

/* --------------------------------------------------------------------------
    RESPONSE
-------------------------------------------------------------------------- */
echo json_encode([
    "success" => true,
    "items" => $items,
    "subtotal" => $subtotal,
    "shipping" => $shipping,
    "discount" => $discount,
    "total" => $total
]);
