<?php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

try {
    $db = get_db();
    $id = intval($_POST['id_pembayaran'] ?? 0);
    $nama_metode = trim($_POST['nama_metode'] ?? '');
    $jenis = trim($_POST['jenis'] ?? '');
    $tujuan = trim($_POST['tujuan'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $status = trim($_POST['status'] ?? 'Aktif');

    // Validasi input
    if ($id <= 0 || empty($nama_metode) || empty($jenis)) {
        throw new Exception("Data tidak lengkap atau ID tidak valid.");
    }

    // Validasi jenis
    $valid_jenis = ['Transfer Bank', 'E-Wallet', 'COD', 'QRIS'];
    if (!in_array($jenis, $valid_jenis)) {
        throw new Exception("Jenis pembayaran tidak valid.");
    }

    // Ambil data lama
    $stmt = $db->prepare("SELECT qr_image FROM tb_pembayaran WHERE id_pembayaran = ?");
    $stmt->execute([$id]);
    $old = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$old) {
        throw new Exception("Data pembayaran tidak ditemukan.");
    }
    $qrFile = $old['qr_image'];

    // Handle upload baru
    if (!empty($_FILES['qr_image']['name'])) {
        // Validasi upload (sama seperti add)
        if ($_FILES['qr_image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error upload file: " . $_FILES['qr_image']['error']);
        }
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['qr_image']['type'], $allowed_types)) {
            throw new Exception("Hanya file gambar (JPEG, PNG, GIF) yang diperbolehkan.");
        }
        if ($_FILES['qr_image']['size'] > 2 * 1024 * 1024) {
            throw new Exception("Ukuran file maksimal 2MB.");
        }

        // Hapus QR lama jika ada
        if ($qrFile) {
            $oldPath = __DIR__ . '/../assets/images/qr/' . $qrFile;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Upload baru
        $uploadDir = __DIR__ . '/../assets/images/qr/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $qrFile = uniqid('qr_', true) . '_' . basename($_FILES['qr_image']['name']);
        if (!move_uploaded_file($_FILES['qr_image']['tmp_name'], $uploadDir . $qrFile)) {
            throw new Exception("Gagal menyimpan file QR baru.");
        }
    }

    // Update database
    $stmt = $db->prepare("UPDATE tb_pembayaran SET nama_metode=?, jenis=?, tujuan=?, keterangan=?, qr_image=?, status=? WHERE id_pembayaran=?");
    $stmt->execute([$nama_metode, $jenis, $tujuan, $keterangan, $qrFile, $status, $id]);

    echo json_encode([
        "success" => true,
        "message" => "Data metode pembayaran berhasil diperbarui"
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>