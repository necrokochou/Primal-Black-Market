<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';
require_once 'bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';

// Define database schema files path
define('DATABASE_PATH', BASE_PATH . '/database');

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

// Migration function
function runTableMigration($pdo, $tableName, $sqlFile) {
    echo "\n📄 Creating table: {$tableName} from {$sqlFile}\n";
    
    // Check if SQL file exists
    $sqlFilePath = DATABASE_PATH . '/' . $sqlFile;
    if (!file_exists($sqlFilePath)) {
        echo "❌ SQL file not found: {$sqlFile}\n";
        return false;
    }
    
    // Read SQL content from file
    $createTableSQL = file_get_contents($sqlFilePath);
    if ($createTableSQL === false) {
        echo "❌ Failed to read SQL file: {$sqlFile}\n";
        return false;
    }
    
    // Clean up SQL (remove comments, extra whitespace)
    $createTableSQL = preg_replace('/--.*$/m', '', $createTableSQL); // Remove SQL comments
    $createTableSQL = preg_replace('/\s+/', ' ', $createTableSQL); // Normalize whitespace
    $createTableSQL = trim($createTableSQL);
    
    try {
        // Drop table if it exists to ensure we get the latest schema
        $dropSQL = "DROP TABLE IF EXISTS {$tableName} CASCADE";
        $pdo->exec($dropSQL);
        
        // Create the table with the current schema
        $pdo->exec($createTableSQL);
        echo "✅ Table '{$tableName}' created successfully from {$sqlFile}\n";
        return true;
    } catch (PDOException $e) {
        echo "❌ Failed to create table '{$tableName}': " . $e->getMessage() . "\n";
        echo "📄 SQL content: " . substr($createTableSQL, 0, 200) . "...\n";
        return false;
    }
}

// ---- 🧱 Run All Migrations in Correct Order ----
echo "\n🧱 Starting migration process...\n";

$successCount = 0;
$failureCount = 0;

// 1. Users Table (Foundation) - Based on users.model.sql
if (runTableMigration($pdo, 'users', 'users.model.sql')) {
    $successCount++;
} else {
    $failureCount++;
}

// 2. Categories Table - Based on categories.model.sql
if (runTableMigration($pdo, 'categories', 'categories.model.sql')) {
    $successCount++;
} else {
    $failureCount++;
}

// 3. Listings Table (Requires Users & Categories) - Based on listings.model.sql
if (runTableMigration($pdo, 'listings', 'listings.model.sql')) {
    $successCount++;
} else {
    $failureCount++;
}

// 4. Feedback Table (Requires Users) - Based on feedbacks.model.sql
if (runTableMigration($pdo, 'feedbacks', 'feedbacks.model.sql')) {
    $successCount++;
} else {
    $failureCount++;
}

// 5. Messages Table (Requires Users) - Based on messages.model.sql
if (runTableMigration($pdo, 'messages', 'messages.model.sql')) {
    $successCount++;
} else {
    $failureCount++;
}

// 6. Transactions Table (Requires Users & Listings) - Based on transactions.model.sql
if (runTableMigration($pdo, 'transactions', 'transactions.model.sql')) {
    $successCount++;
} else {
    $failureCount++;
}

// 7. Cart Table (Requires Users & Listings) - Based on cart.model.sql
if (runTableMigration($pdo, 'cart', 'cart.model.sql')) {
    $successCount++;
} else {
    $failureCount++;
}

// 8. Purchase History Table (Requires Users, Listings & Transactions) - Based on purchase_history.model.sql
if (runTableMigration($pdo, 'purchase_history', 'purchase_history.model.sql')) {
    $successCount++;
} else {
    $failureCount++;
}

// ---- 🔍 Verify Migration Results ----
echo "\n🔍 Verifying migration results...\n";

$expectedTables = ['users', 'categories', 'listings', 'feedbacks', 'messages', 'transactions', 'cart', 'purchase_history'];
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
echo "   📋 Tables created: {$tablesCreated}/8\n";
echo "\n➡️  Next step: Run seeders to populate data\n";
echo "   Command: php utils/dbSeederPostgresql.util.php\n";
echo "🎉 ========================================\n";
