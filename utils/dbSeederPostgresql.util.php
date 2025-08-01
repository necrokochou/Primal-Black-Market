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

// Function to clear all tables in correct order (reverse dependency order)
function clearAllTables($pdo) {
    echo "\n🧹 Clearing all tables in correct order...\n";
    $clearOrder = ['purchase_history', 'cart', 'transactions', 'listings', 'categories', 'users'];
    foreach ($clearOrder as $table) {
        try {
            $pdo->exec("DELETE FROM {$table}");
            echo "✅ Cleared {$table} table\n";
        } catch (PDOException $e) {
            echo "⚠️  Could not clear {$table}: " . $e->getMessage() . "\n";
        }
    }

    echo "🧹 Table clearing complete!\n";
}

// Seeder function
function seedTable($pdo, $tableName, $staticDataFile, $insertSQL, $dataProcessor) {
    echo "\n🌱 Seeding table: {$tableName}\n";
    
    // Load dummy data
    $dummyDataPath = DUMMIES_PATH . '/' . $staticDataFile;
    if (!file_exists($dummyDataPath)) {
        echo "⚠️  Static data file not found: {$staticDataFile}\n";
        return 0;
    }
    $dummyData = require_once $dummyDataPath;

    // Insert dummy data
    $insertStmt = $pdo->prepare($insertSQL);

    $insertedCount = 0;
    $skippedCount = 0;

    foreach ($dummyData as $item) {
        try {
            $processedData = $dataProcessor($item);
            $result = $insertStmt->execute($processedData);
            if ($insertStmt->rowCount() > 0) {
                $insertedCount++;
                echo "✅ Inserted {$tableName} item successfully.\n";
            } else {
                $skippedCount++;
                echo "⚠️  {$tableName} item already exists, skipped.\n";
            }
        } catch (PDOException $e) {
            echo "❌ Failed to seed {$tableName} item: " . $e->getMessage() . "\n";
            $skippedCount++;
        }
    }
    echo "📊 {$tableName} seeding complete! {$insertedCount} items inserted, {$skippedCount} items skipped.\n";
    return $insertedCount;
}

// ---- 🌱 Run All Seeders in Correct Order ----
echo "\n🌱 Starting seeding process...\n";

// Clear all tables first to avoid foreign key violations
clearAllTables($pdo);

$totalInserted = 0;

// 1. Users Data (Foundation) - Based on users.model.sql structure
$usersInserted = seedTable(
    $pdo,
    'users',
    'users.staticData.php',
    'INSERT INTO users (Username, Password, Email, Alias, TrustLevel, Created_At, Is_Vendor, Is_Admin, Is_Banned)
     VALUES (:username, :password, :email, :alias, :trustlevel, :created_at, :is_vendor, :is_admin, :is_banned)
     ON CONFLICT (Username) DO NOTHING',
    function($user) {
        return [
            ':username' => $user['Username'],
            ':password' => password_hash($user['Password'], PASSWORD_DEFAULT),
            ':email' => $user['Email'] ?? $user['Username'] . '@example.com',
            ':alias' => $user['Alias'],
            ':trustlevel' => $user['TrustLevel'],
            ':created_at' => $user['Created_At'] ?? date('Y-m-d H:i:s'),
            ':is_vendor' => $user['Is_Vendor'] ? 'true' : 'false',
            ':is_admin' => $user['Is_Admin'] ? 'true' : 'false',
            ':is_banned' => $user['Is_Banned'] ? 'true' : 'false'
        ];
    }
);
$totalInserted += $usersInserted;

// 2. Categories Data - Based on categories.model.sql structure
$categoriesInserted = seedTable(
    $pdo,
    'categories',
    'categories.staticData.php',
    'INSERT INTO categories (Name, Description) VALUES (:name, :description)',
    function($category) {
        return [
            ':name' => $category['Name'],
            ':description' => $category['Description']
        ];
    }
);
$totalInserted += $categoriesInserted;

// 3. Listings Data (Requires Users & Categories) - Based on listings.model.sql structure
echo "\n🌱 Seeding listings with foreign key relationships...\n";

// Get random users and categories for foreign key relationships
$userStmt = $pdo->query("SELECT User_ID FROM users ORDER BY RANDOM() LIMIT 10");
$userIds = $userStmt->fetchAll(PDO::FETCH_COLUMN);

$categoryStmt = $pdo->query("SELECT Categories_ID FROM categories ORDER BY RANDOM() LIMIT 10");
$categoryIds = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($userIds) || empty($categoryIds)) {
    echo "⚠️  Cannot seed listings: Missing users or categories data\n";
    $listingsInserted = 0;
} else {
    $listingsInserted = seedTable(
        $pdo,
        'listings',
        'listings.staticData.php',
        'INSERT INTO listings (Vendor_ID, Categories_ID, Title, Description, Category, Price, Quantity, Is_Active, Publish_Date, Item_Image)
         VALUES (:vendor_id, :categories_id, :title, :description, :category, :price, :quantity, :is_active, :publish_date, :item_image)',
        function($listing) use ($userIds, $categoryIds) {
            return [
                ':vendor_id' => $userIds[array_rand($userIds)],
                ':categories_id' => $categoryIds[array_rand($categoryIds)],
                ':title' => $listing['Title'],
                ':description' => $listing['Description'],
                ':category' => $listing['Category'],
                ':price' => $listing['Price'],
                ':quantity' => $listing['Quantity'],
                ':is_active' => $listing['IsActive'] ? 'true' : 'false',
                ':publish_date' => $listing['PublishDate'],
                ':item_image' => $listing['Item_Image'] ?? null
            ];
        }
    );
}
$totalInserted += $listingsInserted;
// 4. Transactions Data (Requires Users & Listings) - Based on transactions.model.sql structure
echo "\n🌱 Seeding transactions with foreign key relationships...\n";

