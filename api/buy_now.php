<?php
session_start();
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request.");
    }

    $product_id = intval($_POST['product_id'] ?? 0);
    $qty = intval($_POST['qty'] ?? 1);

    if ($product_id <= 0 || $qty <= 0) {
        throw new Exception("Invalid product or qty.");
    }

    $db = get_db();

    // ambil data produk
    $stmt = $db->prepare("SELECT id, nama_produk, harga, stok, foto FROM products WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception("Product not found.");
    }

    if ($product['stok'] < $qty) {
        throw new Exception("Stok tidak mencukupi.");
    }

    // buat session buy_now
    $_SESSION['buy_now'] = [
        'product_id' => $product['id'],
        'name'       => $product['nama_produk'],
        'price'      => $product['harga'],
        'qty'        => $qty,
        'foto'       => $product['foto'],
        'subtotal'   => $product['harga'] * $qty
    ];

    echo json_encode([
        "success" => true,
        "message" => "Buy Now created.",
        "redirect" => "/checkout_buy_now.php"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
