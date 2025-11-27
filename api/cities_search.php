<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $db = get_db();
    $q = trim($_GET['q'] ?? '');
    if ($q === '') {
        echo json_encode([]);
        exit;
    }

    // normalize and search (case-insensitive)
    $qnorm = mb_strtolower($q, 'UTF-8');
    $stmt = $db->prepare("
        SELECT id, nama_kota AS name
        FROM cities
        WHERE LOWER(nama_kota) LIKE :like
        ORDER BY nama_kota
        LIMIT 30
    ");
    $stmt->execute([':like' => "%{$qnorm}%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows);
    exit;
} catch (Exception $e) {
    // on error return empty array (don't leak internal errors)
    echo json_encode([]);
    exit;
}
?>