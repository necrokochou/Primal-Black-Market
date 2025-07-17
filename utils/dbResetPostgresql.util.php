<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';
require_once 'bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';

echo "🚨 ========================================\n";
echo "🚨 PRIMAL BLACK MARKET DATABASE RESET    \n";
echo "🚨 ========================================\n";
echo "⚠️  THIS WILL CLEAR ALL TABLE DATA!\n";
echo "⚠️  TABLES WILL BE KEPT, ONLY DATA CLEARED\n";
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
echo "\n🧹 Clearing table data...\n";

// Clear all tables individually (preserve structure)
echo "🗂️  Clearing tables individually...\n";
$tables = [
    'transactions',
    'messages', 
    'feedback',
    'listings',
    'categories',
    'users'
];

foreach ($tables as $table) {
    try {
        // Check if table exists first
        $stmt = $pdo->prepare("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_schema = 'public' AND table_name = ?)");
        $stmt->execute([$table]);
        $tableExists = $stmt->fetchColumn();
        
        if ($tableExists) {
            // Use TRUNCATE for faster clearing with CASCADE to handle foreign keys
            $pdo->exec("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE;");
            echo "✅ Cleared table: {$table}\n";
        } else {
            echo "⏭️  Skipped table: {$table} (does not exist)\n";
        }
    } catch (PDOException $e) {
        echo "⚠️  Failed to clear {$table}: " . $e->getMessage() . "\n";
        // Try alternative DELETE method if TRUNCATE fails
        try {
            if ($tableExists) {
                $pdo->exec("DELETE FROM {$table};");
                echo "✅ Cleared table using DELETE: {$table}\n";
            }
        } catch (PDOException $deleteError) {
            echo "❌ Could not clear {$table} with DELETE either: " . $deleteError->getMessage() . "\n";
        }
    }
}

// Verify reset was successful
echo "\n🔍 Verifying data clearance...\n";
$stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
$existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!empty($existingTables)) {
    echo "✅ Tables preserved: " . implode(', ', $existingTables) . "\n";
    
    // Check if tables are actually empty
    $allEmpty = true;
    foreach ($existingTables as $table) {
        try {
            $countStmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
            $count = $countStmt->fetchColumn();
            if ($count > 0) {
                echo "⚠️  Table {$table} still has {$count} records\n";
                $allEmpty = false;
            } else {
                echo "✅ Table {$table} is empty\n";
            }
        } catch (PDOException $e) {
            echo "⚠️  Could not verify {$table}: " . $e->getMessage() . "\n";
        }
    }
    
    if ($allEmpty) {
        echo "✅ All tables successfully cleared of data\n";
    } else {
        echo "⚠️  Some tables may still contain data\n";
    }
} else {
    echo "⚠️  No tables found in the database\n";
}

// ---- 🎉 Reset Complete ----
echo "\n🎉 ========================================\n";
echo "🎉 DATABASE RESET COMPLETE!              \n";
echo "🎉 ========================================\n";
echo "🧹 All table data has been cleared\n";
echo "📋 Table structures are preserved\n";
echo "� Database is ready for fresh data\n";
echo "➡️  Next steps:\n";
echo "   1. Run seeders: php utils/dbSeederPostgresql.util.php\n";
echo "   2. Or run migrations first if tables don't exist: php utils/dbMigratePostgresql.util.php\n";
echo "🎉 ========================================\n";
