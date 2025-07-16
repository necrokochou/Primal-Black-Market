<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';
require_once 'bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';

echo "📦 ========================================\n";
echo "📦 PRIMAL BLACK MARKET - MIGRATE ALL     \n";
echo "📦 ========================================\n";
echo "🧱 Running all database migrations...\n";
echo "📦 ========================================\n";

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

// Migration runner function
function runMigration($file, $description) {
    echo "\n📄 Running migration: {$description}\n";
    echo "   File: {$file}\n";
    
    $fullPath = UTILS_PATH . '/' . $file;
    if (file_exists($fullPath)) {
        // Capture output but don't interfere with the migration process
        ob_start();
        require $fullPath;
        $output = ob_get_clean();
        
        // Show relevant output lines
        $lines = explode("\n", $output);
        foreach ($lines as $line) {
            if (strpos($line, '✅') !== false || strpos($line, '❌') !== false || strpos($line, 'Creating') !== false) {
                echo "   $line\n";
            }
        }
    } else {
        echo "   ❌ Migration file not found: $file\n";
    }
}

// ---- 🧱 Run All Migrations in Correct Order ----
echo "\n🧱 Starting migration process...\n";

// Order is important due to foreign key dependencies
$migrations = [
    ['dbMigrateUsersPostgresql.util.php', 'Users Table (Foundation)'],
    ['dbMigrateCategoriesPostgresql.util.php', 'Categories Table'],
    ['dbMigrateListingsPostgresql.util.php', 'Listings Table (Requires Users)'],
    ['dbMigrateFeedbacksPostgresql.util.php', 'Feedbacks Table (Requires Users)'],
    ['dbMigrateMessagesPostgresql.util.php', 'Messages Table (Requires Users)'],
    ['dbMigrateTransactionsPostgresql.util.php', 'Transactions Table (Requires Users & Listings)']
];

$successCount = 0;
$failureCount = 0;

foreach ($migrations as $migration) {
    [$file, $description] = $migration;
    
    try {
        runMigration($file, $description);
        $successCount++;
    } catch (Exception $e) {
        echo "   ❌ Migration failed: " . $e->getMessage() . "\n";
        $failureCount++;
    }
}

// ---- 🔍 Verify Migration Results ----
echo "\n🔍 Verifying migration results...\n";

$expectedTables = ['users', 'categories', 'listings', 'feedbacks', 'messages', 'transactions'];
$tablesCreated = 0;

foreach ($expectedTables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
        $count = $stmt->fetchColumn();
        echo "✅ Table '{$table}' exists (currently {$count} records)\n";
        $tablesCreated++;
    } catch (Exception $e) {
        echo "❌ Table '{$table}' not found or has error\n";
    }
}

// ---- 🎉 Migration Summary ----
echo "\n🎉 ========================================\n";
echo "🎉 MIGRATION COMPLETE!                   \n";
echo "🎉 ========================================\n";
echo "📊 Migration Summary:\n";
echo "   ✅ Successful migrations: {$successCount}\n";
echo "   ❌ Failed migrations: {$failureCount}\n";
echo "   📋 Tables created: {$tablesCreated}/6\n";
echo "\n➡️  Next step: Run seeders to populate data\n";
echo "   Command: php utils/dbSeederAllPostgresql.util.php\n";
echo "🎉 ========================================\n";
