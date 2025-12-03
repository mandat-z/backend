<?php
session_start();
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');

$db = get_db();

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(["success" => false, "message" => "Harus login"]);
    exit;
}

$userId = $_SESSION['user']['id'];
$statusFilter = $_GET['status'] ?? null;

$mapStatus = [
    "pending"   => "pending",
    "packed"    => "dikemas",
    "shipping"  => "dikirim",
    "completed" => "selesai"
];

$dbStatus = $statusFilter && isset($mapStatus[$statusFilter])
    ? $mapStatus[$statusFilter]
    : null;

try {
    if ($dbStatus) {
        $stmt = $db->prepare("
            SELECT 
                o.id,
                o.order_code,
                o.tanggal_pesan,
                o.status,
                o.total_harga,
                (
                    SELECT SUM(qty) FROM order_items WHERE order_id = o.id
                ) AS item_count,
                (
                    SELECT p.foto 
                    FROM order_items oi
                    JOIN produk p ON p.id = oi.produk_id
                    WHERE oi.order_id = o.id
                    LIMIT 1
                ) AS image,
                (
                    SELECT p.nama 
                    FROM order_items oi
                    JOIN produk p ON p.id = oi.produk_id
                    WHERE oi.order_id = o.id
                    LIMIT 1
                ) AS product_name
            FROM orders o
            WHERE o.user_id = ? AND o.status = ?
            ORDER BY o.tanggal_pesan DESC
        ");
        $stmt->execute([$userId, $dbStatus]);
    } else {
        $stmt = $db->prepare("
            SELECT 
                o.id,
                o.order_code,
                o.tanggal_pesan,
                o.status,
                o.total_harga,
                (
                    SELECT SUM(qty) 
                    FROM order_items 
                    WHERE order_id = o.id
                ) AS item_count,
                (
                    SELECT p.foto 
                    FROM order_items oi
                    JOIN produk p ON p.id = oi.produk_id
                    WHERE oi.order_id = o.id
                    LIMIT 1
                ) AS image,
                (
                    SELECT p.nama 
                    FROM order_items oi
                    JOIN produk p ON p.id = oi.produk_id
                    WHERE oi.order_id = o.id
                    LIMIT 1
                ) AS product_name
            FROM orders o
            WHERE o.user_id = ?
            ORDER BY o.tanggal_pesan DESC
        ");
        $stmt->execute([$userId]);
    }

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $orders
    ]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
