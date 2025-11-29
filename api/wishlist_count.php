<?php
header("Access-Control-Allow-Origin: http://localhost/ecommerce");
header("Access-Control-Allow-Credentials: true");

session_set_cookie_params(['lifetime'=>0,'path'=>'/']);
session_start();

require_once __DIR__ . '/../config/config.php';
$db = get_db();

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success'=>true,'count'=>0]);
    exit;
}

$user_id = $_SESSION['user']['id'];

$stmt = $db->prepare("SELECT COUNT(*) FROM tb_wishlist WHERE user_id = ?");
$stmt->execute([$user_id]);
$count = $stmt->fetchColumn();

echo json_encode(['success'=>true,'count'=>$count]);
