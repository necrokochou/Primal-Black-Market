<?php
namespace App\Utils;

use PDO;

class Auth {
    private PDO $account;

    public function __construct(PDO $account) {
        $this->account = $account;
    }

    public function tryLogin(string $username, string $password): bool {
        $stmt = $this->account->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true; // Login successful
        } else {
            return false; // Login failed
        }
    }
}