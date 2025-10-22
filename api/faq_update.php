<?php
// backend/api/faq_update.php

include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
  exit;
}

$id = intval($_POST['id'] ?? 0);
$pertanyaan = trim($_POST['pertanyaan'] ?? '');
$jawaban = trim($_POST['jawaban'] ?? '');
$status = $_POST['status'] ?? 'Nonaktif';

if ($id <= 0 || $pertanyaan === '' || $jawaban === '') {
  echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
  exit;
}

try {
  $db = get_db();
  $stmt = $db->prepare("UPDATE tb_faq SET pertanyaan=?, jawaban=?, status=? WHERE id=?");
  $stmt->execute([$pertanyaan, $jawaban, $status, $id]);

  echo json_encode(['success' => true, 'message' => 'FAQ berhasil diperbarui']);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Gagal update FAQ: ' . $e->getMessage()]);
}
