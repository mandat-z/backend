<?php
header('Content-Type: application/json; charset=utf-8');
if(session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../config/config.php';
$db = get_db();

$userId = $_SESSION['user']['id'] ?? null;
if(!$userId){
    echo json_encode(['success'=>false,'message'=>'Harus login']);
    exit;
}

function jsonRes($arr,$code=200){
    http_response_code($code);
    echo json_encode($arr);
    exit;
}

function readBody(){
    $b = file_get_contents("php://input");
    $p = json_decode($b,true);
    return is_array($p) ? $p : $_POST;
}

try {
    $body = readBody();

    $voucherCode = trim($body['voucher'] ?? '');

    // kalau voucher kosong → hapus session
    if ($voucherCode === "") {
        unset($_SESSION['voucher_code']);
    }

    // kalau kosong tapi session ada → pakai session
    if ($voucherCode === "" && isset($_SESSION['voucher_code'])) {
        $voucherCode = $_SESSION['voucher_code'];
    }

    $selectedCartIds = $body['selected_cart_ids'] ?? null;

    /* --------------------------------------------------------------------------
        AMBIL ITEM CART
    -------------------------------------------------------------------------- */
    $query = "
        SELECT c.id AS cart_id, c.product_id, c.quantity,
               p.nama, p.harga, p.foto, p.size, p.panjang, p.lebar, p.stok
        FROM carts c
        JOIN produk p ON c.product_id = p.id
        WHERE c.user_id=:uid
    ";

    $params = [':uid'=>$userId];

    if ($selectedCartIds && is_array($selectedCartIds) && !empty($selectedCartIds)) {
        $placeholders = str_repeat('?,', count($selectedCartIds) - 1) . '?';
        $query .= " AND c.id IN ($placeholders)";
        $params = array_merge($params, $selectedCartIds);
    }

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!$items){
        jsonRes([
            'success'=>true,
            'items'=>[],
            'subtotal'=>0,
            'shipping'=>0,
            'discount'=>0,
            'total'=>0
        ]);
    }

    /* --------------------------------------------------------------------------
        HITUNG SUBTOTAL
    -------------------------------------------------------------------------- */
    $subtotal = 0;

    foreach ($items as &$it) {
        $price = $it['harga'];
        $it['line_subtotal'] = $price * $it['quantity'];
        $subtotal += $it['line_subtotal'];
    }

    /* --------------------------------------------------------------------------
        HITUNG ONGKIR DARI ALAMAT DEFAULT
    -------------------------------------------------------------------------- */
    $stmt = $db->prepare("
        SELECT c.ongkir
        FROM user_addresses ua
        JOIN cities c ON ua.kota_id = c.id
        WHERE ua.user_id = :uid
          AND ua.is_default = 1
        LIMIT 1
    ");
    $stmt->execute([':uid' => $userId]);
    $shipping = floatval($stmt->fetchColumn() ?? 0);

    /* --------------------------------------------------------------------------
        VOUCHER DISCOUNT
    -------------------------------------------------------------------------- */
    $discount = 0;

    if($voucherCode){
        $stmt = $db->prepare("SELECT * FROM tb_voucher WHERE kode_voucher=:kode AND status='Aktif' LIMIT 1");
        $stmt->execute([':kode'=>$voucherCode]);
        $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

        if($voucher){
            if($subtotal >= floatval($voucher['minimal_belanja'])){

                // simpan voucher ke session
                $_SESSION['voucher_code'] = $voucherCode;

                if($voucher['tipe_diskon'] === 'persen'){
                    $discount = round($subtotal * floatval($voucher['diskon']) / 100);

                    if(floatval($voucher['maksimal_diskon']) > 0 && $discount > floatval($voucher['maksimal_diskon'])){
                        $discount = floatval($voucher['maksimal_diskon']);
                    }
                } else {
                    $discount = floatval($voucher['diskon']);
                }
            }
        }
    }

    /* --------------------------------------------------------------------------
        HITUNG TOTAL
    -------------------------------------------------------------------------- */
    $total = $subtotal + $shipping - $discount;

    jsonRes([
        'success'=>true,
        'items'=>$items,
        'subtotal'=>$subtotal,
        'shipping'=>$shipping,
        'discount'=>$discount,
        'total'=>$total
    ]);

} catch(Exception $e){
    jsonRes(['success'=>false,'message'=>$e->getMessage()],500);
}