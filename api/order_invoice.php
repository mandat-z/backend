<?php
// =====================================================================
// ORDER INVOICE API (Fixed & DB-Sync)
// File: backend/api/order_invoice.php
// =====================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/config.php';

function respond(int $code, array $payload): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

try {

    // ===============================================================
    // VALIDATE ID
    // ===============================================================
    $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($orderId <= 0) {
        respond(400, [
            'success' => false,
            'message' => 'ID pesanan tidak valid.'
        ]);
    }

    $db = get_db();

    // ===============================================================
    // GET ORDER + USER + ADDRESS + CITY + PAYMENT METHOD (from orders.metode_pembayaran_id)
    // ===============================================================
    $stmt = $db->prepare("
        SELECT
            o.id,
            o.order_code,
            o.user_id,
            o.address_id,
            o.metode_pembayaran_id,
            o.subtotal,
            o.ongkir,
            o.potongan_voucher,
            o.kode_voucher,
            o.total_harga,
            o.status,
            o.tanggal_pesan,
            o.tanggal_sampai,

            u.username,
            u.email,
            u.phone AS user_phone,

            ua.nama_penerima,
            ua.phone AS alamat_phone,
            ua.jalan,
            ua.rt_rw,
            ua.kelurahan,
            ua.kecamatan,
            ua.provinsi,
            ua.kode_pos,

            c.nama_kota,

            pm.nama_metode AS metode_nama,
            pm.jenis      AS metode_jenis,
            pm.tujuan     AS metode_tujuan,
            pm.keterangan AS metode_keterangan,
            pm.qr_image   AS metode_qr_image,
            pm.status     AS metode_status
        FROM orders o
        JOIN users u ON u.id = o.user_id
        LEFT JOIN user_addresses ua ON ua.id = o.address_id
        LEFT JOIN cities c ON c.id = ua.kota_id
        LEFT JOIN metode_pembayaran pm ON pm.id_metode = o.metode_pembayaran_id
        WHERE o.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        respond(404, [
            'success' => false,
            'message' => 'Data pesanan tidak ditemukan.'
        ]);
    }

    // ---------------------------------------------------------------
    // Backward compat untuk frontend Anda yang pakai o.penerima_phone
    // ---------------------------------------------------------------
    // (Di DB tidak ada kolom penerima_phone; yang benar: user_addresses.phone)
    $order['penerima_phone'] = $order['alamat_phone'] ?? null;

    // ===============================================================
    // GET ITEMS
    // ===============================================================
    $stmt = $db->prepare("
        SELECT
            oi.qty,
            oi.harga_satuan,
            oi.subtotal,
            p.nama AS product_name
        FROM order_items oi
        JOIN produk p ON p.id = oi.produk_id
        WHERE oi.order_id = :oid
        ORDER BY oi.id ASC
    ");
    $stmt->execute([':oid' => $orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ===============================================================
    // GET PAYMENT (latest) - from pembayaran_order
    // ===============================================================
    $stmt = $db->prepare("
        SELECT
            po.id_pembayaran,
            po.total_bayar,
            po.jumlah_dibayar,
            po.status AS payment_status,
            po.bukti_pembayaran,
            po.waktu_dibayar,
            po.dibuat_pada,

            pm.nama_metode,
            pm.jenis,
            pm.tujuan,
            pm.keterangan,
            pm.qr_image
        FROM pembayaran_order po
        LEFT JOIN metode_pembayaran pm ON pm.id_metode = po.id_metode
        WHERE po.id_order = :oid
        ORDER BY po.id_pembayaran DESC
        LIMIT 1
    ");
    $stmt->execute([':oid' => $orderId]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$payment) {
        $payment = null;
    }

    // ===============================================================
    // SUCCESS
    // ===============================================================
    respond(200, [
        'success' => true,
        'order'   => $order,
        'items'   => $items,
        'payment' => $payment
    ]);
} catch (Throwable $e) {
    // Jangan bocorkan detail error di production (ini aman untuk dev)
    respond(500, [
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
