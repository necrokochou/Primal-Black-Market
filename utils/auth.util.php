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
        $statement = $this->account->prepare("SELECT * FROM users WHERE \"$field\" = :username");
        $statement->bindParam(':username', $username);
        $statement->execute();
        $user = $statement->fetch();
        return $user && password_verify($password, $user['Password']);
    }

    public function getLoggedInUserID(): ?string
    {
        if (!isset($_SESSION['user'])) {
            return null;
        }

        $username = $_SESSION['user'];
        $statement = $this->account->prepare('SELECT "UserID" FROM users WHERE "Username" = :username');
        $statement->bindParam(':username', $username);
        $statement->execute();
        $result = $statement->fetch();

        return $result ? $result['UserID'] : null;
    }
}
