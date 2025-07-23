<?php
define('BASE_PATH', realpath(__DIR__));
define('COMPONENTS_PATH', BASE_PATH . "/components");
define('TEMPLATES_PATH', BASE_PATH . "/components/templates");
define('HANDLERS_PATH', BASE_PATH . "/handlers");
define('LAYOUTS_PATH', BASE_PATH . "/layouts");
define('PAGES_PATH', BASE_PATH . "/pages");
define('STATICDATAS_PATH', BASE_PATH . "/staticData");
define('DUMMIES_PATH', BASE_PATH . "/staticData/dummies");
define('UTILS_PATH', BASE_PATH . "/utils");
define('ERRORS_PATH', BASE_PATH . "/servers");

chdir(BASE_PATH);


// ✅ Add this block for PDO initialization
$host = 'postgresql';
$db   = 'primal-black-market';
$user = 'user';
$pass = 'password';
$charset = 'utf8mb4';

$dsn = "pgsql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // important for debugging
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log("PDO connection failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection error']);
    exit;
}