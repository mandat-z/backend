<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

// Cek method
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Ambil raw JSON body
$raw = json_decode(file_get_contents('php://input'), true);

// Ambil data
$id             = intval($raw['id'] ?? 0);
$status         = $raw['status'] ?? '';
$tanggal_sampai = $raw['tanggal_sampai'] ?? null;

// Validasi dasar
$allowed_status = ['pending', 'dikemas', 'dikirim', 'selesai', 'dibatalkan'];

if (!$id || !in_array($status, $allowed_status)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid data input'
    ]);
    exit;
}

try {
    $db = get_db();

    // Pastikan order ada
    $check = $db->prepare("SELECT id FROM orders WHERE id = :id LIMIT 1");
    $check->execute([':id' => $id]);

    if ($check->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Order not found'
        ]);
        exit;
    }

    // Update data
    $stmt = $db->prepare("
        UPDATE orders 
        SET status = :status,
            tanggal_sampai = :tanggal_sampai
        WHERE id = :id
    ");

    $stmt->execute([
        ':status' => $status,
        ':tanggal_sampai' => $tanggal_sampai,
        ':id' => $id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Order updated successfully'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
