<?php
require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';

$postgres = getPostgresEnv();

try {
    $dsn = "pgsql:host={$postgres['host']};port={$postgres['port']};dbname={$postgres['db']}";
    $pdo = new PDO($dsn, $postgres['user'], $postgres['password'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ PostgreSQL Connection\n";
} catch (Throwable $e) {
    echo "❌ Connection Failed: " . $e->getMessage() . "\n";
}