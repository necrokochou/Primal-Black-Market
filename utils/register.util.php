<?php
require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/dbConnect.util.php'; // Assumes connectPostgres() exists

function isUsernameTaken(string $username): bool
{
    try {
        $pdo = connectPostgres();
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return (bool) $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Error checking username: " . $e->getMessage());
        return false; // Assume not taken if error occurs
    }
}

function isEmailTaken(string $email): bool
{
    try {
        $pdo = connectPostgres();
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Error checking email: " . $e->getMessage());
        return false; // Assume not taken if error occurs
    }
}

function isAliasTaken(string $alias): bool
{
    try {
        $pdo = connectPostgres();
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE alias = :alias");
        $stmt->execute(['alias' => $alias]);
        return (bool) $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Error checking alias: " . $e->getMessage());
        return false; // Assume not taken if error occurs
    }
}

function registerUser(string $username, string $password, string $email, ?string $alias = null, bool $is_vendor = false): array
{
    if ($alias === null) {
        $alias = $username; // fallback alias
    }

    // Add validation logging
    error_log("Registration attempt: username=$username, email=$email, is_vendor=" . ($is_vendor ? 'true' : 'false'));
    error_log("is_vendor type: " . gettype($is_vendor));
    error_log("is_vendor value: " . var_export($is_vendor, true));

    if (isUsernameTaken($username)) {
        error_log("Registration failed: Username already exists - $username");
        return ['success' => false, 'error' => 'Username already exists.'];
    }

    if (isEmailTaken($email)) {
        error_log("Registration failed: Email already registered - $email");
        return ['success' => false, 'error' => 'Email already registered.'];
    }

    if (isAliasTaken($alias)) {
        error_log("Registration failed: Alias already in use - $alias");
        return ['success' => false, 'error' => 'Alias is already in use.'];
    }

    try {
        $pdo = connectPostgres();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Log the exact parameters being used
        error_log("Preparing INSERT with parameters:");
        error_log("- username: $username");
        error_log("- email: $email");
        error_log("- alias: $alias");
        error_log("- is_vendor: " . ($is_vendor ? 'true' : 'false'));

        $stmt = $pdo->prepare("
        INSERT INTO users (username, password, email, alias, is_vendor, created_at)
        VALUES (:username, :password, :email, :alias, :is_vendor, CURRENT_TIMESTAMP)
    ");

        // Use PDO's native boolean handling for PostgreSQL
        $executeParams = [
            'username' => $username,
            'password' => $hashedPassword,
            'email'    => $email,
            'alias'    => $alias,
            'is_vendor' => $is_vendor,
        ];

        // Bind the boolean parameter explicitly with proper conversion
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);

        // Convert boolean to PostgreSQL-compatible format
        $vendorBoolean = $is_vendor ? 'true' : 'false';
        $stmt->bindParam(':is_vendor', $vendorBoolean, PDO::PARAM_STR);

        error_log("Executing INSERT with params: " . json_encode($executeParams));

        $ok = $stmt->execute();

        if (!$ok) {
            error_log("Registration failed: Failed to insert user - $username");
            $errorInfo = $stmt->errorInfo();
            error_log("PDO Error Info: " . json_encode($errorInfo));
            return ['success' => false, 'error' => 'Failed to create user account.'];
        }

        error_log("Registration successful: User inserted - $username");
    } catch (PDOException $e) {
        error_log("Registration PDO error during insert: " . $e->getMessage());
        error_log("PDO Error Code: " . $e->getCode());
        error_log("SQL State: " . $e->errorInfo[0] ?? 'N/A');
        error_log("Driver Error Code: " . $e->errorInfo[1] ?? 'N/A');
        error_log("Driver Error Message: " . $e->errorInfo[2] ?? 'N/A');
        return ['success' => false, 'error' => 'Database error occurred during registration.'];
    } catch (Exception $e) {
        error_log("Registration general error during insert: " . $e->getMessage());
        return ['success' => false, 'error' => 'Database error occurred during registration.'];
    }

    if ($ok) {
        $userStmt = $pdo->prepare("
            SELECT user_id, username, email, 
                   alias, trustlevel, is_admin, 
                   is_vendor 
            FROM users WHERE username = :username
        ");
        $userStmt->execute(['username' => $username]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'error' => 'Failed to retrieve user data after registration.'];
        }

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

    return ['success' => false, 'error' => 'Failed to insert user into database.'];
}
