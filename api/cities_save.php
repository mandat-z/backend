<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');


$data = json_decode(file_get_contents("php://input"), true);

$id    = intval($data['id'] ?? 0);
$nama  = trim($data['nama_kota'] ?? '');
$ongkir = floatval($data['ongkir'] ?? 0);

if (!$nama || $ongkir <= 0) {
    http_response_code(400);
    exit;
}

$db = get_db();

if ($id) {
    $stmt = $db->prepare("UPDATE cities SET nama_kota=?, ongkir=? WHERE id=?");
    $stmt->execute([$nama, $ongkir, $id]);
} else {
    $stmt = $db->prepare("INSERT INTO cities (nama_kota, ongkir) VALUES (?, ?)");
    $stmt->execute([$nama, $ongkir]);
}

echo json_encode(['success' => true]);
