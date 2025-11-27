<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
    exit;
}

// Ambil koneksi PDO
$db = get_db();

$kode_voucher = $_POST['kode_voucher'] ?? '';
$diskon = $_POST['diskon'] ?? '';
$tipe_diskon = $_POST['tipe_diskon'] ?? '';
$minimal_belanja = $_POST['minimal_belanja'] ?? '';
$maksimal_diskon = $_POST['maksimal_diskon'] ?? 0;
$berlaku_hingga = $_POST['berlaku_hingga'] ?? '';
$status = $_POST['status'] ?? 'Nonaktif';

// Validasi
if (trim($kode_voucher) === '') {
    echo json_encode(['status' => 'error', 'message' => 'Kode voucher tidak boleh kosong']);
    exit;
}
if (!is_numeric($diskon) || $diskon <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Diskon harus berupa angka positif']);
    exit;
}
if (!in_array($tipe_diskon, ['persen', 'nominal'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tipe diskon tidak valid']);
    exit;
}
if (!is_numeric($minimal_belanja) || $minimal_belanja < 0) {
    echo json_encode(['status' => 'error', 'message' => 'Minimal belanja harus berupa angka']);
    exit;
}
if (strtotime($berlaku_hingga) <= time()) {
    echo json_encode(['status' => 'error', 'message' => 'Tanggal berlaku harus di masa depan']);
    exit;
}

// Simpan ke database
try {
    $stmt = $db->prepare("INSERT INTO tb_voucher (kode_voucher, diskon, tipe_diskon, maksimal_diskon, minimal_belanja, berlaku_hingga, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$kode_voucher, $diskon, $tipe_diskon, $maksimal_diskon, $minimal_belanja, $berlaku_hingga, $status]);

    echo json_encode(['status' => 'success', 'message' => 'Voucher berhasil ditambahkan']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
