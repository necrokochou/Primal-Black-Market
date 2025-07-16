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

// ---- 🧹 Step 1: Truncate Tables ----
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

// ---- 🧱 Step 2: Run Migrations ----
echo "\n📦 Running migrations...\n";
$migrationFiles = [
    'dbMigrateUsersPostgresql.util.php',
    'dbMigrateCategoriesPostgresql.util.php',
    'dbMigrateListingsPostgresql.util.php',
    'dbMigrateFeedbacksPostgresql.util.php',
    'dbMigrateTransactionsPostgresql.util.php',
    'dbMigrateMessagesPostgresql.util.php',
];

foreach ($migrationFiles as $file) {
    $path = MIGRATIONS_PATH . '/' . $file;
    if (file_exists($path)) {
        require_once $path;
    } else {
        echo "⚠️  Migration file missing: $file\n";
    }
}

// ---- 🌱 Step 3: Run Seeders ----
echo "\n🌱 Running seeders...\n";
$seederFiles = [
    'dbSeederCategoriesPostgresql.util.php',
    'dbSeederUsersPostgresql.util.php',
    'dbSeederListingsPostgresql.util.php',
    'dbSeederFeedbacksPostgresql.util.php',
    'dbSeederTransactionsPostgresql.util.php',
    'dbSeederMessagesPostgresql.util.php',
];

foreach ($seederFiles as $file) {
    $path = SEEDERS_PATH . '/' . $file;
    if (file_exists($path)) {
        require_once $path;
    } else {
        echo "⚠️  Seeder file missing: $file\n";
    }
}

echo "\n🎉 PostgreSQL database reset, migration, and seeding complete!\n";
