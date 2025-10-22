<?php
// backend/api/product_delete.php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
$db = get_db();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Use POST');
    $id = $_POST['id'] ?? null;
    if (!$id) throw new Exception('Product ID missing');

    // fetch all fotos for deletion
    $stmt = $db->prepare("SELECT file_path FROM produk_foto WHERE produk_id = :id");
    $stmt->execute([':id'=>$id]);
    $photos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // delete product (produk_foto has ON DELETE CASCADE OR we delete explicitly)
    $db->prepare("DELETE FROM produk WHERE id = :id")->execute([':id'=>$id]);
    // explicitly delete files
    foreach ($photos as $p) {
        $parsed = parse_url($p);
        if (isset($parsed['path'])) {
            $local = $_SERVER['DOCUMENT_ROOT'] . $parsed['path'];
            if (file_exists($local)) @unlink($local);
        }
    }

    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}
