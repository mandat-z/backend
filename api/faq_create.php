<?php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
  exit;
}

$pertanyaan = trim($_POST['pertanyaan'] ?? '');
$jawaban = trim($_POST['jawaban'] ?? '');
$status = $_POST['status'] ?? 'Nonaktif';

if ($pertanyaan === '' || $jawaban === '') {
  echo json_encode(['success' => false, 'message' => 'Data tidak boleh kosong']);
  exit;
}

try {
  $db = get_db();
  $stmt = $db->prepare("INSERT INTO tb_faq (pertanyaan, jawaban, status) VALUES (?, ?, ?)");
  $stmt->execute([$pertanyaan, $jawaban, $status]);
  echo json_encode(['success' => true, 'message' => 'FAQ berhasil ditambahkan']);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Gagal menambah FAQ: ' . $e->getMessage()]);
}
