<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Ambil user ID dari session
$uid = null;
if (!empty($_SESSION['user']['id'])) {
    $uid = intval($_SESSION['user']['id']);
} elseif (!empty($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
}

if (!$uid) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$db = get_db();


// ======================================================================
// FUNGSI RESOLVE KOTA — SUDAH FIX SESUAI KOLOM `nama_kota`
// ======================================================================
function resolve_kota(PDO $db, $input)
{
    if ($input === null || $input === '') return false;

    // Jika input berupa ID kota
    if (is_numeric($input)) {
        $stmt = $db->prepare("SELECT id FROM cities WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => intval($input)]);
        if ($stmt->fetch()) return intval($input);
        return false;
    }

    // Normalisasi nama
    $name = mb_strtolower(trim(preg_replace('/[^\p{L}\p{N}\s]+/u', '', $input)));

    // Exact match
    $stmt = $db->prepare("SELECT id FROM cities WHERE LOWER(nama_kota) = :name LIMIT 1");
    $stmt->execute([':name' => $name]);
    if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) return intval($r['id']);

    // Starts with
    $stmt = $db->prepare("SELECT id FROM cities WHERE LOWER(nama_kota) LIKE :like LIMIT 1");
    $stmt->execute([':like' => '%' . $name . '%']);
    if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) return intval($r['id']);

    // Contains
    $stmt->execute([':like' => '%' . $name . '%']);
    if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) return intval($r['id']);

    return false;
}


