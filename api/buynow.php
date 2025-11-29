<?php
session_start();
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request.");
    }

    // WAJIB JSON
    $body = json_decode(file_get_contents("php://input"), true);
    if (!$body) throw new Exception("Invalid JSON body.");

    $product_id = intval($body['product_id'] ?? 0);
    $qty        = intval($body['quantity'] ?? 1);

    if ($product_id <= 0 || $qty <= 0) {
        throw new Exception("Invalid product ID or quantity.");
    }

    if (empty($_SESSION['user']['id'])) {
        throw new Exception("Harus login dahulu");
    }

    $db = get_db();

    $stmt = $db->prepare("SELECT id, nama, harga, stok, foto FROM produk WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) throw new Exception("Product not found.");
    if ($product['stok'] < $qty) throw new Exception("Stok tidak cukup.");

    // SIMPAN SESSION BUY NOW
    $_SESSION['buy_now'] = [
        "product_id" => $product['id'],
        "qty"        => $qty
    ];

    echo json_encode([
        "success" => true,
        "message" => "Buy Now berhasil.",
        "redirect" => "/ecommerce/checkout.php?mode=buynow"
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
    exit;
}
?>
