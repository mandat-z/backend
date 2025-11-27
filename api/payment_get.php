<?php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

try {
    $db = get_db();
    $stmt = $db->query("SELECT * FROM metode_pembayaran ORDER BY id_metode DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        "success" => true,
        "data" => $data
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Gagal mengambil data: " . $e->getMessage()
    ]);
}
?>