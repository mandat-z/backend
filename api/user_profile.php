<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$uid = null;
// support different session shapes
if (!empty($_SESSION['user']['id'])) $uid = intval($_SESSION['user']['id']);
elseif (!empty($_SESSION['user_id'])) $uid = intval($_SESSION['user_id']);

if (!$uid) {
    http_response_code(401);
    echo json_encode(['success'=>false,'message'=>'Not authenticated']);
    exit;
}

try {
    $db = get_db();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $db->prepare('SELECT id, username, email, phone AS phone, birthdate, gender, role FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $uid]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$u) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'User not found']); exit; }
        echo json_encode(['success'=>true,'data'=>$u]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid JSON']); exit;
        }

        // basic validation / sanitize
        $username = trim($input['username'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? null);
        $birthdate = $input['birthdate'] ?? null;
        $gender = in_array($input['gender'] ?? '', ['L','P','Laki-laki','Perempuan']) ? $input['gender'] : null;

        if ($username === '' || $email === '') {
            http_response_code(400);
            echo json_encode(['success'=>false,'message'=>'Missing required fields']);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success'=>false,'message'=>'Invalid email']);
            exit;
        }

        // check email not used by other user
        $chk = $db->prepare('SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1');
        $chk->execute([':email'=>$email, ':id'=>$uid]);
        if ($chk->fetch()) {
            http_response_code(409);
            echo json_encode(['success'=>false,'message'=>'Email already in use']);
            exit;
        }

        $upd = $db->prepare('UPDATE users SET username = :username, email = :email, phone = :phone, birthdate = :birthdate, gender = :gender WHERE id = :id');
        $upd->execute([
            ':username'=>$username,
            ':email'=>$email,
            ':phone'=>$phone ?: null,
            ':birthdate'=>$birthdate ?: null,
            ':gender'=>$gender ?: null,
            ':id'=>$uid
        ]);

        // refresh session user if present
        if (!empty($_SESSION['user'])) {
            $_SESSION['user']['username'] = $username;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['phone'] = $phone;
            $_SESSION['user']['birthdate'] = $birthdate;
            $_SESSION['user']['gender'] = $gender;
        }

        echo json_encode(['success'=>true,'message'=>'Profile updated']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success'=>false,'message'=>'Method not allowed']);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    exit;
}
?>