<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

// require admin
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    http_response_code(403); echo json_encode(['success'=>false,'message'=>'Forbidden']); exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit;
}

$raw = json_decode(file_get_contents('php://input'), true);
$order_id = intval($raw['order_id'] ?? 0);
$shipping_cost = floatval($raw['shipping_cost'] ?? 0);
$tracking_number = trim($raw['tracking_number'] ?? '');

if (!$order_id || $shipping_cost <= 0) {
    http_response_code(400); echo json_encode(['success'=>false,'message'=>'Missing required fields']); exit;
}

try {
    $db = get_db();

    // Check if order exists and is not already shipped
    $stmt = $db->prepare('SELECT id FROM orders WHERE id = :order_id');
    $stmt->execute([':order_id'=>$order_id]);
    if (!$stmt->fetch()) {
        http_response_code(404); echo json_encode(['success'=>false,'message'=>'Order not found']); exit;
    }

    // Check if shipping already exists for this order
    $stmt = $db->prepare('SELECT id FROM shippings WHERE order_id = :order_id');
    $stmt->execute([':order_id'=>$order_id]);
    if ($stmt->fetch()) {
        http_response_code(409); echo json_encode(['success'=>false,'message'=>'Shipping already exists for this order']); exit;
    }

    // Insert shipping
    $stmt = $db->prepare('INSERT INTO shippings (order_id, shipping_cost, tracking_number) VALUES (:order_id, :shipping_cost, :tracking_number)');
    $stmt->execute([
        ':order_id' => $order_id,
        ':shipping_cost' => $shipping_cost,
        ':tracking_number' => $tracking_number ?: null
    ]);
    $shipping_id = $db->lastInsertId();

    echo json_encode(['success'=>true, 'shipping_id'=>$shipping_id]);

} catch (Exception $e) {
    http_response_code(500); echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>
