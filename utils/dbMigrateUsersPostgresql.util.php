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

echo "Creating users table...\n";

// Create users table (adapted for PostgreSQL)
$createUsersTable = '
CREATE TABLE IF NOT EXISTS users (
    "UserID" SERIAL PRIMARY KEY,
    "Username" VARCHAR(256) UNIQUE NOT NULL,
    "Password" VARCHAR(256) NOT NULL,
    "Alias" VARCHAR(256) NOT NULL,
    "TrustLevel" REAL DEFAULT 0,
    "IsVendor" BOOLEAN DEFAULT FALSE
);
';

try {
    $pdo->exec($createUsersTable);
    echo "✅ Users table created successfully.\n";
} catch (PDOException $e) {
    die("❌ Failed to create users table: " . $e->getMessage() . "\n");
}

echo "✅ Migration complete!\n";
