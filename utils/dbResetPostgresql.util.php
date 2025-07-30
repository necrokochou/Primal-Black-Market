<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';
require_once 'bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';

echo "🚨 ========================================\n";
echo "🚨 PRIMAL BLACK MARKET DATABASE RESET    \n";
echo "🚨 ========================================\n";
echo "⚠️  THIS WILL DROP ALL TABLES COMPLETELY!\n";
echo "⚠️  ALL DATA AND TABLE STRUCTURES WILL BE DESTROYED\n";
echo "⚠️  PRESS CTRL+C TO CANCEL IN 5 SECONDS\n";
echo "🚨 ========================================\n";

// Countdown warning
for ($i = 5; $i > 0; $i--) {
    echo "⏱️  Dropping tables in {$i} seconds...\n";
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

// ---- 🔥 Database Reset (DROP TABLES) ----
echo "\n🔥 Dropping all tables...\n";

// Drop all tables in reverse dependency order (children first, parents last)
echo "🗂️  Dropping tables in dependency order...\n";
$tables = [
    'purchase_history',  // Has FK to users, listings, transactions
    'cart',             // Has FK to users, listings
    'transactions',     // Has FK to users, listings
    'listings',         // Has FK to users, categories
    'categories',       // No dependencies
    'users'            // No dependencies (base table)
];

foreach ($tables as $table) {
    try {
        $pdo->exec("DROP TABLE IF EXISTS {$table} CASCADE;");
        echo "✅ Dropped table: {$table}\n";
    } catch (PDOException $e) {
        echo "❌ Failed to drop {$table}: " . $e->getMessage() . "\n";
    }
}

// Verify all tables are dropped
echo "\n🔍 Verifying tables are dropped...\n";
$stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
$remainingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($remainingTables)) {
    echo "✅ All tables successfully dropped from database\n";
} else {
    echo "⚠️  Some tables still exist: " . implode(', ', $remainingTables) . "\n";
    foreach ($remainingTables as $table) {
        echo "   - {$table}\n";
    }
}

// ---- 🎉 Reset Complete ----
echo "\n🎉 ========================================\n";
echo "🎉 DATABASE RESET COMPLETE!              \n";
echo "🎉 ========================================\n";
echo "🔥 All tables have been completely dropped\n";
echo "📋 Database is now empty and clean\n";
echo "🆕 Database is ready for fresh schema\n";
echo "➡️  Next steps:\n";
echo "   1. REQUIRED: Run migrations first: php utils/dbMigratePostgresql.util.php\n";
echo "   2. THEN: Run seeders: php utils/dbSeederPostgresql.util.php\n";
echo "🎉 ========================================\n";