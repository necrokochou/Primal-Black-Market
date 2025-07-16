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

echo "Seeding listings table...\n";

// Load dummy data
$dummyListings = require_once DUMMIES_PATH . '/dummydata.listings.php';

// Clear existing data
$pdo->exec('DELETE FROM listings');
echo "✅ Cleared existing listings data.\n";

// Get user IDs for vendors (assuming we need to assign listings to existing users)
$vendorQuery = $pdo->query('SELECT "UserID" FROM users WHERE "IsVendor" = TRUE');
$vendorIds = $vendorQuery->fetchAll(PDO::FETCH_COLUMN);

if (empty($vendorIds)) {
    echo "⚠️  No vendors found in users table. Please seed users first.\n";
    exit(1);
}

// Insert dummy data
$insertStmt = $pdo->prepare('
    INSERT INTO listings ("VendorID", "Title", "Description", "Category", "Price", "Quantity", "IsActive", "PublishDate") 
    VALUES (:vendorId, :title, :description, :category, :price, :quantity, :isActive, :publishDate)
');

$insertedCount = 0;
foreach ($dummyListings as $listing) {
    // Skip incomplete listings
    if (!isset($listing['Title']) || !isset($listing['Description']) || !isset($listing['Category'])) {
        continue;
    }
    
    try {
        // Assign a random vendor ID
        $randomVendorId = $vendorIds[array_rand($vendorIds)];
        
        $insertStmt->execute([
            ':vendorId' => $randomVendorId,
            ':title' => $listing['Title'],
            ':description' => $listing['Description'],
            ':category' => $listing['Category'],
            ':price' => $listing['Price'],
            ':quantity' => $listing['Quantity'],
            ':isActive' => $listing['IsActive'],
            ':publishDate' => $listing['PublishDate']
        ]);
        $insertedCount++;
        echo "✅ Inserted listing: {$listing['Title']}\n";
    } catch (PDOException $e) {
        echo "❌ Failed to insert listing {$listing['Title']}: " . $e->getMessage() . "\n";
    }
}

echo "✅ Seeding complete! Inserted {$insertedCount} listings.\n";
