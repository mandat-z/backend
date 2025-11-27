<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $db = get_db();

    // ambil 1 data berdasarkan id
    if (isset($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM tb_voucher WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($data ?: []);
        exit;
    }

    // ambil semua voucher
    $stmt = $db->query("SELECT * FROM tb_voucher ORDER BY id DESC");
    $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($vouchers);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
