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
$id = intval($raw['id'] ?? 0);
$status = $raw['status'] ?? null;
$tracking_number = trim($raw['tracking_number'] ?? '');

if (!$id) {
    http_response_code(400); echo json_encode(['success'=>false,'message'=>'Missing id']); exit;
}

try {
    $db = get_db();

    $sets = [];
    $params = [':id'=>$id];
    if ($status) { $sets[] = 'status=:status'; $params[':status']=$status; }
    if ($tracking_number !== '') { $sets[] = 'tracking_number=:tracking_number'; $params[':tracking_number']=$tracking_number ?: null; }

    if (!empty($sets)) {
        $sets[] = 'updated_at=NOW()';
        $sql = 'UPDATE shippings SET ' . implode(',', $sets) . ' WHERE id=:id';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }

    echo json_encode(['success'=>true]);

} catch (Exception $e) {
    http_response_code(500); echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>
