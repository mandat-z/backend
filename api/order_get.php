<?php
session_start();
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $db = get_db();
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    // =============================================================
    // GET SINGLE ORDER
    // =============================================================
    if ($id) {
        $stmt = $db->prepare('
            SELECT 
                o.*, 
                u.username, u.email, u.phone as user_phone,

                ua.nama_penerima, ua.jalan, ua.rt_rw, ua.kelurahan, ua.kecamatan,
                c.nama_kota, ua.provinsi, ua.kode_pos, ua.phone as alamat_phone,

                pm.nama_metode, pm.jenis, pm.tujuan, pm.keterangan, 
                pm.qr_image, pm.status as metode_status

            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN user_addresses ua ON o.address_id = ua.id
            LEFT JOIN cities c ON ua.kota_id = c.id
            LEFT JOIN metode_pembayaran pm ON o.metode_pembayaran_id = pm.id_metode
            WHERE o.id = :id
        ');
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }

        // =============================================================
        // GET ORDER ITEMS (FIXED product_id -> produk_id)
        // =============================================================
        $stmt = $db->prepare('
            SELECT 
                oi.qty, 
                oi.harga_satuan, 
                oi.subtotal,
                p.nama as product_name, 
                p.foto
            FROM order_items oi
            JOIN produk p ON oi.produk_id = p.id
            WHERE oi.order_id = :order_id
        ');
        $stmt->execute([':order_id' => $id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // =============================================================
        // PAYMENT INFO
        // =============================================================
        $stmt = $db->prepare('
            SELECT 
                po.id_pembayaran, po.total_bayar, po.jumlah_dibayar,
                po.status as payment_status, po.bukti_pembayaran, po.waktu_dibayar,
                pm.nama_metode, pm.jenis, pm.tujuan, pm.qr_image, pm.keterangan
            FROM pembayaran_order po
            LEFT JOIN metode_pembayaran pm ON po.id_metode = pm.id_metode
            WHERE po.id_order = :order_id
            LIMIT 1
        ');
        $stmt->execute([':order_id' => $id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $order,
            'items' => $items,
            'payment' => $payment
        ]);
        exit;
    }

    // =============================================================
    // GET ALL ORDERS
    // =============================================================
    $stmt = $db->query('
        SELECT 
            o.id, o.order_code, o.user_id, 
            o.subtotal, o.ongkir, o.total_harga,
            o.status, o.tanggal_pesan,
            u.username, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.tanggal_pesan DESC
    ');

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $orders]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
