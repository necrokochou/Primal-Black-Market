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

echo "Creating categories table...\n";

// Create categories table (adapted for PostgreSQL)
$createCategoriesTable = '
CREATE TABLE IF NOT EXISTS categories (
    "CategoriesID" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    "Name" VARCHAR(256) UNIQUE NOT NULL,
    "Description" TEXT NOT NULL
);
';

try {
    $pdo->exec($createCategoriesTable);
    echo "✅ Categories table created successfully.\n";
} catch (PDOException $e) {
    die("❌ Failed to create categories table: " . $e->getMessage() . "\n");
}

echo "✅ Migration complete!\n";
