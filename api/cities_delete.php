<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id'] ?? 0);

if (!$id) {
    http_response_code(400);
    exit;
}

$db = get_db();
$db->prepare("DELETE FROM cities WHERE id=?")->execute([$id]);

echo json_encode(['success' => true]);
