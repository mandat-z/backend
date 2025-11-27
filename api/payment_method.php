<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . '/../config/config.php';
$db = get_db();

try {
    $stmt = $db->query("
        SELECT 
            id_metode,
            nama_metode,
            jenis,
            tujuan,
            keterangan,
            qr_image,
            status
        FROM metode_pembayaran
        WHERE status = 'aktif'
        ORDER BY nama_metode ASC
    ");

    $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $methods
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Gagal mengambil data metode pembayaran",
        "error" => $e->getMessage()
    ]);
}
