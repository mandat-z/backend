<?php
header("Access-Control-Allow-Origin: http://localhost/ecommerce");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
$db = get_db();

// cek login
if (!isset($_SESSION['user']['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Silakan login terlebih dahulu'
    ]);
    exit;
}

$user_id = $_SESSION['user']['id'];
$product_id = $_POST['id'] ?? null;

if (!$product_id) {
    echo json_encode(['status' => 'error', 'message' => 'ID produk tidak ditemukan']);
    exit;
}

// cek apakah sudah ada di wishlist
$cek = $db->prepare("SELECT id FROM tb_wishlist WHERE user_id = ? AND product_id = ?");
$cek->execute([$user_id, $product_id]);
$already = $cek->fetch();

if ($already) {
    // Hapus
    $hapus = $db->prepare("DELETE FROM tb_wishlist WHERE user_id = ? AND product_id = ?");
    $hapus->execute([$user_id, $product_id]);

    echo json_encode(['status' => 'removed']);
} else {
    // Tambah
    $tambah = $db->prepare("INSERT INTO tb_wishlist (user_id, product_id) VALUES (?, ?)");
    $tambah->execute([$user_id, $product_id]);

    echo json_encode(['status' => 'added']);
}
