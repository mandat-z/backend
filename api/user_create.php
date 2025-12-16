
<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit;
}

$raw = json_decode(file_get_contents('php://input'), true);
$username = trim($raw['username'] ?? '');
$email = trim($raw['email'] ?? '');
$password = $raw['password'] ?? '';
$role = in_array($raw['role'] ?? '', ['admin','pelanggan']) ? $raw['role'] : 'pelanggan';
$phone = trim($raw['phone'] ?? '');

if (!$username || !$email || !$password) {
    http_response_code(400); echo json_encode(['success'=>false,'message'=>'Missing fields']); exit;
}

try {
    $db = get_db();
    $stmt = $db->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute([':email'=>$email]);
    if ($stmt->fetch()) { http_response_code(409); echo json_encode(['success'=>false,'message'=>'Email exists']); exit; }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins = $db->prepare('INSERT INTO users (username,email,password,phone,role) VALUES (:username,:email,:password,:phone,:role)');
    $ins->execute([
        ':username'=>$username, ':email'=>$email, ':password'=>$hash, ':phone'=>$phone ?: null, ':role'=>$role
    ]);
    echo json_encode(['success'=>true,'id'=>$db->lastInsertId()]);
} catch (Exception $e) {
    http_response_code(500); echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>