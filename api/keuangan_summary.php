<?php
// backend/api/keuangan_summary.php
include __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

try {
    $db = get_db();

    // =====================
    // RINGKASAN BULAN INI
    // =====================
    $bulanSekarang = date('Y-m-01');
    $bulanAkhir = date('Y-m-t');

    // Pendapatan bulan ini
    $stmt = $db->prepare("
        SELECT COALESCE(SUM(total_harga), 0) as total
        FROM orders
        WHERE status = 'selesai'
        AND DATE(tanggal_pesan) >= :mulai
        AND DATE(tanggal_pesan) <= :selesai
    ");
    $stmt->execute([':mulai' => $bulanSekarang, ':selesai' => $bulanAkhir]);
    $pendapatanBulanIni = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Pengeluaran bulan ini
    $stmt = $db->prepare("
        SELECT COALESCE(SUM(nominal), 0) as total
        FROM pengeluaran
        WHERE tanggal >= :mulai
        AND tanggal <= :selesai
    ");
    $stmt->execute([':mulai' => $bulanSekarang, ':selesai' => $bulanAkhir]);
    $pengeluaranBulanIni = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // =====================
    // RINGKASAN TAHUN INI
    // =====================
    $tahunSekarang = date('Y-01-01');
    $tahunAkhir = date('Y-12-31');

    // Pendapatan tahun ini
    $stmt = $db->prepare("
        SELECT COALESCE(SUM(total_harga), 0) as total
        FROM orders
        WHERE status = 'selesai'
        AND DATE(tanggal_pesan) >= :mulai
        AND DATE(tanggal_pesan) <= :selesai
    ");
    $stmt->execute([':mulai' => $tahunSekarang, ':selesai' => $tahunAkhir]);
    $pendapatanTahunIni = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Pengeluaran tahun ini
    $stmt = $db->prepare("
        SELECT COALESCE(SUM(nominal), 0) as total
        FROM pengeluaran
        WHERE tanggal >= :mulai
        AND tanggal <= :selesai
    ");
    $stmt->execute([':mulai' => $tahunSekarang, ':selesai' => $tahunAkhir]);
    $pengeluaranTahunIni = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // =====================
    // KATEGORI PENGELUARAN TERBESAR
    // =====================
    $stmt = $db->prepare("
        SELECT kategori, SUM(nominal) as total
        FROM pengeluaran
        WHERE tanggal >= :mulai AND tanggal <= :selesai
        GROUP BY kategori
        ORDER BY total DESC
        LIMIT 5
    ");
    $stmt->execute([':mulai' => $bulanSekarang, ':selesai' => $bulanAkhir]);
    $kategoriTerbesar = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'bulan_ini' => [
            'pendapatan' => floatval($pendapatanBulanIni),
            'pengeluaran' => floatval($pengeluaranBulanIni),
            'keuntungan' => floatval($pendapatanBulanIni - $pengeluaranBulanIni)
        ],
        'tahun_ini' => [
            'pendapatan' => floatval($pendapatanTahunIni),
            'pengeluaran' => floatval($pengeluaranTahunIni),
            'keuntungan' => floatval($pendapatanTahunIni - $pengeluaranTahunIni)
        ],
        'kategori_terbesar' => $kategoriTerbesar
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
