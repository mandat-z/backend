<?php
session_start();
header('Content-Type: application/json');

// Jika logout admin
if (isset($_SESSION['admin'])) {
    unset($_SESSION['admin']);
    echo json_encode([
        'success' => true,
        'type' => 'admin',
        'message' => 'Admin berhasil logout'
    ]);
    exit;
}

// Jika logout user (pelanggan)
if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
    echo json_encode([
        'success' => true,
        'type' => 'user',
        'message' => 'Pelanggan berhasil logout'
    ]);
    exit;
}

// Tidak ada session yang login
echo json_encode([
    'success' => false,
    'message' => 'Tidak ada sesi aktif'
]);
exit;
?>