try {

    $method = $_SERVER['REQUEST_METHOD'];


    // ======================================================================
    // GET — AMBIL LIST ADDRESS / ADDRESS BY ID
    // ======================================================================
    if ($method === 'GET') {

        // GET by ID
        if (!empty($_GET['id'])) {
            $id = intval($_GET['id']);

            $stmt = $db->prepare("
                SELECT a.*, c.nama_kota, c.ongkir
                FROM user_addresses a
                LEFT JOIN cities c ON c.id = a.kota_id
                WHERE a.id = :id AND a.user_id = :uid
                LIMIT 1
            ");

            $stmt->execute([':id' => $id, ':uid' => $uid]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Alamat tidak ditemukan']);
                exit;
            }

            echo json_encode(['success' => true, 'data' => $row]);
            exit;
        }

        // GET semua alamat
        $stmt = $db->prepare("
            SELECT a.*, c.nama_kota, c.ongkir
            FROM user_addresses a
            LEFT JOIN cities c ON c.id = a.kota_id
            WHERE a.user_id = :uid
            ORDER BY a.is_default DESC, a.id DESC
        ");

        $stmt->execute([':uid' => $uid]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
    }


    // Baca body JSON untuk POST/PUT/DELETE
    $body = json_decode(file_get_contents('php://input'), true);
    if (!is_array($body)) $body = [];


    // ======================================================================
    // POST — TAMBAH ALAMAT
    // ======================================================================
    if ($method === 'POST') {

        $nama = trim($body['nama_penerima'] ?? '');
        $phone = trim($body['nomor_telepon'] ?? '');
        $jalan = trim($body['alamat_jalan'] ?? '');
        $rt_rw = trim($body['rt_rw'] ?? '');
        $kelurahan = trim($body['kelurahan'] ?? '');
        $kecamatan = trim($body['kecamatan'] ?? '');
        $provinsi = trim($body['provinsi'] ?? '');
        $kode_pos = trim($body['kode_pos'] ?? '');
        $is_default = !empty($body['set_as_default']) ? 1 : 0;

        // Ambil kota
        $kota_input =
            $body['kota_id']
            ?? $body['kota_kabupaten']
            ?? $body['kota']
            ?? $body['city']
            ?? $body['nama_kota']
            ?? null;
        $kota_id = resolve_kota($db, $kota_input);

        if (!$kota_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Kota tidak valid']);
            exit;
        }

        if ($nama === '' || $jalan === '' || $provinsi === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field wajib belum diisi']);
            exit;
        }

        $db->beginTransaction();

        // Jika set default, reset yang lama
        if ($is_default) {
            $db->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = :uid")
               ->execute([':uid' => $uid]);
        }

        // Insert alamat
        $stmt = $db->prepare("
            INSERT INTO user_addresses
            (user_id, nama_penerima, phone, jalan, rt_rw, kelurahan, kecamatan, kota_id, provinsi, kode_pos, is_default)
            VALUES
            (:uid, :nama, :phone, :jalan, :rt_rw, :kelurahan, :kecamatan, :kota_id, :provinsi, :kode_pos, :is_default)
        ");

        $stmt->execute([
            ':uid' => $uid,
            ':nama' => $nama,
            ':phone' => $phone ?: null,
            ':jalan' => $jalan,
            ':rt_rw' => $rt_rw ?: null,
            ':kelurahan' => $kelurahan ?: null,
            ':kecamatan' => $kecamatan ?: null,
            ':kota_id' => $kota_id,
            ':provinsi' => $provinsi,
            ':kode_pos' => $kode_pos ?: null,
            ':is_default' => $is_default
        ]);

        $newId = $db->lastInsertId();
        $db->commit();

        http_response_code(201);
        echo json_encode(['success' => true, 'id' => $newId]);
        exit;
    }


    // ======================================================================
    // PUT — UPDATE ALAMAT
    // ======================================================================
    if ($method === 'PUT') {

        $id = intval($_GET['id'] ?? $body['id'] ?? 0);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID tidak diberikan']);
            exit;
        }

        // Pastikan alamat dimiliki user
        $chk = $db->prepare("SELECT id FROM user_addresses WHERE id = :id AND user_id = :uid LIMIT 1");
        $chk->execute([':id' => $id, ':uid' => $uid]);

        if (!$chk->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Alamat tidak ditemukan']);
            exit;
        }

        $nama = trim($body['nama_penerima'] ?? '');
        $phone = trim($body['nomor_telepon'] ?? '');
        $jalan = trim($body['alamat_jalan'] ?? '');
        $rt_rw = trim($body['rt_rw'] ?? '');
        $kelurahan = trim($body['kelurahan'] ?? '');
        $kecamatan = trim($body['kecamatan'] ?? '');
        $provinsi = trim($body['provinsi'] ?? '');
        $kode_pos = trim($body['kode_pos'] ?? '');
        $is_default = !empty($body['set_as_default']) ? 1 : 0;

        // Kota
        $kota_input =
            $body['kota_id']
            ?? $body['kota_kabupaten']
            ?? $body['kota']
            ?? $body['city']
            ?? $body['nama_kota']
            ?? null;
        $kota_id = resolve_kota($db, $kota_input);

        if (!$kota_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Kota tidak valid']);
            exit;
        }

        $db->beginTransaction();

        // Set default
        if ($is_default) {
            $db->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = :uid")
               ->execute([':uid' => $uid]);
        }

        // Update
        $stmt = $db->prepare("
            UPDATE user_addresses SET
                nama_penerima = :nama,
                phone = :phone,
                jalan = :jalan,
                rt_rw = :rt_rw,
                kelurahan = :kelurahan,
                kecamatan = :kecamatan,
                kota_id = :kota_id,
                provinsi = :provinsi,
                kode_pos = :kode_pos,
                is_default = :is_default
            WHERE id = :id AND user_id = :uid
        ");

        $stmt->execute([
            ':nama' => $nama,
            ':phone' => $phone ?: null,
            ':jalan' => $jalan,
            ':rt_rw' => $rt_rw ?: null,
            ':kelurahan' => $kelurahan ?: null,
            ':kecamatan' => $kecamatan ?: null,
            ':kota_id' => $kota_id,
            ':provinsi' => $provinsi,
            ':kode_pos' => $kode_pos ?: null,
            ':is_default' => $is_default,
            ':id' => $id,
            ':uid' => $uid
        ]);

        $db->commit();

        echo json_encode(['success' => true, 'message' => 'Updated']);
        exit;
    }


    // ======================================================================
    // DELETE — HAPUS ALAMAT
    // ======================================================================
    if ($method === 'DELETE') {

        $id = intval($_GET['id'] ?? $body['id'] ?? 0);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID tidak diberikan']);
            exit;
        }

        // Cek alamat
        $chk = $db->prepare("SELECT id, is_default FROM user_addresses WHERE id = :id AND user_id = :uid LIMIT 1");
        $chk->execute([':id' => $id, ':uid' => $uid]);

        $row = $chk->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Alamat tidak ditemukan']);
            exit;
        }

        $db->prepare("DELETE FROM user_addresses WHERE id = :id AND user_id = :uid")
           ->execute([':id' => $id, ':uid' => $uid]);

        // Jika delete default → set default baru
        if ($row['is_default']) {
            $stmt = $db->prepare("
                SELECT id FROM user_addresses 
                WHERE user_id = :uid 
                ORDER BY id DESC LIMIT 1
            ");
            $stmt->execute([':uid' => $uid]);

            if ($new = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $db->prepare("UPDATE user_addresses SET is_default = 1 WHERE id = :id AND user_id = :uid")
                   ->execute([':id' => $new['id'], ':uid' => $uid]);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Deleted']);
        exit;
    }


    // ❌ Method tidak valid
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;


} catch (PDOException $e) {
    if ($db->inTransaction()) $db->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

?>
