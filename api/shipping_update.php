<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$orderId = intval($data['order_id'] ?? 0);
$resi    = trim($data['no_resi'] ?? '');
$status  = $data['status_pengiriman'] ?? '';

if (!$orderId || !$status) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Order ID atau status tidak valid'
    ]);
    exit;
}

$db = get_db();

/* UPSERT SHIPPING */
$stmt = $db->prepare("
    INSERT INTO order_shipping (order_id, no_resi, status_pengiriman, updated_at)
    VALUES (?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE
        no_resi = VALUES(no_resi),
        status_pengiriman = VALUES(status_pengiriman),
        updated_at = NOW()
");
$stmt->execute([$orderId, $resi ?: null, $status]);

/* SINKRON STATUS ORDER */
if (in_array($status, ['Terkirim', 'Dikirim'])) {
    $db->prepare("
        UPDATE orders 
        SET status = 'selesai'
        WHERE id = ?
    ")->execute([$orderId]);
}

echo json_encode(['success' => true]);
