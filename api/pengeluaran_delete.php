<?php
// backend/api/pengeluaran_delete.php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$db = get_db();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Gunakan POST');

    $id = intval($_POST['id_pengeluaran'] ?? 0);

    if ($id <= 0) throw new Exception('ID tidak valid');

    // cek apakah data ada
    $stmt = $db->prepare("SELECT id_pengeluaran FROM pengeluaran WHERE id_pengeluaran = :id");
    $stmt->execute([':id' => $id]);
    if (!$stmt->fetch()) throw new Exception('Data tidak ditemukan');

    $stmt = $db->prepare("DELETE FROM pengeluaran WHERE id_pengeluaran = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Pengeluaran berhasil dihapus'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
