<?php

namespace App\Utils;

use PDO;

class Auth
{
    private PDO $account;

    public function __construct(PDO $account)
    {
        $this->account = $account;
    }

    public function tryLogin(string $username, string $password): bool
    {
        $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $statement = $this->account->prepare("SELECT * FROM users WHERE {$field} = :username");
        $statement->bindParam(':username', $username);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        // Prevent null or undefined access
        if (!$user || !isset($user['password'])) {
            return false;
        }

        return password_verify($password, $user['password']);
    }

    public function getLoggedInUserID(): ?string
    {
        if (!isset($_SESSION['user'])) {
            return null;
        }

        $username = $_SESSION['user'];
        $statement = $this->account->prepare('SELECT user_id FROM users WHERE username = :username');
        $statement->bindParam(':username', $username);
        $statement->execute();
        $result = $statement->fetch();

        return $result ? $result['user_id'] : null;
    }
}
