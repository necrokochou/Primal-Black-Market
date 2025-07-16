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

echo "Creating listings table...\n";

// Create listings table (adapted for PostgreSQL)
$createListingsTable = '
CREATE TABLE IF NOT EXISTS listings (
    "ListingID" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    "VendorID" uuid NOT NULL,
    "Title" VARCHAR(256) NOT NULL,
    "Description" TEXT NOT NULL,
    "Category" VARCHAR(100) NOT NULL,
    "Price" REAL NOT NULL,
    "Quantity" INTEGER NOT NULL,
    "IsActive" BOOLEAN DEFAULT TRUE,
    "PublishDate" DATE NOT NULL,
    FOREIGN KEY ("VendorID") REFERENCES users("UserID")
);
';

try {
    $pdo->exec($createListingsTable);
    echo "✅ Listings table created successfully.\n";
} catch (PDOException $e) {
    die("❌ Failed to create listings table: " . $e->getMessage() . "\n");
}

echo "✅ Migration complete!\n";
