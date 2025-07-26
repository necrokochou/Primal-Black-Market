<?php

declare(strict_types=1);

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to browser
ini_set('log_errors', 1);     // Log errors

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent accidental echo output (HTML/errors/warnings)
ob_start();

// Define BASE_PATH if not already defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/..'));
}

try {
    require_once BASE_PATH . '/bootstrap.php';
    require_once UTILS_PATH . '/envSetter.util.php';
    require_once UTILS_PATH . '/auth.util.php';
} catch (Exception $e) {
    ob_clean();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to load required files: ' . $e->getMessage()]);
    exit;
}

// Clean any output that might have been generated
ob_clean();
header('Content-Type: application/json');

try {
    $host = $_ENV['PG_HOST'] ?? 'localhost';
    $port = $_ENV['PG_PORT'] ?? '5432';
    $db = $_ENV['PG_DB'] ?? 'primal-black-market';
    $user = $_ENV['PG_USER'] ?? 'user';
    $pass = $_ENV['PG_PASS'] ?? 'password';
    
    $pdo = new PDO(
        "pgsql:host={$host};port={$port};dbname={$db}",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    ob_clean();
    header('Content-Type: application/json');
    http_response_code(500);
    error_log('Database connection error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database connection failed. Please check your database configuration.']);
    exit;
}

try {
    $auth = new \App\Utils\Auth($pdo);
} catch (Exception $e) {
    ob_clean();
    header('Content-Type: application/json');
    http_response_code(500);
    error_log('Auth class instantiation error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Auth initialization failed: ' . $e->getMessage()]);
    exit;
}

// Helper function to ensure clean JSON output
function outputJson($data, $statusCode = 200) {
    ob_clean();
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    outputJson(['success' => false, 'error' => 'Method not allowed'], 405);
}


try {
    $action = $_POST['action'] ?? '';

if ($action === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $loginResult = $auth->tryLogin($username, $password);
    if ($loginResult !== null) {
        // $_SESSION['user'] = $loginResult['username'];
        // $_SESSION['is_admin'] = $loginResult['is_admin'] ?? false;
        // $_SESSION['is_vendor'] = $loginResult['is_vendor'] ?? false;
        // $_SESSION['user_email'] = $loginResult['email'] ?? '';
        // $_SESSION['user_alias'] = $loginResult['alias'] ?? $loginResult['username'];
        // $_SESSION['user_trust_level'] = $loginResult['trustlevel'] ?? 0;

        $_SESSION['user'] = [
            'id' => $loginResult['user_id'],
            'username' => $loginResult['username'],
            'email' => $loginResult['email'],
            'alias' => $loginResult['alias'] ?? $loginResult['username'],
            'trust_level' => $loginResult['trustlevel'],
            'is_admin' => $loginResult['is_admin'] ?? false
        ];

        echo json_encode([
            'success' => true
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
    }
    exit;
}


if ($action === 'register') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $alias = $_POST['alias'] ?? $username;
    $account_type = $_POST['account_type'] ?? 'buyer';

    if (!$username || !$email || !$password) {
        outputJson(['success' => false, 'error' => 'All fields are required'], 400);
        exit;
    }

    $is_vendor = ($account_type === 'seller');

    require UTILS_PATH . '/register.util.php';

    $result = registerUser($username, $password, $email, $alias, $is_vendor);

    if ($result['success']) {
        $_SESSION['user'] = [
            'id' => $result['user_id'],
            'username' => $result['username'],
            'email' => $result['email'],
            'alias' => $result['alias'] ?? $result['username'],
            'trust_level' => $result['trustlevel'],
            'is_admin' => $result['is_admin'] ?? false,
            'is_vendor' => $result['is_vendor'] ?? false
        ];
        outputJson(['success' => true]);
    } else {
        outputJson(['success' => false, 'error' => $result['error'] ?? 'Registration failed.'], 500);
    }
}

if ($action === 'get_session_user') {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'error' => 'Not logged in']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'user' => $_SESSION['user']
    ]);
}

// If invalid action
outputJson(['success' => false, 'error' => 'Invalid action'], 400);

} catch (Exception $e) {
    error_log('Auth handler error: ' . $e->getMessage());
    outputJson(['success' => false, 'error' => 'Server error occurred'], 500);
}
