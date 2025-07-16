<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';
require_once 'bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';

echo "🚨 ========================================\n";
echo "🚨 PRIMAL BLACK MARKET DATABASE RESET    \n";
echo "🚨 ========================================\n";
echo "⚠️  THIS WILL DELETE ALL DATABASE DATA!\n";
echo "⚠️  PRESS CTRL+C TO CANCEL IN 5 SECONDS\n";
echo "🚨 ========================================\n";

// Countdown warning
for ($i = 5; $i > 0; $i--) {
    echo "⏱️  Resetting in {$i} seconds...\n";
    sleep(1);
}

echo "\n🔥 Starting database reset process...\n";

// Get PostgreSQL configuration
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

// ---- 🧹 Database Reset ----
echo "\n🧹 Resetting database...\n";

// Drop all tables individually
echo "🗂️  Dropping tables individually...\n";
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
        $pdo->exec("DROP TABLE IF EXISTS {$table} CASCADE;");
        echo "✅ Dropped table: {$table}\n";
    } catch (PDOException $e) {
        echo "⚠️  Failed to drop {$table}: " . $e->getMessage() . "\n";
    }
}

// Verify reset was successful
echo "\n🔍 Verifying reset...\n";
$stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
$remainingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($remainingTables)) {
    echo "✅ All tables successfully removed\n";
} else {
    echo "⚠️  Some tables may still exist: " . implode(', ', $remainingTables) . "\n";
    echo "🧹 Performing schema reset as backup...\n";
    
    // Backup method: Drop entire schema if individual drops failed
    $pdo->exec("DROP SCHEMA IF EXISTS public CASCADE;");
    echo "✅ Dropped public schema\n";
    
    $pdo->exec("CREATE SCHEMA public;");
    echo "✅ Recreated public schema\n";
    
    $pdo->exec("GRANT ALL ON SCHEMA public TO postgres;");
    $pdo->exec("GRANT ALL ON SCHEMA public TO public;");
    echo "✅ Set schema permissions\n";
}

// ---- 🎉 Reset Complete ----
echo "\n🎉 ========================================\n";
echo "🎉 DATABASE RESET COMPLETE!              \n";
echo "🎉 ========================================\n";
echo "🧹 All tables and data have been deleted\n";
echo "📋 Database is now empty and ready\n";
echo "➡️  Next steps:\n";
echo "   1. Run migrations: php utils/dbMigrateAllPostgresql.util.php\n";
echo "   2. Run seeders: php utils/dbSeederAllPostgresql.util.php\n";
echo "🎉 ========================================\n";