// Get random users and listings for foreign key relationships
$userStmt = $pdo->query("SELECT User_ID FROM users ORDER BY RANDOM() LIMIT 10");
$userIds = $userStmt->fetchAll(PDO::FETCH_COLUMN);

$listingStmt = $pdo->query("SELECT Listing_ID FROM listings ORDER BY RANDOM() LIMIT 10");
$listingIds = $listingStmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($userIds) || empty($listingIds)) {
    echo "⚠️  Cannot seed transactions: Missing users or listings data\n";
    $transactionsInserted = 0;
} else {
    $transactionsInserted = seedTable(
        $pdo,
        'transactions',
        'transactions.staticData.php',
        'INSERT INTO transactions (Buyer_ID, Listing_ID, Quantity, Total_Price, Transaction_Status, Timestamp)
         VALUES (:buyer_id, :listing_id, :quantity, :total_price, :transaction_status, :timestamp)',
        function($transaction) use ($userIds, $listingIds) {
            return [
                ':buyer_id' => $userIds[array_rand($userIds)],
                ':listing_id' => $listingIds[array_rand($listingIds)],
                ':quantity' => $transaction['Quantity'],
                ':total_price' => $transaction['TotalPrice'],
                ':transaction_status' => $transaction['TransactionStatus'],
                ':timestamp' => $transaction['Timestamp']
            ];
        }
    );
}
$totalInserted += $transactionsInserted;

// 5. Cart Data (Requires Users & Listings) - Based on cart.model.sql structure
echo "\n🌱 Seeding cart with foreign key relationships...\n";

// Get random users and listings for foreign key relationships
$userStmt = $pdo->query("SELECT User_ID FROM users ORDER BY RANDOM() LIMIT 10");
$userIds = $userStmt->fetchAll(PDO::FETCH_COLUMN);

$listingStmt = $pdo->query("SELECT Listing_ID FROM listings ORDER BY RANDOM() LIMIT 10");
$listingIds = $listingStmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($userIds) || empty($listingIds)) {
    echo "⚠️  Cannot seed cart: Missing users or listings data\n";
    $cartInserted = 0;
} else {
    $cartInserted = seedTable(
        $pdo,
        'cart',
        'cart.staticData.php',
        'INSERT INTO cart (User_ID, Listing_ID, Quantity, Added_At)
         VALUES (:user_id, :listing_id, :quantity, :added_at)',
        function($cartItem) use ($userIds, $listingIds) {
            return [
                ':user_id' => $userIds[array_rand($userIds)],
                ':listing_id' => $listingIds[array_rand($listingIds)],
                ':quantity' => $cartItem['Quantity'],
                ':added_at' => $cartItem['AddedAt']
            ];
        }
    );
}
$totalInserted += $cartInserted;

// 8. Purchase History Data (Requires Users, Listings & Transactions) - Based on purchase_history.model.sql structure
echo "\n🌱 Seeding purchase_history with foreign key relationships...\n";

// Get random users, listings, and transactions for foreign key relationships
$userStmt = $pdo->query("SELECT User_ID FROM users ORDER BY RANDOM() LIMIT 10");
$userIds = $userStmt->fetchAll(PDO::FETCH_COLUMN);

$listingStmt = $pdo->query("SELECT Listing_ID FROM listings ORDER BY RANDOM() LIMIT 10");
$listingIds = $listingStmt->fetchAll(PDO::FETCH_COLUMN);

$transactionStmt = $pdo->query("SELECT Transaction_ID FROM transactions ORDER BY RANDOM() LIMIT 10");
$transactionIds = $transactionStmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($userIds) || empty($listingIds) || empty($transactionIds)) {
    echo "⚠️  Cannot seed purchase_history: Missing users, listings, or transactions data\n";
    $purchaseHistoryInserted = 0;
} else {
    $purchaseHistoryInserted = seedTable(
        $pdo,
        'purchase_history',
        'purchase_history.staticData.php',
        'INSERT INTO purchase_history (User_ID, Listing_ID, Transaction_ID, Quantity, Price_Each, Total_Price, Purchase_Date, Payment_Method, Delivery_Status, Notes)
         VALUES (:user_id, :listing_id, :transaction_id, :quantity, :price_each, :total_price, :purchase_date, :payment_method, :delivery_status, :notes)',
        function($purchaseItem) use ($userIds, $listingIds, $transactionIds) {
            return [
                ':user_id' => $userIds[array_rand($userIds)],
                ':listing_id' => $listingIds[array_rand($listingIds)],
                ':transaction_id' => $transactionIds[array_rand($transactionIds)],
                ':quantity' => $purchaseItem['Quantity'],
                ':price_each' => $purchaseItem['Price_Each'],
                ':total_price' => $purchaseItem['Total_Price'],
                ':purchase_date' => $purchaseItem['Purchase_Date'],
                ':payment_method' => $purchaseItem['Payment_Method'],
                ':delivery_status' => $purchaseItem['Delivery_Status'],
                ':notes' => $purchaseItem['Notes']
            ];
        }
    );
}
$totalInserted += $purchaseHistoryInserted;

// ---- 🔍 Verify Seeding Results ----
echo "\n🔍 Verifying seeding results...\n";

$expectedTables = ['users', 'categories', 'listings', 'transactions', 'cart', 'purchase_history'];
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
echo "   ✅ Total records inserted: {$totalInserted}\n";
echo "   📋 Total records in database: {$totalRecords}\n";
echo "   🎯 All tables seeded successfully!\n";
echo "\n➡️  Database is now ready for use!\n";
echo "🎉 ========================================\n";
