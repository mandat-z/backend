<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit; }

$raw = json_decode(file_get_contents('php://input'), true);
if (!$raw) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid JSON']); exit; }

$nama = trim($raw['nama'] ?? '');
$kategori = trim($raw['kategori'] ?? '');
$stok = intval($raw['stok'] ?? 0);
$harga = floatval($raw['harga'] ?? 0);
$deskripsi = $raw['deskripsi'] ?? null;
$foto = $raw['foto'] ?? null;

if ($nama === '' || $kategori === '') { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Missing fields']); exit; }

try {
    $db = get_db();
    $stmt = $db->prepare('INSERT INTO produk (nama,kategori,stok,harga,deskripsi,foto) VALUES (:nama,:kategori,:stok,:harga,:deskripsi,:foto)');
    $stmt->execute([
        ':nama'=>$nama, ':kategori'=>$kategori, ':stok'=>$stok, ':harga'=>$harga,
        ':deskripsi'=>$deskripsi, ':foto'=>$foto
    ]);
    $id = $db->lastInsertId();
    echo json_encode(['success'=>true,'id'=>$id]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    exit;
}
?>