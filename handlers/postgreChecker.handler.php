<?php
require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';

try {
    $dsn = "pgsql:host={$databases['pgHost']};port={$databases['pgPort']};dbname={$databases['pgDB']}";
    $pdo = new PDO($dsn, $databases['pgUser'], $databases['pgPassword'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ PostgreSQL Connection\n";
} catch (Throwable $e) {
    echo "❌ Connection Failed: " . $e->getMessage() . "\n";
}