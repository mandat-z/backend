<?php
// backend/api/faq_get.php

include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
$db = get_db();

// Jika ada parameter ID (untuk edit)
if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $stmt = $db->prepare("SELECT * FROM tb_faq WHERE id = ?");
  $stmt->execute([$id]);
  $faq = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($faq) {
    echo json_encode($faq);
  } else {
    echo json_encode(['error' => 'Data tidak ditemukan']);
  }
  exit;
}

// Jika tidak ada ID → ambil semua data
    if (isset($_GET['public']) && $_GET['public'] == '1') {
        $stmt = $db->prepare("SELECT * FROM tb_faq WHERE status='Aktif' ORDER BY id ASC");
        $stmt->execute();
    } else {
        $stmt = $db->query("SELECT * FROM tb_faq ORDER BY id ASC");
    }
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
