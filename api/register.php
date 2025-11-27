<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success'=>false,'message'=>'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$confirm = $input['confirm_password'] ?? '';
$phone = trim($input['phone'] ?? '');
$birthdate = $input['birthdate'] ?? null;
$gender = in_array($input['gender'] ?? '', ['L','P']) ? $input['gender'] : null;

if (!$username || !$email || !$password || $password !== $confirm) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Invalid input']);
    exit;
}

try {
    $db = get_db();
    // check email exists
    $stmt = $db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email'=>$email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success'=>false,'message'=>'Email already registered']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins = $db->prepare('INSERT INTO users (username,email,password,phone,birthdate,gender,role) VALUES (:username,:email,:password,:phone,:birthdate,:gender,:role)');
    $ins->execute([
        ':username'=>$username,
        ':email'=>$email,
        ':password'=>$hash,
        ':phone'=>$phone ?: null,
        ':birthdate'=>$birthdate ?: null,
        ':gender'=>$gender,
        ':role'=>'pelanggan' // force pelanggan
    ]);

    echo json_encode(['success'=>true,'id'=>$db->lastInsertId()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>