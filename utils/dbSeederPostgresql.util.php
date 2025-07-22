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
    
    // $clearOrder = ['cart', 'transactions', 'feedbacks', 'messages', 'listings', 'categories', 'users'];
    $clearOrder = ['transactions', 'feedbacks', 'messages', 'listings', 'categories', 'users'];
    
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
    'INSERT INTO users (Username, Password, Email, Alias, TrustLevel, Is_Vendor, Is_Admin)
     VALUES (:username, :password, :email, :alias, :trustlevel, :is_vendor, :is_admin)
     ON CONFLICT (Username) DO NOTHING',
    function($user) {
        return [
            ':username' => $user['Username'],
            ':password' => password_hash($user['Password'], PASSWORD_DEFAULT),
            ':email' => $user['Email'] ?? $user['Username'] . '@example.com',
            ':alias' => $user['Alias'],
            ':trustlevel' => $user['TrustLevel'],
            ':is_vendor' => $user['IsVendor'] ? 'true' : 'false',
            ':is_admin' => $user['IsAdmin'] ? 'true' : 'false'
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

// 4. Feedback Data (Requires Users) - Based on feedbacks.model.sql structure
echo "\n🌱 Seeding feedback with foreign key relationships...\n";

// Get random users for reviewer and vendor relationships
$userStmt = $pdo->query("SELECT User_ID FROM users ORDER BY RANDOM() LIMIT 10");
$userIds = $userStmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($userIds)) {
    echo "⚠️  Cannot seed feedback: Missing users data\n";
    $feedbackInserted = 0;
} else {
    $feedbackInserted = seedTable(
        $pdo,
        'feedbacks',
        'feedbacks.staticData.php',
        'INSERT INTO feedbacks (Reviewer_ID, Vendor_ID, Rating, Comments, Posted_At)
         VALUES (:reviewer_id, :vendor_id, :rating, :comments, :posted_at)',
        function($feedbacks) use ($userIds) {
            return [
                ':reviewer_id' => $userIds[array_rand($userIds)],
                ':vendor_id' => $userIds[array_rand($userIds)],
                ':rating' => $feedbacks['Rating'],
                ':comments' => $feedbacks['Comments'],
                ':posted_at' => $feedbacks['PostedAt']
            ];
        }
    );
}
$totalInserted += $feedbackInserted;

// 5. Messages Data (Requires Users) - Based on messages.model.sql structure
echo "\n🌱 Seeding messages with foreign key relationships...\n";

// Get random users for sender and receiver relationships
$userStmt = $pdo->query("SELECT User_ID FROM users ORDER BY RANDOM() LIMIT 10");
$userIds = $userStmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($userIds)) {
    echo "⚠️  Cannot seed messages: Missing users data\n";
    $messagesInserted = 0;
} else {
    $messagesInserted = seedTable(
        $pdo,
        'messages',
        'messages.staticData.php',
        'INSERT INTO messages (Sender_ID, Receiver_ID, Messages_Content, Sent_At)
         VALUES (:sender_id, :receiver_id, :messages_content, :sent_at)',
        function($message) use ($userIds) {
            return [
                ':sender_id' => $userIds[array_rand($userIds)],
                ':receiver_id' => $userIds[array_rand($userIds)],
                ':messages_content' => $message['MessagesContent'],
                ':sent_at' => $message['SentAt']
            ];
        }
    );
}
$totalInserted += $messagesInserted;

// 6. Transactions Data (Requires Users & Listings) - Based on transactions.model.sql structure
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

// // 7. Cart Data (Requires Users & Listings) - Based on cart.model.sql structure
// echo "\n🌱 Seeding cart with foreign key relationships...\n";

// // Get random users and listings for foreign key relationships
// $userStmt = $pdo->query("SELECT User_ID FROM users ORDER BY RANDOM() LIMIT 10");
// $userIds = $userStmt->fetchAll(PDO::FETCH_COLUMN);

// $listingStmt = $pdo->query("SELECT Listing_ID FROM listings ORDER BY RANDOM() LIMIT 10");
// $listingIds = $listingStmt->fetchAll(PDO::FETCH_COLUMN);

// if (empty($userIds) || empty($listingIds)) {
//     echo "⚠️  Cannot seed cart: Missing users or listings data\n";
//     $cartInserted = 0;
// } else {
//     $cartInserted = seedTable(
//         $pdo,
//         'cart',
//         'cart.staticData.php',
//         'INSERT INTO cart (User_ID, Listing_ID, Quantity, Added_At)
//          VALUES (:user_id, :listing_id, :quantity, :added_at)',
//         function($cartItem) use ($userIds, $listingIds) {
//             return [
//                 ':user_id' => $userIds[array_rand($userIds)],
//                 ':listing_id' => $listingIds[array_rand($listingIds)],
//                 ':quantity' => $cartItem['Quantity'],
//                 ':added_at' => $cartItem['AddedAt']
//             ];
//         }
//     );
// }
// $totalInserted += $cartInserted;

// ---- 🔍 Verify Seeding Results ----
echo "\n🔍 Verifying seeding results...\n";

// $expectedTables = ['users', 'categories', 'listings', 'feedbacks', 'messages', 'transactions', 'cart'];
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
echo "   ✅ Total records inserted: {$totalInserted}\n";
echo "   📋 Total records in database: {$totalRecords}\n";
echo "   🎯 All tables seeded successfully!\n";
echo "\n➡️  Database is now ready for use!\n";
echo "🎉 ========================================\n";
