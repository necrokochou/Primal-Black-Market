<?php
require_once BASE_PATH . '/bootstrap.php';

function getPostgresEnv(): array {
    return [
        'host' => $_ENV['PG_HOST'],
        'port' => $_ENV['PG_PORT'],
        'db' => $_ENV['PG_DB'],
        'user' => $_ENV['PG_USER'],
        'password' => $_ENV['PG_PASS'],
    ];
}

function getMongoEnv(): array {
    return [
        'uri' => $_ENV['MONGO_URI'],
        'host' => $_ENV['MONGO_HOST'],
        'port' => $_ENV['MONGO_PORT'],
        'db' => $_ENV['MONGO_DB'],
        'user' => $_ENV['MONGO_USER'],
        'password' => $_ENV['MONGO_PASS'],
    ];
}