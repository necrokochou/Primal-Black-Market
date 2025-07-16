<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';
require_once 'bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';

$pgConfig = getPostgresEnv();
$dsn = "pgsql:host={$pgConfig['host']};port={$pgConfig['port']};dbname={$pgConfig['db']}";

try {
    $pdo = new PDO($dsn, $pgConfig['user'], $pgConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✅ Connected to PostgreSQL successfully.\n";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

// Tables in correct truncation order (child → parent)
$tables = ['transactions', 'messages', 'feedbacks', 'listings', 'categories', 'users'];

echo "\n🧹 Truncating tables...\n";
foreach ($tables as $table) {
    try {
        $pdo->exec("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE;");
        echo "✅ Truncated: {$table}\n";
    } catch (PDOException $e) {
        echo "❌ Failed to truncate {$table}: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ PostgreSQL reset complete (data only).\n";
