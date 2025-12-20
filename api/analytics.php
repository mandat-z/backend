<?php
session_start();
header("Content-Type: application/json");
require_once __DIR__ . '/../config/config.php';

/*
|--------------------------------------------------------------------------
| PREVIEW MODE (DEV ONLY)
|--------------------------------------------------------------------------
*/
if (isset($_GET['preview']) && $_GET['preview'] == '1') {
    echo json_encode([
        "success" => true,
        "summary" => ["total_user" => 123, "total_order" => 45, "total_omzet" => 67890000],
        "user" => array_map(function ($i) {
            return ["tanggal" => date('Y-m-d', strtotime("-{$i} days")), "total" => rand(0, 10)];
        }, range(29, 0)),
        "sales" => array_map(function ($i) {
            return ["tanggal" => date('Y-m-d', strtotime("-{$i} days")), "total" => rand(100000, 1000000)];
        }, range(29, 0)),
        "produk" => [
            ["nama" => "Produk A", "terjual" => 120],
            ["nama" => "Produk B", "terjual" => 85]
        ]
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| AUTH ADMIN (NONAKTIF SEMENTARA)
|--------------------------------------------------------------------------
| Aktifkan kembali setelah login admin siap
*/
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     echo json_encode([
//         "success" => false,
//         "message" => "Not authenticated as admin."
//     ]);
//     exit;
// }

$db = get_db();

/* KPI */
$summary = [
    "total_user"  => (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    "total_order" => (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    "total_omzet" => (float)$db->query("SELECT IFNULL(SUM(total_harga),0) FROM orders")->fetchColumn(),
];

/* USER */
$user = $db->query("
    SELECT DATE(created_at) tanggal, COUNT(*) total
    FROM users
    GROUP BY DATE(created_at)
    ORDER BY tanggal ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* SALES */
$sales = $db->query("
    SELECT DATE(tanggal_pesan) tanggal, SUM(total_harga) total
    FROM orders
    GROUP BY DATE(tanggal_pesan)
    ORDER BY tanggal ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* PRODUK */
$produk = $db->query("
    SELECT p.nama, SUM(oi.qty) terjual
    FROM order_items oi
    JOIN produk p ON p.id = oi.produk_id
    GROUP BY oi.produk_id
    HAVING terjual > 0
    ORDER BY terjual DESC
")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "summary" => $summary,
    "user"    => $user,
    "sales"  => $sales,
    "produk" => $produk
]);
