<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit; }
if (empty($_FILES['gambar'])) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'No file uploaded']); exit; }

$file = $_FILES['gambar'];
if ($file['error'] !== UPLOAD_ERR_OK) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Upload error']); exit; }

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
$allowed = ['image/jpeg','image/png','image/gif','image/webp'];
if (!in_array($mime, $allowed)) { http_response_code(415); echo json_encode(['success'=>false,'message'=>'Invalid file type']); exit; }

$maxSize = 5 * 1024 * 1024;
if ($file['size'] > $maxSize) { http_response_code(413); echo json_encode(['success'=>false,'message'=>'File too large']); exit; }

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$targetDir = __DIR__ . '/../assets/images/products';
if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

$filename = time() . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
$targetPath = $targetDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Cannot move uploaded file']);
    exit;
}

// public URL
$publicUrl = rtrim(ASSET, '/') . '/images/products/' . $filename;
echo json_encode(['success'=>true,'url'=>$publicUrl]);
exit;
?>