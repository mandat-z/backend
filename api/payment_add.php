<?php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

try {
    $db = get_db();
    $nama_metode = trim($_POST['nama_metode'] ?? '');
    $jenis = trim($_POST['jenis'] ?? '');
    $tujuan = trim($_POST['tujuan'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $status = trim($_POST['status'] ?? 'Aktif');

    // Validasi input wajib
    if (empty($nama_metode) || empty($jenis)) {
        throw new Exception("Nama metode dan jenis wajib diisi.");
    }

    // Validasi jenis (opsional, cegah input aneh)
    $valid_jenis = ['Transfer Bank', 'E-Wallet', 'COD', 'QRIS'];
    if (!in_array($jenis, $valid_jenis)) {
        throw new Exception("Jenis pembayaran tidak valid.");
    }

    $qrFile = null;
    if (!empty($_FILES['qr_image']['name'])) {
        // Validasi upload
        if ($_FILES['qr_image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error upload file: " . $_FILES['qr_image']['error']);
        }
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['qr_image']['type'], $allowed_types)) {
            throw new Exception("Hanya file gambar (JPEG, PNG, GIF) yang diperbolehkan.");
        }
        if ($_FILES['qr_image']['size'] > 2 * 1024 * 1024) { // 2MB max
            throw new Exception("Ukuran file maksimal 2MB.");
        }

        // Upload
        $uploadDir = __DIR__ . '/../assets/images/qr/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $qrFile = uniqid('qr_', true) . '_' . basename($_FILES['qr_image']['name']);
        if (!move_uploaded_file($_FILES['qr_image']['tmp_name'], $uploadDir . $qrFile)) {
            throw new Exception("Gagal menyimpan file QR.");
        }
    }

    // Insert database
    $stmt = $db->prepare("INSERT INTO tb_pembayaran (nama_metode, jenis, tujuan, keterangan, qr_image, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nama_metode, $jenis, $tujuan, $keterangan, $qrFile, $status]);

    echo json_encode([
        "success" => true,
        "message" => "Metode pembayaran berhasil ditambahkan"
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>