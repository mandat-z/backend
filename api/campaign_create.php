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
$judul = trim($_POST['judul'] ?? '');
$isi   = trim($_POST['isi'] ?? '');
$jenis = trim($_POST['jenis'] ?? '');

// ================= VALIDASI =================
if ($judul === '' || $isi === '' || $jenis === '') {
    echo json_encode([
        "success" => false,
        "message" => "Judul, isi, dan jenis wajib diisi"
    ]);
    exit;
}

// ================= INSERT =================
$stmt = $db->prepare("
    INSERT INTO crm_campaign (judul, isi, jenis, status)
    VALUES (?, ?, ?, 'draft')
");
$stmt->execute([$judul, $isi, $jenis]);

echo json_encode([
    "success" => true,
    "id" => (int) $db->lastInsertId()
]);
