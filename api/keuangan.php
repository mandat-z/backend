<?php
// backend/api/keuangan.php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

try {
  $db = get_db();

  $mulai   = $_GET['mulai']   ?? null;
  $selesai = $_GET['selesai'] ?? null;

  // =====================
  // PENDAPATAN (Orders selesai)
  // =====================
  $sqlPendapatan = "
        SELECT 
            DATE(tanggal_pesan) AS tanggal,
            'Pendapatan' AS kategori,
            CONCAT('Order #', id) AS keterangan,
            CAST(total_harga AS DECIMAL(15,2)) AS jumlah
        FROM orders
        WHERE status = 'selesai'
    ";

  $paramsPendapatan = [];
  if ($mulai) {
    $sqlPendapatan .= " AND DATE(tanggal_pesan) >= :mulai";
    $paramsPendapatan[':mulai'] = $mulai;
  }
  if ($selesai) {
    $sqlPendapatan .= " AND DATE(tanggal_pesan) <= :selesai";
    $paramsPendapatan[':selesai'] = $selesai;
  }
  $sqlPendapatan .= " ORDER BY tanggal DESC";

  $stmt = $db->prepare($sqlPendapatan);
  $stmt->execute($paramsPendapatan);
  $pendapatan = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // =====================
  // PENGELUARAN (Tabel pengeluaran)
  // =====================
  $sqlPengeluaran = "
        SELECT 
            tanggal,
            kategori,
            deskripsi AS keterangan,
            nominal AS jumlah
        FROM pengeluaran
        WHERE 1=1
    ";

  $paramsPengeluaran = [];
  if ($mulai) {
    $sqlPengeluaran .= " AND tanggal >= :mulai";
    $paramsPengeluaran[':mulai'] = $mulai;
  }
  if ($selesai) {
    $sqlPengeluaran .= " AND tanggal <= :selesai";
    $paramsPengeluaran[':selesai'] = $selesai;
  }
  $sqlPengeluaran .= " ORDER BY tanggal DESC";

  $stmt = $db->prepare($sqlPengeluaran);
  $stmt->execute($paramsPengeluaran);
  $pengeluaran = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // =====================
  // TOTAL
  // =====================
  $totalPendapatan = 0;
  foreach ($pendapatan as $row) {
    $totalPendapatan += floatval($row['jumlah']);
  }

  $totalPengeluaran = 0;
  foreach ($pengeluaran as $row) {
    $totalPengeluaran += floatval($row['jumlah']);
  }

  echo json_encode([
    'success' => true,
    'pendapatan' => $pendapatan,
    'pengeluaran' => $pengeluaran,
    'total_pendapatan' => floatval($totalPendapatan),
    'total_pengeluaran' => floatval($totalPengeluaran),
    'keuntungan_bersih' => floatval($totalPendapatan - $totalPengeluaran)
  ]);
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'message' => $e->getMessage()
  ]);
}
