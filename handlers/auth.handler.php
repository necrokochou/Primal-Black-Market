<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent accidental echo output (HTML/errors/warnings)
ob_start();

require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';
require_once UTILS_PATH . '/auth.util.php';

header('Content-Type: application/json');

$pdo = new PDO(
    "pgsql:host={$_ENV['PG_HOST']};port={$_ENV['PG_PORT']};dbname={$_ENV['PG_DB']}",
    $_ENV['PG_USER'],
    $_ENV['PG_PASS'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$auth = new \App\Utils\Auth($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

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

    if (!$username || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit;
    }

    require UTILS_PATH . '/register.util.php';

    $result = registerUser($username, $password, $email, $alias);

    if ($result['success']) {
        $_SESSION['user'] = [
            'id' => $result['user_id'],
            'username' => $result['username'],
            'email' => $result['email'],
            'alias' => $result['alias'] ?? $result['username'],
            'trust_level' => $result['trustlevel'],
            'is_admin' => $result['is_admin'] ?? false
        ];
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Registration failed.']);
    }
    exit;
}

// If invalid action
http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Invalid action']);
exit;
