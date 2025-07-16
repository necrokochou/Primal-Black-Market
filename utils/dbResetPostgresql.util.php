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

// ---- 🧱 Run Migrations ----
echo "\n📦 Running migrations...\n";
$migrationFiles = [
    'migrate.users.php',
    'migrate.categories.php',
    'migrate.listings.php',
    'migrate.feedbacks.php',
    'migrate.transactions.php',
    'migrate.messages.php',
];

foreach ($migrationFiles as $file) {
    $path = MIGRATIONS_PATH . '/' . $file;
    if (file_exists($path)) {
        require_once $path;
    } else {
        echo "⚠️  Migration file missing: $file\n";
    }
}

// ---- 🧹 Truncate Tables ----
echo "\n🧹 Truncating tables...\n";
$tables = [
    'transactions',
    'messages',
    'feedbacks',
    'listings',
    'categories',
    'users'
];

foreach ($tables as $table) {
    try {
        $pdo->exec("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE;");
        echo "✅ Truncated: {$table}\n";
    } catch (PDOException $e) {
        echo "❌ Failed to truncate {$table}: " . $e->getMessage() . "\n";
    }
}

// ---- 🌱 Run Seeders ----
echo "\n🌱 Running seeders...\n";
$seederFiles = [
    'seeder.categories.php',
    'seeder.users.php',
    'seeder.listings.php',
    'seeder.feedbacks.php',
    'seeder.transactions.php',
    'seeder.messages.php',
];

foreach ($seederFiles as $file) {
    $path = SEEDERS_PATH . '/' . $file;
    if (file_exists($path)) {
        require_once $path;
    } else {
        echo "⚠️  Seeder file missing: $file\n";
    }
}

echo "\n🎉 Database reset and seeding complete!\n";
    