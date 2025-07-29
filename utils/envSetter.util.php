<?php
// Remove circular dependency - bootstrap.php should be loaded before this file
if (!defined('BASE_PATH')) {
    throw new Exception('BASE_PATH not defined. Include bootstrap.php first.');
}
require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

function getPostgresEnv(): array {
    return [
        'host' => $_ENV['PG_HOST'] ?? 'postgresql',
        'port' => $_ENV['PG_PORT'] ?? 5432,
        'db' => $_ENV['PG_DB'] ?? 'primal-black-market',
        'user' => $_ENV['PG_USER'] ?? 'user',
        'password' => $_ENV['PG_PASS'] ?? 'password',
    ];
}

function getMongoEnv(): array {
    return [
        'uri' => $_ENV['MONGO_URI'] ?? 'mongodb://root:rootPassword@mongodb:27111',
        'host' => $_ENV['MONGO_HOST'] ?? 'mongodb',
        'port' => $_ENV['MONGO_PORT'] ?? 27017,
        'db' => $_ENV['MONGO_DB'] ?? 'primal-black-market',
        'user' => $_ENV['MONGO_USER'] ?? 'root',
        'password' => $_ENV['MONGO_PASS'] ?? 'rootPassword',
    ];
}