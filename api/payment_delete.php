<?php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

try {
    $db = get_db();
    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        throw new Exception("ID tidak valid.");
    }

    // Ambil data untuk hapus QR
    $stmt = $db->prepare("SELECT qr_image FROM tb_pembayaran WHERE id_pembayaran = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // Hapus file QR jika ada
        if ($data['qr_image']) {
            $filePath = __DIR__ . '/../assets/images/qr/' . $data['qr_image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Hapus dari database
        $stmt = $db->prepare("DELETE FROM tb_pembayaran WHERE id_pembayaran = ?");
        $stmt->execute([$id]);

        echo json_encode([
            "success" => true,
            "message" => "Metode pembayaran berhasil dihapus"
        ]);
    } else {
        throw new Exception("Data pembayaran tidak ditemukan.");
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>