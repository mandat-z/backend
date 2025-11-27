
<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');


 if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
     http_response_code(403); echo json_encode(['success'=>false,'message'=>'Forbidden']); exit;
 }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit;
}
$raw = json_decode(file_get_contents('php://input'), true);
$id = intval($raw['id'] ?? 0);
if ($id <= 0) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Missing id']); exit; }

try {
    $db = get_db();
    $stmt = $db->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute([':id'=>$id]);
    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    http_response_code(500); echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>