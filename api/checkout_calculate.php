<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../config/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$db = get_db();

/* --------------------------------------------------------------------------
    VALIDASI LOGIN
-------------------------------------------------------------------------- */
$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Harus login']);
    exit;
}

/* --------------------------------------------------------------------------
    CHECK MODE BUY NOW
-------------------------------------------------------------------------- */
$isBuyNow = !empty($_SESSION['buy_now']);

/* --------------------------------------------------------------------------
    READ BODY
-------------------------------------------------------------------------- */
$body = json_decode(file_get_contents("php://input"), true);

$address_id  = $body['address_id'] ?? null;
$voucherCode = trim($body['voucher'] ?? "");

/* Jika voucher kosong → hapus session */
if ($voucherCode === "") unset($_SESSION['voucher_code']);

/* --------------------------------------------------------------------------
    AMBIL ALAMAT
-------------------------------------------------------------------------- */

$address = null;

if ($address_id) {
    $stmt = $db->prepare("
        SELECT * FROM user_addresses 
        WHERE id = ? AND user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$address_id, $userId]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$address) {
    $stmt = $db->prepare("
        SELECT * FROM user_addresses
        WHERE user_id = ?
        ORDER BY is_default DESC, id DESC
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* --------------------------------------------------------------------------
    AMBIL ITEM → BUY NOW / CART
-------------------------------------------------------------------------- */
$items = [];
$subtotal = 0;

if ($isBuyNow) {

    /* ---------------------------------------
        MODE BUY NOW
    ----------------------------------------*/
    $bn = $_SESSION['buy_now'];
    $productId = intval($bn['product_id']);
    $qty = intval($bn['qty']);

    $stmt = $db->prepare("SELECT id, nama, harga, stok FROM produk WHERE id = ?");
    $stmt->execute([$productId]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($p) {
        $line = $p['harga'] * $qty;

        $items[] = [
            "product_id" => $p['id'],
            "nama"       => $p['nama'],
            "harga"      => $p['harga'],
            "quantity"   => $qty,
            "line_subtotal" => $line
        ];

        $subtotal = $line;
    }

} else {

    /* ---------------------------------------
        MODE CART NORMAL
    ----------------------------------------*/
    $stmt = $db->prepare("
        SELECT c.quantity, p.id AS product_id, p.nama, p.harga
        FROM carts c
        JOIN produk p ON p.id = c.product_id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as &$it) {
        $it['line_subtotal'] = $it['harga'] * $it['quantity'];
        $subtotal += $it['line_subtotal'];
    }
}

/* --------------------------------------------------------------------------
    SHIPPING
-------------------------------------------------------------------------- */
$shipping = 0;

if ($address) {
    $stmt = $db->prepare("SELECT ongkir FROM cities WHERE id = ?");
    $stmt->execute([$address['kota_id']]);
    $shipping = floatval($stmt->fetchColumn() ?? 0);
}

/* --------------------------------------------------------------------------
    DISCOUNT (CART ONLY)
-------------------------------------------------------------------------- */
$discount = 0;

if (!$isBuyNow && $voucherCode !== "") {

    $stmt = $db->prepare("
        SELECT * FROM tb_voucher 
        WHERE kode_voucher = :kode AND status = 'Aktif'
        LIMIT 1
    ");
    $stmt->execute([':kode' => $voucherCode]);
    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($voucher && $subtotal >= floatval($voucher['minimal_belanja'])) {

        $_SESSION['voucher_code'] = $voucherCode;

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

/* --------------------------------------------------------------------------
    TOTAL
-------------------------------------------------------------------------- */
$total = $subtotal + $shipping - $discount;

/* --------------------------------------------------------------------------
    RESPONSE
-------------------------------------------------------------------------- */
echo json_encode([
    "success"  => true,
    "mode"     => $isBuyNow ? "buynow" : "cart",
    "items"    => $items,
    "subtotal" => $subtotal,
    "shipping" => $shipping,
    "discount" => $discount,
    "total"    => $total
]);
