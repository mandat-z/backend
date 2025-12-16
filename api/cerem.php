<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/config.php";
$db = get_db();

header("Content-Type: application/json");


$action = $_GET["action"] ?? null;

// ================= HELPER =================
function input($key)
{
    $json = json_decode(file_get_contents("php://input"), true);
    if (is_array($json) && isset($json[$key])) return $json[$key];
    return $_POST[$key] ?? null;
}

// ================= LIST =================
if ($action === "list") {
    $q = $db->query("SELECT * FROM crm_campaign ORDER BY id DESC");
    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// ================= DETAIL =================
if ($action === "detail" && isset($_GET["id"])) {
    $id = intval($_GET["id"]);

    $camp = $db->prepare("SELECT * FROM crm_campaign WHERE id=?");
    $camp->execute([$id]);
    $campaign = $camp->fetch(PDO::FETCH_ASSOC);

    $targets = $db->prepare("SELECT * FROM crm_targets WHERE campaign_id=?");
    $targets->execute([$id]);

    echo json_encode([
        "success"  => true,
        "campaign" => $campaign,
        "targets"  => $targets->fetchAll(PDO::FETCH_ASSOC)
    ]);
    exit;
}

// ================= CREATE =================
if ($action === "create") {
    $judul = input("judul");
    $isi   = input("isi");
    $jenis = input("jenis");

    if (!$judul || !$isi || !$jenis) {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
        exit;
    }

    $stmt = $db->prepare("
        INSERT INTO crm_campaign (judul, isi, jenis, status)
        VALUES (?, ?, ?, 'draft')
    ");
    $stmt->execute([$judul, $isi, $jenis]);

    echo json_encode(["success" => true]);
    exit;
}

// ================= UPDATE =================
if ($action === "update" && isset($_GET["id"])) {
    $id    = intval($_GET["id"]);
    $judul = input("judul");
    $isi   = input("isi");
    $jenis = input("jenis");
    $status = input("status") ?? 'draft';

    if (!$judul || !$isi || !$jenis) {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
        exit;
    }

    $stmt = $db->prepare("
        UPDATE crm_campaign 
        SET judul=?, isi=?, jenis=?, status=?
        WHERE id=?
    ");
    $stmt->execute([$judul, $isi, $jenis, $status, $id]);

    echo json_encode(["success" => true]);
    exit;
}

// ================= DELETE =================
if ($action === "delete" && isset($_GET["id"])) {
    $id = intval($_GET["id"]);

    // hapus target dulu (FK aman)
    $db->prepare("DELETE FROM crm_targets WHERE campaign_id=?")
        ->execute([$id]);

    $db->prepare("DELETE FROM crm_campaign WHERE id=?")
        ->execute([$id]);

    echo json_encode(["success" => true]);
    exit;
}

// ================= GENERATE TARGET =================
if ($action === "generate" && isset($_GET["id"])) {
    $id = intval($_GET["id"]);

    $users = $db->query("
        SELECT id, email, phone 
        FROM users 
        WHERE role='pelanggan'
    ")->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("
        INSERT INTO crm_targets (campaign_id, user_id, email, phone)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($users as $u) {
        $stmt->execute([$id, $u['id'], $u['email'], $u['phone']]);
    }

    echo json_encode(["success" => true, "count" => count($users)]);
    exit;
}

// ================= SEND =================
if ($action === "send" && isset($_GET["id"])) {
    $id = intval($_GET["id"]);

    $db->prepare("
        UPDATE crm_targets 
        SET status='sent', sent_at=NOW()
        WHERE campaign_id=?
    ")->execute([$id]);

    $db->prepare("
        UPDATE crm_campaign 
        SET status='sent'
        WHERE id=?
    ")->execute([$id]);

    echo json_encode(["success" => true]);
    exit;
}

echo json_encode(["success" => false, "message" => "Invalid API"]);
exit;
