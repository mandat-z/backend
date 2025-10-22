<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
    exit;
}

// Ambil koneksi PDO
$db = get_db();

$judul  = $_POST['judul'] ?? '';
$subjudul = $_POST['subjudul'] ?? '';
$status = $_POST['status'] ?? 'Nonaktif';
$gambar = null;

// Validasi
if (trim($judul) === '') {
    echo json_encode(['status' => 'error', 'message' => 'Judul tidak boleh kosong']);
    exit;
}

// Pastikan folder upload ada
$uploadDir = __DIR__ . '/../assets/images/banner/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Upload gambar jika ada
if (!empty($_FILES['gambar']['name'])) {
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed)) {
        echo json_encode(['status' => 'error', 'message' => 'Format file tidak valid. Gunakan JPG/PNG/WEBP']);
        exit;
    }

    $filename = uniqid('banner_') . '.' . $ext;
    $target = $uploadDir . $filename;

    if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah gambar']);
        exit;
    }

    $gambar = $filename;
} else {
    echo json_encode(['status' => 'error', 'message' => 'File gambar belum dipilih']);
    exit;
}

// Simpan ke database
try {
    $stmt = $db->prepare("INSERT INTO tb_banner (judul, subjudul, gambar, status, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$judul, $subjudul, $gambar, $status]);

    echo json_encode(['status' => 'success', 'message' => 'Banner berhasil ditambahkan']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>