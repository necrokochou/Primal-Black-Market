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

function registerUser(string $username, string $password, string $email, ?string $alias = null): array
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
    INSERT INTO users (Username, Password, Email, Alias, Created_At)
    VALUES (:username, :password, :email, :alias, NOW())
    ");

    $ok = $stmt->execute([
        'username' => $username,
        'password' => $hashedPassword,
        'email'    => $email,
        'alias'    => $alias,
    ]);

    if ($ok) {
        $userIdStmt = $pdo->prepare("SELECT User_ID FROM users WHERE Username = :username");
        $userIdStmt->execute(['username' => $username]);
        $userId = $userIdStmt->fetchColumn();

        return [
            'success' => true,
            'user_id' => $userId,
            'username' => $username,
            'email' => $email,
            'alias' => $alias,
            'trustlevel' => 0,
            'is_admin' => false
        ];
    }

    return ['success' => false];
}
