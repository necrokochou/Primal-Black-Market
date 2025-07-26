<?php
require_once BASE_PATH . '/utils/dbConnect.util.php'; // Assumes connectPostgres() exists

function isUsernameTaken(string $username): bool
{
    $pdo = connectPostgres();
    $stmt = $pdo->prepare("SELECT 1 FROM users WHERE Username = :username");
    $stmt->execute(['username' => $username]);
    return (bool) $stmt->fetchColumn();
}

function isEmailTaken(string $email): bool
{
    $pdo = connectPostgres();
    $stmt = $pdo->prepare("SELECT 1 FROM users WHERE Email = :email");
    $stmt->execute(['email' => $email]);
    return (bool) $stmt->fetchColumn();
}

function isAliasTaken(string $alias): bool
{
    $pdo = connectPostgres();
    $stmt = $pdo->prepare("SELECT 1 FROM users WHERE Alias = :alias");
    $stmt->execute(['alias' => $alias]);
    return (bool) $stmt->fetchColumn();
}

function registerUser(string $username, string $password, string $email, ?string $alias = null, bool $is_vendor = false): array
{
    if ($alias === null) {
        $alias = $username; // fallback alias
    }

    if (isUsernameTaken($username)) {
        return ['success' => false, 'error' => 'Username already exists.'];
    }

    if (isEmailTaken($email)) {
        return ['success' => false, 'error' => 'Email already registered.'];
    }

    if (isAliasTaken($alias)) {
        return ['success' => false, 'error' => 'Alias is already in use.'];
    }

    $pdo = connectPostgres();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (Username, Password, Email, Alias, Is_Vendor)
        VALUES (:username, :password, :email, :alias, :is_vendor)
    ");

    $ok = $stmt->execute([
        'username' => $username,
        'password' => $hashedPassword,
        'email'    => $email,
        'alias'    => $alias,
        'is_vendor' => $is_vendor,
    ]);

    if ($ok) {
        $userStmt = $pdo->prepare("
            SELECT User_ID, Username, Email, Alias, TrustLevel, Is_Admin, Is_Vendor 
            FROM users WHERE Username = :username
        ");
        $userStmt->execute(['username' => $username]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        return [
            'success' => true, 
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'alias' => $user['alias'],
            'trustlevel' => $user['trustlevel'],
            'is_admin' => $user['is_admin'],
            'is_vendor' => $user['is_vendor']
        ];
    }

    return ['success' => false];
}
