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

echo "Creating transactions table...\n";

// Create transactions table (adapted for PostgreSQL)
$createTransactionsTable = '
CREATE TABLE IF NOT EXISTS transactions (
    "TransactionID" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    "BuyerID" uuid NOT NULL,
    "ListingID" uuid NOT NULL,
    "Quantity" INTEGER NOT NULL,
    "TotalPrice" INTEGER NOT NULL,
    "TransactionStatus" VARCHAR(30) NOT NULL,
    "Timestamp" DATE NOT NULL,
    FOREIGN KEY ("BuyerID") REFERENCES users("UserID"),
    FOREIGN KEY ("ListingID") REFERENCES listings("ListingID")
);
';

try {
    $pdo->exec($createTransactionsTable);
    echo "✅ Transactions table created successfully.\n";
} catch (PDOException $e) {
    die("❌ Failed to create transactions table: " . $e->getMessage() . "\n");
}

echo "✅ Migration complete!\n";
