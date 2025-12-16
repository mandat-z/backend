<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');


$data = json_decode(file_get_contents("php://input"), true);

$order_id = intval($data['order_id'] ?? 0);
$kurir    = trim($data['kurir'] ?? '');
$resi     = trim($data['no_resi'] ?? '');

if (!$order_id || !$kurir) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$db = get_db();

// cegah double shipping
$cek = $db->prepare("SELECT id FROM order_shipping WHERE order_id=?");
$cek->execute([$order_id]);
if ($cek->fetch()) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Pengiriman sudah ada']);
    exit;
}

$db->prepare("
    INSERT INTO order_shipping (order_id, kurir, no_resi, status_pengiriman)
    VALUES (?, ?, ?, 'Diproses')
")->execute([$order_id, $kurir, $resi ?: null]);

$db->prepare("UPDATE orders SET status='dikirim' WHERE id=?")
    ->execute([$order_id]);

echo json_encode(['success' => true]);
