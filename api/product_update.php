<?php
// backend/api/product_update.php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
$db = get_db();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Gunakan POST');
    $id = $_POST['id'] ?? null;
    if (!$id) throw new Exception('ID produk tidak diberikan');

    $nama      = trim($_POST['nama'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $stok      = intval($_POST['stok'] ?? 0);
    $harga     = $_POST['harga'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $size      = $_POST['size'] ?? null;
    $panjang   = $_POST['panjang'] ?? null;
    $lebar     = $_POST['lebar'] ?? null;

    if ($nama === '' || $kategori === '' || $harga === '') throw new Exception('Nama, kategori, dan harga wajib diisi.');

    $db->beginTransaction();

    $db->prepare("UPDATE produk SET nama=:nama, kategori=:kategori, stok=:stok, harga=:harga, deskripsi=:deskripsi, size=:size, panjang=:panjang, lebar=:lebar WHERE id=:id")
       ->execute([
           ':nama'=>$nama, ':kategori'=>$kategori, ':stok'=>$stok, ':harga'=>$harga,
           ':deskripsi'=>$deskripsi, ':size'=>$size, ':panjang'=>$panjang, ':lebar'=>$lebar, ':id'=>$id
       ]);

    // handle newly uploaded photos (append)
    if (!empty($_FILES['foto']) && !empty($_FILES['foto']['name'][0])) {
        $uploadDir = __DIR__ . '/../assets/images/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        // compute next urutan
        $urStmt = $db->prepare("SELECT COALESCE(MAX(urutan),0) FROM produk_foto WHERE produk_id = :pid");
        $urStmt->execute([':pid'=>$id]);
        $next = intval($urStmt->fetchColumn()) + 1;

        foreach ($_FILES['foto']['name'] as $i => $origName) {
            $error = $_FILES['foto']['error'][$i];
            if ($error !== UPLOAD_ERR_OK) continue;
            $tmp = $_FILES['foto']['tmp_name'][$i];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp','gif'];
            if (!in_array($ext, $allowed)) continue;
            $newName = uniqid('prod_', true) . '.' . $ext;
            $target = $uploadDir . $newName;
            if (!move_uploaded_file($tmp, $target)) continue;
            $url = rtrim(BACKEND_URL, '/') . '/assets/images/products/' . $newName;
            $db->prepare("INSERT INTO produk_foto (produk_id, file_path, urutan) VALUES (:pid,:fp,:ur)")
               ->execute([':pid'=>$id, ':fp'=>$url, ':ur'=>$next]);
            $next++;
        }
    }

    // ensure produk.foto points to first photo if not present
    $first = $db->prepare("SELECT file_path FROM produk_foto WHERE produk_id = :pid ORDER BY urutan ASC LIMIT 1");
    $first->execute([':pid'=>$id]);
    $main = $first->fetchColumn();
    if ($main) {
        $db->prepare("UPDATE produk SET foto = :foto WHERE id = :id")->execute([':foto'=>$main, ':id'=>$id]);
    }

    $db->commit();
    echo json_encode(['success'=>true, 'message'=>'Produk berhasil diperbarui.']);
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}
