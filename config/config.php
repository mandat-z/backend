<?php
// Local development config for backend (admin) + frontend integration

// ROOT_PATH: folder root dari backend (satu level di atas file ini)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR);
}

// URLs untuk environment lokal
if (!defined('FRONTEND_URL')) {
    define('FRONTEND_URL', 'http://localhost/ecommerce'); // folder frontend di htdocs
}
if (!defined('BACKEND_URL')) {
    define('BACKEND_URL',  'http://localhost/backend');  // folder backend di htdocs (this project)
}

// BACKWARD COMPAT: BASE_URL dipakai template/backend sebelumnya
if (!defined('BASE_URL')) {
    define('BASE_URL', BACKEND_URL);
}

// URL ke folder assets (dipakai di template)
if (!defined('ASSET')) {
    define('ASSET', BASE_URL . '/assets');
}

// start session untuk fitur auth (safety jika dipanggil berulang)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional: allow frontend (local) to call backend API during development
if (php_sapi_name() !== 'cli') {
    if (!headers_sent()) {
        header('Access-Control-Allow-Origin: ' . FRONTEND_URL);
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/*
 * Database (PDO) connection
 * - DB_NAME: effort_outdoor
 * - Default XAMPP credentials: user = root, pass = ''  (ubah jika perlu)
 */
if (!defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'effort_outdoor');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

if (!defined('DB_DSN')) {
    define('DB_DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET);
}

/* create PDO singleton in $pdo and accessor get_db() */
if (!isset($pdo) || !($pdo instanceof PDO)) {
    try {
        $pdoOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, $pdoOptions);
    } catch (PDOException $e) {
        // untuk development tunjukkan error; di production log dan tampilkan pesan generik
        if (php_sapi_name() === 'cli') {
            echo "DB Connection failed: " . $e->getMessage() . PHP_EOL;
        } else {
            http_response_code(500);
            echo "Database connection error: " . htmlspecialchars($e->getMessage());
        }
        exit;
    }
}

if (!function_exists('get_db')) {
    /**
     * Return PDO instance (singleton)
     * @return PDO
     */
    function get_db(): PDO {
        global $pdo;
        return $pdo;
    }
}
?>