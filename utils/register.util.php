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

function registerUser(string $username, string $password, string $email, string $alias): array
{
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
        INSERT INTO users (Username, Password, Email, Alias)
        VALUES (:username, :password, :email, :alias)
    ");

    $ok = $stmt->execute([
        'username' => $username,
        'password' => $hashedPassword,
        'email'    => $email,
        'alias'    => $alias,
    ]);

    return ['success' => $ok];
}
