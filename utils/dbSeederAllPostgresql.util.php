<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';
require_once 'bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';

echo "🌱 ========================================\n";
echo "🌱 PRIMAL BLACK MARKET - SEED ALL        \n";
echo "🌱 ========================================\n";
echo "🌱 Running all database seeders...\n";
echo "🌱 ========================================\n";

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

// Seeder runner function
function runSeeder($file, $description) {
    echo "\n🌱 Running seeder: {$description}\n";
    echo "   File: {$file}\n";
    
    $fullPath = UTILS_PATH . '/' . $file;
    if (file_exists($fullPath)) {
        // Capture output but don't interfere with the seeder process
        ob_start();
        require $fullPath;
        $output = ob_get_clean();
        
        // Show relevant output lines
        $lines = explode("\n", $output);
        foreach ($lines as $line) {
            if (strpos($line, '✅') !== false || strpos($line, '❌') !== false || strpos($line, '📊') !== false || strpos($line, 'Seeding') !== false) {
                echo "   $line\n";
            }
        }
    } else {
        echo "   ❌ Seeder file not found: $file\n";
    }
}

// ---- 🌱 Run All Seeders in Correct Order ----
echo "\n🌱 Starting seeding process...\n";

// Order is important due to foreign key dependencies
$seeders = [
    ['dbSeederUsersPostgresql.util.php', 'Users Data (Foundation)'],
    ['dbSeederCategoriesPostgresql.util.php', 'Categories Data'],
    ['dbSeederListingsPostgresql.util.php', 'Listings Data (Requires Users)'],
    ['dbSeederFeedbacksPostgresql.util.php', 'Feedbacks Data (Requires Users)'],
    ['dbSeederMessagesPostgresql.util.php', 'Messages Data (Requires Users)'],
    ['dbSeederTransactionsPostgresql.util.php', 'Transactions Data (Requires Users & Listings)']
];

$successCount = 0;
$failureCount = 0;

foreach ($seeders as $seeder) {
    [$file, $description] = $seeder;
    
    try {
        runSeeder($file, $description);
        $successCount++;
    } catch (Exception $e) {
        echo "   ❌ Seeder failed: " . $e->getMessage() . "\n";
        $failureCount++;
    }
}

// ---- 🔍 Verify Seeding Results ----
echo "\n🔍 Verifying seeding results...\n";

$expectedTables = ['users', 'categories', 'listings', 'feedbacks', 'messages', 'transactions'];
$totalRecords = 0;

foreach ($expectedTables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
        $count = $stmt->fetchColumn();
        $totalRecords += $count;
        echo "✅ Table '{$table}' has {$count} records\n";
    } catch (Exception $e) {
        echo "❌ Error checking table '{$table}': " . $e->getMessage() . "\n";
    }
}

// ---- 🎉 Seeding Summary ----
echo "\n🎉 ========================================\n";
echo "🎉 SEEDING COMPLETE!                     \n";
echo "🎉 ========================================\n";
echo "📊 Seeding Summary:\n";
echo "   ✅ Successful seeders: {$successCount}\n";
echo "   ❌ Failed seeders: {$failureCount}\n";
echo "   📋 Total records created: {$totalRecords}\n";
echo "\n✅ Database is now ready for development!\n";
echo "🎉 ========================================\n";
