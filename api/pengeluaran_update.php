<?php
// backend/api/pengeluaran_update.php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$db = get_db();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Gunakan POST');

    $id        = intval($_POST['id_pengeluaran'] ?? 0);
    $tanggal   = trim($_POST['tanggal'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $nominal   = $_POST['nominal'] ?? '';

    if ($id <= 0) throw new Exception('ID tidak valid');
    if ($tanggal === '' || $kategori === '' || $nominal === '') {
        throw new Exception('Tanggal, kategori, dan nominal wajib diisi.');
    }

    $nominal = str_replace(',', '', $nominal);
    $nominal = floatval($nominal);

    if ($nominal <= 0) {
        throw new Exception('Nominal harus lebih dari 0');
    }

    // cek apakah data ada
    $stmt = $db->prepare("SELECT id_pengeluaran FROM pengeluaran WHERE id_pengeluaran = :id");
    $stmt->execute([':id' => $id]);
    if (!$stmt->fetch()) throw new Exception('Data tidak ditemukan');

    $stmt = $db->prepare("UPDATE pengeluaran 
                         SET tanggal = :tanggal, kategori = :kategori, 
                             deskripsi = :deskripsi, nominal = :nominal, updated_at = NOW()
                         WHERE id_pengeluaran = :id");
    $stmt->execute([
        ':id' => $id,
        ':tanggal' => $tanggal,
        ':kategori' => $kategori,
        ':deskripsi' => $deskripsi,
        ':nominal' => $nominal
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Pengeluaran berhasil diperbarui'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
