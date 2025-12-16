<?php
session_start();
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');


$db = get_db();

try {

    // ================= SUMMARY =================
    $summary = [
        'total_produk'     => (int) $db->query("SELECT COUNT(*) FROM produk")->fetchColumn(),
        'total_pesanan'    => (int) $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
        'total_pelanggan'  => (int) $db->query("SELECT COUNT(*) FROM users WHERE role='pelanggan'")->fetchColumn(),
        'pesanan_pending' => (int) $db->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn(),
        'stok_rendah'      => (int) $db->query("SELECT COUNT(*) FROM produk WHERE stok <= 5")->fetchColumn(),
        'pendapatan'       => (float) $db->query("
            SELECT COALESCE(SUM(total_harga),0)
            FROM orders
            WHERE status='selesai'
        ")->fetchColumn()
    ];

    // ================= ORDER TREND (30 HARI) =================
    $orderTrend = $db->query("
        SELECT 
            DATE(tanggal_pesan) AS tanggal,
            COUNT(*) AS total
        FROM orders
        WHERE tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY DATE(tanggal_pesan)
        ORDER BY tanggal ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ================= TOP PRODUK =================
    $topProduk = $db->query("
        SELECT 
            p.nama,
            SUM(oi.qty) AS terjual,
            p.stok
        FROM order_items oi
        JOIN produk p ON p.id = oi.produk_id
        GROUP BY oi.produk_id
        ORDER BY terjual DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ================= LATEST ORDERS =================
    $latestOrders = $db->query("
        SELECT 
            o.order_code,
            u.username,
            o.total_harga,
            o.status
        FROM orders o
        JOIN users u ON u.id = o.user_id
        ORDER BY o.tanggal_pesan DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'summary' => $summary,
        'order_trend' => $orderTrend,
        'top_produk' => $topProduk,
        'latest_orders' => $latestOrders
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error' => $e->getMessage()
    ]);
}
