<?php
namespace App\Utils;

use PDO;

class Auth {
    private PDO $account;

    public function __construct(PDO $account) {
        $this->account = $account;
    }

    public function tryLogin(string $username, string $password): bool {
        $statement = $this->account->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $statement->bindParam(':username', $username);
        $statement->bindParam(':password', $password);
        $statement->execute();

        if ($statement->rowCount() > 0) {
            return true; // login successful
        } else {
            return false; // login failed
        }
    }
}