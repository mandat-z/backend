<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

// optional admin check
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    // Allow if needed, but for admin panel, require admin
    http_response_code(403); echo json_encode(['success'=>false,'message'=>'Forbidden']); exit;
}

try {
    $db = get_db();
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if ($id) {
        $stmt = $db->prepare('
            SELECT s.*, o.user_id, u.username, ua.city
            FROM shippings s
            JOIN orders o ON s.order_id = o.id
            JOIN users u ON o.user_id = u.id
            JOIN user_addresses ua ON o.address_id = ua.id
            WHERE s.id = :id
        ');
        $stmt->execute([':id'=>$id]);
        $data = $stmt->fetch();
        if (!$data) { http_response_code(404); echo json_encode(['success'=>false]); exit; }
        echo json_encode(['success'=>true,'data'=>$data]);
    } else {
        $stmt = $db->query('
            SELECT s.*, o.user_id, u.username, ua.city, o.created_at as order_date
            FROM shippings s
            JOIN orders o ON s.order_id = o.id
            JOIN users u ON o.user_id = u.id
            JOIN user_addresses ua ON o.address_id = ua.id
            ORDER BY s.created_at DESC
        ');
        $rows = $stmt->fetchAll();
        echo json_encode(['success'=>true,'data'=>$rows]);
    }

} catch (Exception $e) {
    http_response_code(500); echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>
