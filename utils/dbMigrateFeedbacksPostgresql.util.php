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

echo "Creating feedbacks table...\n";

// Create feedbacks table (adapted for PostgreSQL, fixed missing comma)
$createFeedbacksTable = '
CREATE TABLE IF NOT EXISTS feedbacks (
    "FeedbackID" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    "ReviewerID" INTEGER NOT NULL,
    "VendorID" INTEGER NOT NULL,
    "Rating" INTEGER CHECK ("Rating" BETWEEN 0 AND 5),
    "Comments" TEXT NOT NULL,
    "PostedAt" DATE NOT NULL,
    FOREIGN KEY ("ReviewerID") REFERENCES users("UserID"),
    FOREIGN KEY ("VendorID") REFERENCES users("UserID")
);
';

try {
    $pdo->exec($createFeedbacksTable);
    echo "✅ Feedbacks table created successfully.\n";
} catch (PDOException $e) {
    die("❌ Failed to create feedbacks table: " . $e->getMessage() . "\n");
}

echo "✅ Migration complete!\n";
