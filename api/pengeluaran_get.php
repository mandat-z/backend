<?php
// backend/api/pengeluaran_get.php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$db = get_db();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') throw new Exception('Gunakan GET');

    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) throw new Exception('ID tidak valid');

    $stmt = $db->prepare("SELECT id_pengeluaran, tanggal, kategori, deskripsi, nominal, created_at, updated_at 
                          FROM pengeluaran WHERE id_pengeluaran = :id");
    $stmt->execute([':id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) throw new Exception('Data tidak ditemukan');

    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
