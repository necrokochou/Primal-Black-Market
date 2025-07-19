<?php
require_once BASE_PATH . '/utils/register.util.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $alias = trim($_POST['alias'] ?? '');

    if (!$username || !$password || !$email || !$alias) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address.");
    }

    $success = registerUser($username, $password, $email, $alias);

    if ($success) {
        echo "Registration successful. You may now <a href='login.php'>log in</a>.";
    } else {
        echo "Username already taken.";
    }
} else {
    header("Location: /register.html");
    exit;
}
