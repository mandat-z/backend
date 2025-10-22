<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $db = get_db();
    $baseUrl = BACKEND_URL . '/assets/images/banner/';

    // ambil 1 data berdasarkan id
    if (isset($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM tb_banner WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $data['gambar'] = $baseUrl . $data['gambar'];
        }
        echo json_encode($data ?: []);
        exit;
    }

    // ambil semua banner
    if (isset($_GET['public']) && $_GET['public'] == '1') {
        $stmt = $db->prepare("SELECT * FROM tb_banner WHERE status='Aktif' ORDER BY id DESC");
        $stmt->execute();
    } else {
        $stmt = $db->query("SELECT * FROM tb_banner ORDER BY id DESC");
    }

    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // tambahkan path lengkap untuk gambar
    foreach ($banners as &$b) {
        $b['gambar'] = $baseUrl . $b['gambar'];
    }

    echo json_encode($banners);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
