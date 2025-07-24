<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';
require_once UTILS_PATH . '/register.util.php';

header('Content-Type: application/json');

if (!isset($_GET['email'])) {
    echo json_encode(['available' => false, 'error' => 'Email not provided']);
    exit;
}

$email = $_GET['email'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['available' => false, 'error' => 'Invalid email format']);
    exit;
}

$isTaken = isEmailTaken($email);

echo json_encode(['available' => !$isTaken]);
