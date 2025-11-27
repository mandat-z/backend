<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$db = get_db();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
    exit;
}

$id = $_POST['id'] ?? null;
$kode_voucher = $_POST['kode_voucher'] ?? '';
$diskon = $_POST['diskon'] ?? '';
$tipe_diskon = $_POST['tipe_diskon'] ?? '';
$minimal_belanja = $_POST['minimal_belanja'] ?? '';
$maksimal_diskon = $_POST['maksimal_diskon'] ?? 0;
$berlaku_hingga = $_POST['berlaku_hingga'] ?? '';
$status = $_POST['status'] ?? 'Nonaktif';

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
    exit;
}

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

try {
    $stmt = $db->prepare("UPDATE tb_voucher SET kode_voucher = ?, diskon = ?, tipe_diskon = ?, maksimal_diskon = ?, minimal_belanja = ?, berlaku_hingga = ?, status = ? WHERE id = ?");
    $stmt->execute([$kode_voucher, $diskon, $tipe_diskon, $maksimal_diskon, $minimal_belanja, $berlaku_hingga, $status, $id]);

    echo json_encode(['status' => 'success', 'message' => 'Voucher berhasil diperbarui']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
