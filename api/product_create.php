<?php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$db = get_db();

try {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validasi input wajib
    if (empty($data['nama']) || empty($data['kategori']) || empty($data['harga'])) {
        throw new Exception("Nama, kategori, dan harga wajib diisi.");
    }
    
    // Query insert produk
    $stmt = $db->prepare("
        INSERT INTO produk (nama, kategori, stok, harga, deskripsi, foto, created_at)
        VALUES (:nama, :kategori, :stok, :harga, :deskripsi, :foto, NOW())
    ");
    
    $stmt->execute([
        ':nama'      => $data['nama'],
        ':kategori'  => $data['kategori'],
        ':stok'      => $data['stok'] ?? 0,
        ':harga'     => $data['harga'],
        ':deskripsi' => $data['deskripsi'] ?? '',
        ':foto'      => $data['foto'] ?? null
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Produk berhasil ditambahkan.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
