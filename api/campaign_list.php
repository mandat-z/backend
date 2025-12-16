<?php
session_start();
require_once "../../config/config.php";
$db = get_db();

header("Content-Type: application/json");


$stmt = $db->query("
    SELECT id, judul, jenis, status, created_at
    FROM crm_campaign
    ORDER BY id DESC
");

echo json_encode([
    "success" => true,
    "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
