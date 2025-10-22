<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $db = get_db(); // ambil koneksi PDO dari config.php

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0) {
        throw new Exception('ID banner tidak valid.');
    }

    // Ambil data gambar
    $stmt = $db->prepare("SELECT gambar FROM tb_banner WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        throw new Exception('Data banner tidak ditemukan.');
    }

    // Hapus file gambar kalau ada
    if (!empty($row['gambar'])) {
        $path = __DIR__ . '/../assets/images/banner/' . $row['gambar'];
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // Hapus data dari DB
    $stmt = $db->prepare("DELETE FROM tb_banner WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Banner berhasil dihapus'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
