<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$orderId = intval($data['order_id'] ?? 0);
$kurir   = trim($data['kurir'] ?? '');
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

/* Periksa apakah record shipping untuk order sudah ada */
$cek = $db->prepare("SELECT id FROM order_shipping WHERE order_id = ? LIMIT 1");
$cek->execute([$orderId]);
$exists = $cek->fetchColumn();

if ($exists) {
    $upd = $db->prepare("UPDATE order_shipping SET kurir = ?, no_resi = ?, status_pengiriman = ?, updated_at = NOW() WHERE order_id = ?");
    $upd->execute([$kurir ?: null, $resi ?: null, $status, $orderId]);
} else {
    $ins = $db->prepare("INSERT INTO order_shipping (order_id, kurir, no_resi, status_pengiriman, updated_at) VALUES (?, ?, ?, ?, NOW())");
    $ins->execute([$orderId, $kurir ?: null, $resi ?: null, $status]);
}

/* SINKRON STATUS ORDER */
if (in_array($status, ['Terkirim', 'Dikirim'])) {
    $db->prepare("
        UPDATE orders 
        SET status = 'selesai'
        WHERE id = ?
    ")->execute([$orderId]);
}

echo json_encode(['success' => true]);
