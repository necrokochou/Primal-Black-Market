<?php
require_once __DIR__ . '/../bootstrap.php';

chdir(BASE_PATH);

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

function getPostgresEnv(): array {
    // Check if running inside Docker container
    $isDocker = isset($_ENV['DOCKER_ENV']) || file_exists('/.dockerenv');
    
    if ($isDocker) {
        // Use Docker service names and internal ports
        return [
            'host' => 'postgresql',
            'port' => '5432',
            'db' => 'primal-black-market',
            'user' => 'user',
            'password' => 'password',
        ];
    } else {
        // Use host machine configuration
        return [
            'host' => $_ENV['PG_HOST'] ?? 'host.docker.internal',
            'port' => $_ENV['PG_PORT'] ?? '5111',
            'db' => $_ENV['PG_DB'] ?? 'primal-black-market',
            'user' => $_ENV['PG_USER'] ?? 'user',
            'password' => $_ENV['PG_PASS'] ?? 'password',
        ];
    }
}

function getMongoEnv(): array {
    // Check if running inside Docker container
    $isDocker = isset($_ENV['DOCKER_ENV']) || file_exists('/.dockerenv');
    
    if ($isDocker) {
        // Use Docker service names and internal ports
        return [
            'uri' => 'mongodb://root:rootPassword@mongodb:27017',
            'host' => 'mongodb',
            'port' => '27017',
            'db' => 'primal-black-market',
            'user' => 'root',
            'password' => 'rootPassword',
        ];
    } else {
        // Use host machine configuration
        return [
            'uri' => $_ENV['MONGO_URI'] ?? 'mongodb://host.docker.internal:27111',
            'host' => $_ENV['MONGO_HOST'] ?? 'host.docker.internal',
            'port' => $_ENV['MONGO_PORT'] ?? '27111',
            'db' => $_ENV['MONGO_DB'] ?? 'primal-black-market',
            'user' => $_ENV['MONGO_USER'] ?? 'root',
            'password' => $_ENV['MONGO_PASS'] ?? 'rootPassword',
        ];
    }
}