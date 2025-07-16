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
        $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'Email' : 'Username';
        $statement = $this->account->prepare("SELECT * FROM users WHERE \"$field\" = :username AND \"Password\" = :password");
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
