<?php
// backend/api/pengeluaran_list.php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$db = get_db();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') throw new Exception('Gunakan GET');

    $bulan = $_GET['bulan'] ?? '';
    $tahun = $_GET['tahun'] ?? date('Y');
    $kategori = $_GET['kategori'] ?? '';

    $sql = "SELECT id_pengeluaran, tanggal, kategori, deskripsi, nominal, created_at, updated_at 
            FROM pengeluaran WHERE 1=1";
    $params = [];

    if ($bulan !== '' && $tahun !== '') {
        $sql .= " AND YEAR(tanggal) = :tahun AND MONTH(tanggal) = :bulan";
        $params[':tahun'] = intval($tahun);
        $params[':bulan'] = str_pad(intval($bulan), 2, '0', STR_PAD_LEFT);
    } elseif ($tahun !== '') {
        $sql .= " AND YEAR(tanggal) = :tahun";
        $params[':tahun'] = intval($tahun);
    }

    if ($kategori !== '') {
        $sql .= " AND kategori = :kategori";
        $params[':kategori'] = $kategori;
    }

    $sql .= " ORDER BY tanggal DESC, created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // hitung total
    $total = 0;
    foreach ($data as $row) {
        $total += floatval($row['nominal']);
    }

    echo json_encode([
        'status' => 'success',
        'total' => $total,
        'data' => $data
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
