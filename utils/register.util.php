<?php
require_once BASE_PATH . '/utils/dbConnect.util.php'; // Assumes connectPostgres() exists

function isUsernameTaken(string $username): bool
{
    $pdo = connectPostgres();
    $stmt = $pdo->prepare("SELECT 1 FROM users WHERE Username = :username");
    $stmt->execute(['username' => $username]);
    return (bool) $stmt->fetchColumn();
}

function registerUser(string $username, string $password, string $email, string $alias): bool
{
    if (isUsernameTaken($username)) return false;

    $pdo = connectPostgres();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Securely hash password

    $stmt = $pdo->prepare("
        INSERT INTO users (Username, Password, Email, Alias)
        VALUES (:username, :password, :email, :alias)
    ");

    return $stmt->execute([
        'username' => $username,
        'password' => $hashedPassword,
        'email'    => $email,
        'alias'    => $alias,
    ]);
}
