<?php
// Jangan ada spasi/enter sebelum baris ini!
ob_start(); // tampung semua output yang tidak diinginkan

include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

// Matikan error ke browser, biar gak ngotorin JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    // bersihin buffer sebelum kirim JSON
    while (ob_get_level()) ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Method not allowed']);
    exit;
}

try {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);

    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';

    if (!$username || !$password) {
        http_response_code(400);
        while (ob_get_level()) ob_end_clean();
        echo json_encode(['success'=>false,'message'=>'Missing credentials']);
        exit;
    }

    $db = get_db();

    // detect password column
    $colsStmt = $db->query("SHOW COLUMNS FROM users");
    $cols = array_column($colsStmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
    $pwCol = in_array('password_hash', $cols) ? 'password_hash' :
             (in_array('password', $cols) ? 'password' : null);

    if (!$pwCol) {
        http_response_code(500);
        while (ob_get_level()) ob_end_clean();
        echo json_encode(['success'=>false,'message'=>'No password column found']);
        exit;
    }

    $sql = "SELECT id, username, email, role, {$pwCol} AS stored_pw 
            FROM users 
            WHERE username = :u OR email = :e 
            LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([':u'=>$username, ':e'=>$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['stored_pw'])) {
        http_response_code(401);
        while (ob_get_level()) ob_end_clean();
        echo json_encode(['success'=>false,'message'=>'Invalid username or password']);
        exit;
    }

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    // ====== SESSION TERPISAH: ADMIN & PELANGGAN ======
    if (($user['role'] ?? '') === 'admin') {
        $_SESSION['admin'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'email'    => $user['email'],
            'role'     => 'admin'
        ];

        $data = $_SESSION['admin'];
    } else {
        $_SESSION['user'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'email'    => $user['email'],
            'role'     => 'pelanggan'
        ];

        $data = $_SESSION['user'];
    }

    // Bersihkan SEMUA output lain, baru kirim JSON
    while (ob_get_level()) ob_end_clean();

    echo json_encode([
        'success' => true,
        'message' => 'Logged in',
        'data'    => $data
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    while (ob_get_level()) ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    exit;
}
