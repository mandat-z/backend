<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit; }

$raw = json_decode(file_get_contents('php://input'), true);
$id = intval($raw['id'] ?? 0);
if ($id <= 0) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Missing id']); exit; }

try {
    $db = get_db();
    // optional: get foto path to unlink
    $stmt = $db->prepare('SELECT foto FROM produk WHERE id=:id');
    $stmt->execute([':id'=>$id]);
    $row = $stmt->fetch();
    if ($row && !empty($row['foto'])) {
        // only unlink if file is inside assets/images/products
        $possible = realpath(__DIR__ . '/../' . str_replace(ASSET, 'assets', $row['foto']));
        // skip unlink to avoid accidental deletes; implement if sure
    }

    $stmt = $db->prepare('DELETE FROM produk WHERE id=:id');
    $stmt->execute([':id'=>$id]);
    echo json_encode(['success'=>true]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    exit;
}
?>