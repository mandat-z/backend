<?php
// backend/api/product_get.php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
$db = get_db();

try {
    $id = $_GET['id'] ?? null;
    if (!$id) throw new Exception('ID produk tidak diberikan');

    $stmt = $db->prepare("SELECT * FROM produk WHERE id = :id");
    $stmt->execute([':id'=>$id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$p) throw new Exception('Produk tidak ditemukan');

    $stmt2 = $db->prepare("SELECT id, file_path FROM produk_foto WHERE produk_id = :id ORDER BY urutan ASC");
    $stmt2->execute([':id'=>$id]);
    $photos = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success'=>true, 'data'=>$p, 'photos'=>$photos]);
} catch (Exception $e) {
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}
