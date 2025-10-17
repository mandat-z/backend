<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit;
}

$db = get_db();
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
    if ($id) {
        $stmt = $db->prepare('SELECT * FROM produk WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch();
        if (!$row) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Not found']); exit; }
        echo json_encode(['success'=>true,'data'=>$row]);
        exit;
    } else {
        $stmt = $db->query('SELECT * FROM produk ORDER BY created_at DESC');
        $rows = $stmt->fetchAll();
        echo json_encode(['success'=>true,'data'=>$rows]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    exit;
}
?>