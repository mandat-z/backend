
<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');


try {
    $db = get_db();
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    if ($id) {
        $stmt = $db->prepare('SELECT id,username,email,phone,birthdate,gender,role,created_at FROM users WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        $u = $stmt->fetch();
        if (!$u) { http_response_code(404); echo json_encode(['success'=>false]); exit; }
        echo json_encode(['success'=>true,'data'=>$u]);
    } else {
        $stmt = $db->query('SELECT id,username,email,phone,birthdate,gender,role,created_at FROM users ORDER BY created_at DESC');
        $rows = $stmt->fetchAll();
        echo json_encode(['success'=>true,'data'=>$rows]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>