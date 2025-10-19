<?php
include __DIR__ . '/../config/config.php';
$db = get_db();

session_start();
$user_id = $_SESSION['user_id'] ?? null;

header('Content-Type: application/json');

// cek login
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Login required']);
    exit;
}

$produk_id = $_POST['id'] ?? null;
if (!$produk_id) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID missing']);
    exit;
}

// cek apakah produk sudah ada di wishlist
$stmt = $db->prepare("SELECT * FROM tb_wishlist WHERE user_id = :user_id AND produk_id = :produk_id");
$stmt->execute([
    ':user_id' => $user_id,
    ':produk_id' => $produk_id
]);
$exists = $stmt->fetch();

if ($exists) {
    // hapus dari wishlist
    $stmt = $db->prepare("DELETE FROM tb_wishlist WHERE user_id = :user_id AND produk_id = :produk_id");
    $stmt->execute([':user_id'=>$user_id, ':produk_id'=>$produk_id]);
    echo json_encode(['status' => 'removed']);
} else {
    // tambah ke wishlist
    $stmt = $db->prepare("INSERT INTO tb_wishlist (user_id, produk_id) VALUES (:user_id, :produk_id)");
    $stmt->execute([':user_id'=>$user_id, ':produk_id'=>$produk_id]);
    echo json_encode(['status' => 'added']);
}
?>
