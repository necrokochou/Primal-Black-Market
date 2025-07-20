<?php

declare(strict_types=1);

// Bootstrap and Env
require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';
require_once UTILS_PATH . '/auth.util.php';

session_start();
header('Content-Type: application/json');

// Setup DB connection
$dsn = "pgsql:host={$_ENV['PG_HOST']};port={$_ENV['PG_PORT']};dbname={$_ENV['PG_DB']}";
$pdo = new PDO($dsn, $_ENV['PG_USER'], $_ENV['PG_PASS'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$auth = new \App\Utils\Auth($pdo);

// Handle login
if ($_POST['action'] === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($auth->tryLogin($username, $password)) {
        $_SESSION['user'] = $username;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid username or password.']);
    }
    exit;
}

// Handle registration
if ($_POST['action'] === 'register') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$username || !$email || !$password) {
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }

    // Check if user exists
    $stmt = $pdo->prepare('SELECT 1 FROM users WHERE Username = :username OR Email = :email');
    $stmt->execute([':username' => $username, ':email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Username or email already exists.']);
        exit;
    }

    // Hash password and insert
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (Username, Email, Password) VALUES (:username, :email, :password)');
    $ok = $stmt->execute([':username' => $username, ':email' => $email, ':password' => $hashed]);

    if ($ok) {
        $_SESSION['user'] = $username;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Registration failed.']);
    }
    exit;
}

// Invalid action
http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Invalid action or data']);
exit;
