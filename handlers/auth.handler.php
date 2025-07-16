<?php
require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';
require_once UTILS_PATH . '/auth.util.php';

$dsn = "pgsql:host=" . $_ENV['PG_HOST'] . ";port=" . $_ENV['PG_PORT'] . ";dbname=" . $_ENV['PG_DB'];
$pdo = new PDO($dsn, $_ENV['PG_USER'], $_ENV['PG_PASS']);

if ($_POST['action'] === 'login') {
    $auth = new \App\Utils\Auth($pdo);
    session_start();

    if ($auth->login($_POST['username'], $_POST['password'])) {
        $_SESSION['user'] = $_POST['username'];
        // dashboard or home page
    } else {
        // error page
    }
}
