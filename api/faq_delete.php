<?php
// backend/api/faq_delete.php

include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
  exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
  echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
  exit;
}

try {
  $db = get_db();
  $stmt = $db->prepare("DELETE FROM tb_faq WHERE id = ?");
  $stmt->execute([$id]);

  echo json_encode(['success' => true, 'message' => 'FAQ berhasil dihapus']);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Gagal menghapus FAQ: ' . $e->getMessage()]);
}
