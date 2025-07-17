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

// Migration function
function runTableMigration($pdo, $tableName, $createTableSQL) {
    echo "\n📄 Creating table: {$tableName}\n";
    
    try {
        $pdo->exec($createTableSQL);
        echo "✅ Table '{$tableName}' created successfully.\n";
        return true;
    } catch (PDOException $e) {
        echo "❌ Failed to create table '{$tableName}': " . $e->getMessage() . "\n";
        return false;
    }
}

// ---- 🧱 Run All Migrations in Correct Order ----
echo "\n🧱 Starting migration process...\n";

$successCount = 0;
$failureCount = 0;

// 1. Users Table (Foundation) - Based on users.model.sql
$createUsersTable = '
CREATE TABLE IF NOT EXISTS users (
    User_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    Username varchar(256) UNIQUE NOT NULL,
    Password varchar(256) NOT NULL,
    Email varchar(256) NOT NULL,
    Alias varchar(256) NOT NULL,
    TrustLevel real DEFAULT 0,
    Is_Vendor boolean DEFAULT FALSE,
    Is_Admin boolean DEFAULT FALSE
);
';

if (runTableMigration($pdo, 'users', $createUsersTable)) {
    $successCount++;
} else {
    $failureCount++;
}

// 2. Categories Table - Based on categories.model.sql
$createCategoriesTable = '
CREATE TABLE IF NOT EXISTS categories (
    Categories_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    Name varchar(256) UNIQUE NOT NULL,
    Description text NOT NULL
);
';

if (runTableMigration($pdo, 'categories', $createCategoriesTable)) {
    $successCount++;
} else {
    $failureCount++;
}

// 3. Listings Table (Requires Users & Categories) - Based on listings.model.sql
$createListingsTable = '
CREATE TABLE IF NOT EXISTS listings (
    Listing_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    Vendor_ID uuid NOT NULL,
    Categories_ID uuid NOT NULL,
    Title varchar(256) NOT NULL,
    Description text NOT NULL,
    Category varchar(100) NOT NULL,
    Price real NOT NULL,
    Quantity int NOT NULL,
    Is_Active boolean DEFAULT TRUE,
    Publish_Date date NOT NULL,
    FOREIGN KEY (Vendor_ID) REFERENCES users(User_ID),
    FOREIGN KEY (Categories_ID) REFERENCES categories(Categories_ID)
);
';

if (runTableMigration($pdo, 'listings', $createListingsTable)) {
    $successCount++;
} else {
    $failureCount++;
}

// 4. Feedback Table (Requires Users) - Based on feedbacks.model.sql
$createFeedbackTable = '
CREATE TABLE IF NOT EXISTS feedback (
    Feedback_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    Reviewer_ID uuid NOT NULL,
    Vendor_ID uuid NOT NULL,
    Rating int CHECK (Rating BETWEEN 0 AND 5),
    Comments text NOT NULL,
    Posted_At date NOT NULL,
    FOREIGN KEY (Reviewer_ID) REFERENCES users(User_ID),
    FOREIGN KEY (Vendor_ID) REFERENCES users(User_ID)
);
';

if (runTableMigration($pdo, 'feedback', $createFeedbackTable)) {
    $successCount++;
} else {
    $failureCount++;
}

// 5. Messages Table (Requires Users) - Based on messages.model.sql
$createMessagesTable = '
CREATE TABLE IF NOT EXISTS messages (
    Message_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    Sender_ID uuid NOT NULL,
    Receiver_ID uuid NOT NULL,
    Messages_Content text,
    Sent_At date NOT NULL,
    FOREIGN KEY (Sender_ID) REFERENCES users(User_ID),
    FOREIGN KEY (Receiver_ID) REFERENCES users(User_ID)
);
';

if (runTableMigration($pdo, 'messages', $createMessagesTable)) {
    $successCount++;
} else {
    $failureCount++;
}

// 6. Transactions Table (Requires Users & Listings) - Based on transactions.model.sql
$createTransactionsTable = '
CREATE TABLE IF NOT EXISTS transactions (
    Transaction_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    Buyer_ID uuid NOT NULL,
    Listing_ID uuid NOT NULL,
    Quantity int NOT NULL,
    Total_Price int NOT NULL,
    Transaction_Status varchar(30) NOT NULL,
    Timestamp date NOT NULL,
    FOREIGN KEY (Buyer_ID) REFERENCES users(User_ID),
    FOREIGN KEY (Listing_ID) REFERENCES listings(Listing_ID)
);
';

if (runTableMigration($pdo, 'transactions', $createTransactionsTable)) {
    $successCount++;
} else {
    $failureCount++;
}

// ---- 🔍 Verify Migration Results ----
echo "\n🔍 Verifying migration results...\n";

$expectedTables = ['users', 'categories', 'listings', 'feedback', 'messages', 'transactions'];
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
echo "   Command: php utils/dbSeederPostgresql.util.php\n";
echo "🎉 ========================================\n";
