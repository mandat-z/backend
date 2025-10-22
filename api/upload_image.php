<?php
// backend/api/upload_image.php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$db = get_db();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Use POST');

    if (empty($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File not uploaded');
    }

    $allowed = ['jpg','jpeg','png','webp','gif'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $orig = $_FILES['gambar']['name'];
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) throw new Exception('Tipe file tidak diizinkan');

    $uploadDir = __DIR__ . '/../assets/images/products/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $newName = uniqid('img_', true) . '.' . $ext;
    $target = $uploadDir . $newName;

    if (!move_uploaded_file($tmp, $target)) throw new Exception('Gagal memindahkan file');

    $url = rtrim(BACKEND_URL, '/') . '/assets/images/products/' . $newName;

    echo json_encode(['success' => true, 'url' => $url]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
