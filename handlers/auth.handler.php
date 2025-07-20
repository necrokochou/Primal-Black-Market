<?php

declare(strict_types=1);

require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';
require_once UTILS_PATH . '/auth.util.php';

session_start();

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

    if ($auth->tryLogin($username, $password)) {
        $_SESSION['user'] = $username;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
    }
    exit;
}

if ($action === 'register') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    // Temporary default alias: use the username
    $alias = $_POST['alias'] ?? $username;

    if (!$username || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit;
    }

    require UTILS_PATH . '/register.util.php';

    $result = registerUser($username, $password, $email, $alias);

    if ($result['success']) {
        $_SESSION['user'] = $username;
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
