<?php
include __DIR__ . '/../config/config.php';
$db = get_db();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $nama = trim($_POST['nama'] ?? '');
        if ($nama !== '') {
            $stmt = $db->prepare("INSERT INTO kategori_produk (nama_kategori) VALUES (:nama)");
            $stmt->execute([':nama' => $nama]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nama kategori kosong']);
        }
        break;

    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM kategori_produk WHERE id_kategori = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => true]);
        break;

    case 'edit':
        $id = intval($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        if ($id && $nama !== '') {
            $stmt = $db->prepare("UPDATE kategori_produk SET nama_kategori = :nama WHERE id_kategori = :id");
            $stmt->execute([':nama' => $nama, ':id' => $id]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'fetch':
        $stmt = $db->query("SELECT * FROM kategori_produk ORDER BY id_kategori DESC");
        $rows = $stmt->fetchAll();
        echo json_encode($rows);
        break;
}
?>
