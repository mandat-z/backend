<?php
session_start();
require_once "../../config/config.php";
$db = get_db();

header("Content-Type: application/json");

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(["success" => false, "message" => "ID tidak valid"]);
    exit;
}

// FK safety
$db->prepare("DELETE FROM crm_targets WHERE campaign_id=?")->execute([$id]);
$db->prepare("DELETE FROM crm_campaign WHERE id=?")->execute([$id]);

echo json_encode(["success" => true]);
