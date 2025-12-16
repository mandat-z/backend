<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../../config/config.php";
$db = get_db();

header("Content-Type: application/json");

// ================= AUTH =================
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Akses ditolak"
    ]);
    exit;
}

// ================= INPUT =================
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "ID tidak valid"
    ]);
    exit;
}

// ================= QUERY =================
$stmt = $db->prepare("SELECT * FROM crm_campaign WHERE id=?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// ================= RESPONSE =================
if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak ditemukan"
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "data" => $data
]);
