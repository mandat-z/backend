<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success'=>false,'message'=>'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';

    if (!$username || !$password) {
        http_response_code(400);
        echo json_encode(['success'=>false,'message'=>'Missing credentials']);
        exit;
    }

    $db = get_db();

    // deteksi nama kolom password di tabel users
    $colsStmt = $db->query("SHOW COLUMNS FROM users");
    $cols = array_column($colsStmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
    $pwCol = null;
    if (in_array('password_hash', $cols)) $pwCol = 'password_hash';
    elseif (in_array('password', $cols)) $pwCol = 'password';
    else {
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'No password column found in users table']);
        exit;
    }

    // build safe query using detected column (column name is from DB, so safe)
    $sql = "SELECT id, username, email, role, {$pwCol} AS stored_pw FROM users WHERE username = :u OR email = :e LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([':u' => $username, ':e' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success'=>false,'message'=>'Invalid username or password']);
        exit;
    }

    $stored = $user['stored_pw'] ?? null;
    if (!$stored || !password_verify($password, $stored)) {
        http_response_code(401);
        echo json_encode(['success'=>false,'message'=>'Invalid username or password']);
        exit;
    }

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['user'] = [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role' => $user['role'] ?? 'pelanggan'
    ];

    echo json_encode(['success'=>true,'message'=>'Logged in','data'=>$_SESSION['user']]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    exit;
}
?>