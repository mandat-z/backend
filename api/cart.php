<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../config/config.php';
$db = get_db();

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Harus login']);
    exit;
}

function jsonRes(array $arr, int $code = 200) {
    http_response_code($code);
    echo json_encode($arr);
    exit;
}

function readBody() {
    $b = file_get_contents("php://input");
    $p = json_decode($b, true);
    return is_array($p) ? $p : $_POST;
}

// Tentukan action
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
if (!$action) {
    switch ($method) {
        case 'POST': $action = 'add'; break;
        case 'PUT': $action = 'update'; break;
        case 'DELETE': $action = 'remove'; break;
        case 'GET': $action = 'list'; break;
    }
}

try {

    // ==========================================================
    // LIST CART
    // ==========================================================
    if ($action === 'list') {
        $stmt = $db->prepare("
            SELECT c.id, c.product_id, c.quantity,
                   p.nama, p.harga, p.foto, p.size, p.panjang, p.lebar, p.stok
            FROM carts c
            JOIN produk p ON c.product_id = p.id
            WHERE c.user_id = :uid
            ORDER BY c.id DESC
        ");
        $stmt->execute([':uid' => $userId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        jsonRes(['success' => true, 'data' => $items]);
    }

    // ==========================================================
    // ADD TO CART
    // ==========================================================
    if ($action === 'add') {
        $body = readBody();
        $pid = intval($body['product_id'] ?? 0);
        $qty = max(1, intval($body['quantity'] ?? 1));
        $buyNow = !empty($body['buy_now']);

        if (!$pid) jsonRes(['success' => false, 'message' => 'product_id kosong'], 400);

        // Cek produk ada & stok
        $stmt = $db->prepare("SELECT stok FROM produk WHERE id=:id");
        $stmt->execute([':id' => $pid]);
        $stok = intval($stmt->fetchColumn() ?? 0);

        if ($stok <= 0) jsonRes(['success' => false, 'message' => 'Produk habis'], 400);

        // cek apakah sudah ada di cart
        $stmt = $db->prepare("SELECT id, quantity FROM carts WHERE user_id=:uid AND product_id=:pid LIMIT 1");
        $stmt->execute([':uid' => $userId, ':pid' => $pid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $newQty = $row['quantity'] + $qty;
            if ($newQty > $stok) jsonRes(['success' => false, 'message' => "Maksimal $stok tersedia"], 400);

            $upd = $db->prepare("UPDATE carts SET quantity=:q WHERE id=:id");
            $upd->execute([':q' => $newQty, ':id' => $row['id']]);
        } else {
            if ($qty > $stok) $qty = $stok;
            $ins = $db->prepare("
                INSERT INTO carts (user_id, product_id, quantity)
                VALUES (:uid, :pid, :qty)
            ");
            $ins->execute([':uid' => $userId, ':pid' => $pid, ':qty' => $qty]);
        }

        // buy now → simpan di session
        if ($buyNow) {
            $_SESSION['buy_now'] = [
                'product_id' => $pid,
                'quantity'   => $qty
            ];
        }

        jsonRes(['success' => true, 'message' => 'Ditambahkan ke keranjang']);
    }

    // ==========================================================
    // UPDATE QTY
    // ==========================================================
    if ($action === 'update') {
        $body = readBody();
        $cartId = intval($body['cart_id'] ?? 0);
        $qty = max(1, intval($body['quantity'] ?? 1));

        if (!$cartId) jsonRes(['success' => false, 'message' => 'cart_id kosong'], 400);

        // cek stok produk
        $stmt = $db->prepare("
            SELECT stok 
            FROM produk 
            WHERE id = (SELECT product_id FROM carts WHERE id=:cid AND user_id=:uid)
        ");
        $stmt->execute([':cid' => $cartId, ':uid' => $userId]);
        $stok = intval($stmt->fetchColumn() ?? 0);

        if ($qty > $stok) jsonRes(['success' => false, 'message' => "Maksimal $stok tersedia"], 400);

        $stmt = $db->prepare("UPDATE carts SET quantity=:q WHERE id=:id AND user_id=:uid");
        $stmt->execute([':q' => $qty, ':id' => $cartId, ':uid' => $userId]);

        jsonRes(['success' => true, 'message' => 'Diupdate']);
    }

    // ==========================================================
    // REMOVE ITEM
    // ==========================================================
    if ($action === 'remove') {
        $body = readBody();
        $cartId = intval($body['cart_id'] ?? 0);

        if (!$cartId) jsonRes(['success' => false, 'message' => 'cart_id kosong'], 400);

        $stmt = $db->prepare("DELETE FROM carts WHERE id=:id AND user_id=:uid");
        $stmt->execute([':id' => $cartId, ':uid' => $userId]);

        jsonRes(['success' => true, 'message' => 'Dihapus']);
    }

    // ==========================================================
    // CLEAR CART
    // ==========================================================
    if ($action === 'clear') {
        $stmt = $db->prepare("DELETE FROM carts WHERE user_id=:uid");
        $stmt->execute([':uid' => $userId]);
        unset($_SESSION['buy_now']);
        jsonRes(['success' => true, 'message' => 'Cart dikosongkan']);
    }

    // unknown action
    jsonRes(['success' => false, 'message' => 'unknown action'], 400);

} catch (Exception $e) {
    jsonRes(['success' => false, 'message' => $e->getMessage()], 500);
}
?>
