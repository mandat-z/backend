<?php
include "../config/config.php";
header("Content-Type: application/json");

$db = get_db();

$mulai   = $_GET['mulai']   ?? null;
$selesai = $_GET['selesai'] ?? null;

/* =======================
   PENDAPATAN
======================= */
$sqlPendapatan = "
  SELECT 
    DATE(tanggal_pesan) AS tanggal,
    'Pendapatan' AS kategori,
    CONCAT('Order #', id, ' (', status, ')') AS keterangan,
    total_harga AS jumlah
  FROM orders
  WHERE status IN ('selesai','dikirim','dikemas')
";

$params = [];

if ($mulai) {
    $sqlPendapatan .= " AND DATE(tanggal_pesan) >= ?";
    $params[] = $mulai;
}
if ($selesai) {
    $sqlPendapatan .= " AND DATE(tanggal_pesan) <= ?";
    $params[] = $selesai;
}

$stmt = $db->prepare($sqlPendapatan);
$stmt->execute($params);
$pendapatan = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =======================
   PENGELUARAN
======================= */
$sqlPengeluaran = "
  SELECT 
    tanggal, kategori, keterangan, jumlah
  FROM pengeluaran
  WHERE 1=1
";

$params = [];

if ($mulai) {
    $sqlPengeluaran .= " AND tanggal >= ?";
    $params[] = $mulai;
}
if ($selesai) {
    $sqlPengeluaran .= " AND tanggal <= ?";
    $params[] = $selesai;
}

$stmt = $db->prepare($sqlPengeluaran);
$stmt->execute($params);
$pengeluaran = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =======================
   TOTAL
======================= */
$totalPendapatan  = array_sum(array_column($pendapatan, 'jumlah'));
$totalPengeluaran = array_sum(array_column($pengeluaran, 'jumlah'));

echo json_encode([
    'pendapatan'        => $pendapatan,
    'pengeluaran'       => $pengeluaran,
    'total_pendapatan'  => $totalPendapatan,
    'total_pengeluaran' => $totalPengeluaran,
    'keuntungan_bersih' => $totalPendapatan - $totalPengeluaran
]);
