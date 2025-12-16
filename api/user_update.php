
<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit;
}

$raw = json_decode(file_get_contents('php://input'), true);
$id = intval($raw['id'] ?? 0);
if ($id <= 0) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Missing id']); exit; }

$username = trim($raw['username'] ?? '');
$email = trim($raw['email'] ?? '');
$phone = trim($raw['phone'] ?? '');
$birthdate = $raw['birthdate'] ?? null;
$gender = in_array($raw['gender'] ?? '', ['L','P']) ? $raw['gender'] : null;
$role = in_array($raw['role'] ?? '', ['admin','pelanggan']) ? $raw['role'] : null;
$password = $raw['password'] ?? null; // optional: set new password if provided

try {
    $db = get_db();
    // check email conflict
    $stmt = $db->prepare('SELECT id FROM users WHERE email = :email AND id != :id');
    $stmt->execute([':email'=>$email, ':id'=>$id]);
    if ($stmt->fetch()) { http_response_code(409); echo json_encode(['success'=>false,'message'=>'Email exists']); exit; }

    $sets = [];
    $params = [':id'=>$id];
    if ($username !== '') { $sets[] = 'username=:username'; $params[':username']=$username; }
    if ($email !== '') { $sets[] = 'email=:email'; $params[':email']=$email; }
    $sets[] = 'phone=:phone'; $params[':phone']=$phone ?: null;
    $sets[] = 'birthdate=:birthdate'; $params[':birthdate']=$birthdate ?: null;
    $sets[] = 'gender=:gender'; $params[':gender']=$gender;
    if ($role !== null) { $sets[] = 'role=:role'; $params[':role']=$role; }
    if ($password) { $sets[] = 'password=:password'; $params[':password']=password_hash($password, PASSWORD_DEFAULT); }

    if (empty($sets)) { echo json_encode(['success'=>true]); exit; }
    $sql = 'UPDATE users SET ' . implode(',', $sets) . ' WHERE id=:id';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    http_response_code(500); echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>