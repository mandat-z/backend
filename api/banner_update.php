<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

// ambil koneksi PDO dari config
$db = get_db();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
    exit;
}

$id = $_POST['id'] ?? null;
$judul = $_POST['judul'] ?? '';
$subjudul = $_POST['subjudul'] ?? '';
$status = $_POST['status'] ?? 'Nonaktif';

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
    exit;
}

// Ambil data lama
$stmt = $db->prepare("SELECT gambar FROM tb_banner WHERE id = ?");
$stmt->execute([$id]);
$oldData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$oldData) {
    echo json_encode(['status' => 'error', 'message' => 'Data banner tidak ditemukan']);
    exit;
}

$gambar = $oldData['gambar'];
$uploadDir = __DIR__ . '/../assets/images/banner/';

// Jika ada file baru, upload dan hapus lama
if (isset($_FILES['gambar']) && !empty($_FILES['gambar']['name'])) {
    $filename = uniqid() . '_' . basename($_FILES['gambar']['name']);
    $target = $uploadDir . $filename;

    // Pastikan folder ada
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        // hapus gambar lama jika ada
        $oldFile = $uploadDir . $gambar;
        if (!empty($gambar) && file_exists($oldFile)) {
            unlink($oldFile);
        }
        $gambar = $filename;
    }
}

try {
    $stmt = $db->prepare("UPDATE tb_banner SET judul = ?, subjudul = ?, gambar = ?, status = ? WHERE id = ?");
    $stmt->execute([$judul, $subjudul, $gambar, $status, $id]);

    echo json_encode(['status' => 'success', 'message' => 'Banner berhasil diperbarui']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
