<?php

namespace App\Utils;

use PDO;
use Exception;

class Auth
{
    private PDO $account;

    public function __construct(PDO $account)
    {
        $this->account = $account;
    }

    public function tryLogin(string $username, string $password): ?array
    {
        $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $statement = $this->account->prepare("SELECT * FROM users WHERE {$field} = :username");
        $statement->bindParam(':username', $username);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$user || !isset($user['password'])) {
            return null;
        }

        if (password_verify($password, $user['password'])) {
            // Return normalized field names for consistency
            return [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'alias' => $user['alias'],
                'trustlevel' => $user['trustlevel'],
                'is_admin' => $user['is_admin'],
                'is_vendor' => $user['is_vendor']
            ];
        }

        return null;
    }

    public function getLoggedInUserID(): ?string
    {
        if (!isset($_SESSION['user'])) {
            return null;
        }

        // Now that $_SESSION['user'] is an array, extract the username:
        $username = $_SESSION['user']['username'] ?? null;
        if (!$username) {
            return null;
        }

        $statement = $this->account->prepare('SELECT user_id FROM users WHERE username = :username');
        $statement->bindParam(':username', $username);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['user_id'] : null;
    }

    public function getUserData(string $username): ?array
    {
        $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $stmt = $this->account->prepare("SELECT * FROM users WHERE {$field} = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function tryRegister(string $username, string $email, string $password, string $alias = '', string $accountType = 'buyer'): array
    {
        try {
            // Check if username already exists
            $stmt = $this->account->prepare("SELECT user_id FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            if ($stmt->fetch()) {
                return ['success' => false, 'error' => 'Username already exists'];
            }

            // Check if email already exists
            $stmt = $this->account->prepare("SELECT user_id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->fetch()) {
                return ['success' => false, 'error' => 'Email already exists'];
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Use username as alias if not provided
            if (empty($alias)) {
                $alias = $username;
            }

            // Insert new user
            $stmt = $this->account->prepare("
                INSERT INTO users (username, email, password, alias, trustlevel, is_admin, is_vendor, created_at) 
                VALUES (:username, :email, :password, :alias, 0, false, :is_vendor, CURRENT_TIMESTAMP)
            ");

            $isVendor = ($accountType === 'seller');

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':alias', $alias);
            $stmt->bindParam(':is_vendor', $isVendor, PDO::PARAM_BOOL);

            if ($stmt->execute()) {
                // Fetch user data since lastInsertId() doesn't work with UUID
                $userStmt = $this->account->prepare("
                    SELECT user_id, username, email, 
                           alias, trustlevel, is_admin, 
                           is_vendor 
                    FROM users WHERE username = :username
                ");
                $userStmt->execute(['username' => $username]);
                $userData = $userStmt->fetch(PDO::FETCH_ASSOC);

                if (!$userData) {
                    return ['success' => false, 'error' => 'Failed to retrieve user data after registration'];
                }

                return [
                    'success' => true,
                    'user_id' => $userData['user_id'],
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'alias' => $userData['alias'],
                    'trustlevel' => $userData['trustlevel'],
                    'is_admin' => $userData['is_admin'],
                    'is_vendor' => $userData['is_vendor']
                ];
            } else {
                return ['success' => false, 'error' => 'Failed to create account'];
            }
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Registration failed: ' . $e->getMessage()];
        }
    }
}
