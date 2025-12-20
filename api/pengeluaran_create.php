<?php
// backend/api/pengeluaran_create.php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$db = get_db();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Gunakan POST');

    $tanggal    = trim($_POST['tanggal'] ?? '');
    $kategori   = trim($_POST['kategori'] ?? '');
    $deskripsi  = trim($_POST['deskripsi'] ?? '');
    $nominal    = $_POST['nominal'] ?? '';

    if ($tanggal === '' || $kategori === '' || $nominal === '') {
        throw new Exception('Tanggal, kategori, dan nominal wajib diisi.');
    }

    $nominal = str_replace(',', '', $nominal); // hapus koma
    $nominal = floatval($nominal);

    if ($nominal <= 0) {
        throw new Exception('Nominal harus lebih dari 0');
    }

    $stmt = $db->prepare("INSERT INTO pengeluaran (tanggal, kategori, deskripsi, nominal, created_at)
                         VALUES (:tanggal, :kategori, :deskripsi, :nominal, NOW())");
    $stmt->execute([
        ':tanggal' => $tanggal,
        ':kategori' => $kategori,
        ':deskripsi' => $deskripsi,
        ':nominal' => $nominal
    ]);

    $id = $db->lastInsertId();

    echo json_encode([
        'status' => 'success',
        'message' => 'Pengeluaran berhasil ditambah',
        'id_pengeluaran' => $id
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
