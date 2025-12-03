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
$orderId = $_GET['id'] ?? null;

if (!$orderId) {
    echo json_encode(["success" => false, "message" => "ID order diperlukan"]);
    exit;
}

try {
    // ambil order + alamat + pembayaran
    $stmt = $db->prepare("
        SELECT 
            o.*,
            ua.nama_penerima,
            ua.jalan,
            ua.rt_rw,
            ua.kelurahan,
            ua.kecamatan,
            ua.provinsi,
            ua.kode_pos,
            c.nama_kota,
            pm.nama_metode,
            pm.jenis as metode_jenis,
            pm.tujuan,
            pm.qr_image
        FROM orders o
        LEFT JOIN user_addresses ua ON ua.id = o.address_id
        LEFT JOIN cities c ON c.id = ua.kota_id
        LEFT JOIN metode_pembayaran pm ON pm.id_metode = o.metode_pembayaran_id
        WHERE o.id = ? AND o.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(["success" => false, "message" => "Order tidak ditemukan"]);
        exit;
    }

    // ambil item
    $stmt2 = $db->prepare("
        SELECT 
            oi.qty,
            oi.harga_satuan,
            oi.subtotal,
            p.nama as product_name,
            p.foto
        FROM order_items oi
        JOIN produk p ON p.id = oi.produk_id
        WHERE oi.order_id = ?
    ");
    $stmt2->execute([$orderId]);
    $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "order" => $order,
        "items" => $items
    ]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
