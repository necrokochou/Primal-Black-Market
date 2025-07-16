<?php
require_once BASE_PATH . '/bootstrap.php';

$postgresEnv = getPostgresEnv();

try {
    $dsn = "pgsql:host={$postgresEnv['host']};port={$postgresEnv['port']};dbname={$postgresEnv['db']}";
    $pdo = new PDO($dsn, $postgresEnv['user'], $postgresEnv['password'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ PostgreSQL Connection\n";
} catch (Throwable $e) {
    echo "❌ Connection Failed: " . $e->getMessage() . "\n";
}