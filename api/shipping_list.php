<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');


$db = get_db();

$sql = "
    SELECT 
        o.id AS order_id,
        o.order_code,
        c.nama_kota,
        os.id AS shipping_id,
        os.kurir,
        os.no_resi,
        COALESCE(os.status_pengiriman, 'Belum Dikirim') AS status_pengiriman
    FROM orders o
    JOIN user_addresses ua ON o.address_id = ua.id
    JOIN cities c ON ua.kota_id = c.id
    LEFT JOIN order_shipping os ON os.order_id = o.id
    WHERE o.status IN ('dikemas','dikirim','selesai')
    ORDER BY o.tanggal_pesan DESC
";

$data = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => $data
]);
