<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$db = get_db();
$data = $db->query("SELECT * FROM cities ORDER BY nama_kota ASC")->fetchAll();

echo json_encode(['success' => true, 'data' => $data]);
