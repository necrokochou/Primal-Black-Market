<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';
require_once 'bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';

// Get PostgreSQL configuration
$pgConfig = getPostgresEnv();

// Connect to PostgreSQL
$host = $pgConfig['host'];
$port = $pgConfig['port'];
$username = $pgConfig['user'];
$password = $pgConfig['password'];
$dbname = $pgConfig['db'];

$dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✅ Connected to PostgreSQL successfully.\n";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

echo "Seeding transactions table...\n";

// Load dummy data
$dummyTransactions = require_once DUMMIES_PATH . '/transactions.staticData.php';

// Clear existing data
$pdo->exec('DELETE FROM transactions');
echo "✅ Cleared existing transactions data.\n";

// Get user IDs for buyers and listing IDs
$buyerQuery = $pdo->query('SELECT "UserID" FROM users');
$buyerIds = $buyerQuery->fetchAll(PDO::FETCH_COLUMN);

$listingQuery = $pdo->query('SELECT "ListingID" FROM listings');
$listingIds = $listingQuery->fetchAll(PDO::FETCH_COLUMN);

if (empty($buyerIds)) {
    echo "⚠️  No users found in users table. Please seed users first.\n";
    exit(1);
}

if (empty($listingIds)) {
    echo "⚠️  No listings found in listings table. Please seed listings first.\n";
    exit(1);
}

// Insert dummy data
$insertStmt = $pdo->prepare('
    INSERT INTO transactions ("BuyerID", "ListingID", "Quantity", "TotalPrice", "TransactionStatus", "Timestamp") 
    VALUES (:buyerId, :listingId, :quantity, :totalPrice, :transactionStatus, :timestamp)
');

$insertedCount = 0;
foreach ($dummyTransactions as $transaction) {
    try {
        // Assign random buyer and listing IDs
        $randomBuyerId = $buyerIds[array_rand($buyerIds)];
        $randomListingId = $listingIds[array_rand($listingIds)];
        
        $insertStmt->execute([
            ':buyerId' => $randomBuyerId,
            ':listingId' => $randomListingId,
            ':quantity' => $transaction['Quantity'],
            ':totalPrice' => $transaction['TotalPrice'],
            ':transactionStatus' => $transaction['TransactionStatus'],
            ':timestamp' => $transaction['Timestamp']
        ]);
        $insertedCount++;
        echo "✅ Inserted transaction: {$transaction['TransactionStatus']} - {$transaction['TotalPrice']}\n";
    } catch (PDOException $e) {
        echo "❌ Failed to insert transaction: " . $e->getMessage() . "\n";
    }
}

echo "✅ Seeding complete! Inserted {$insertedCount} transactions.\n";
