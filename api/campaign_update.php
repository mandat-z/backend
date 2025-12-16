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
$id    = intval($_POST['id'] ?? 0);
$judul = trim($_POST['judul'] ?? '');
$isi   = trim($_POST['isi'] ?? '');
$jenis = trim($_POST['jenis'] ?? '');

// ================= VALIDASI =================
if ($id <= 0 || $judul === '' || $isi === '' || $jenis === '') {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

// ================= UPDATE =================
$stmt = $db->prepare("
    UPDATE crm_campaign
    SET judul=?, isi=?, jenis=?
    WHERE id=?
");
$stmt->execute([$judul, $isi, $jenis, $id]);

echo json_encode(["success" => true]);
