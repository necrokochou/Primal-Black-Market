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

echo "Creating messages table...\n";

// Create messages table (adapted for PostgreSQL)
$createMessagesTable = '
CREATE TABLE IF NOT EXISTS messages (
    "MessageID" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    "SenderID" uuid NOT NULL,
    "ReceiverID" uuid NOT NULL,
    "MessagesContent" TEXT,
    "SentAt" DATE NOT NULL,
    FOREIGN KEY ("SenderID") REFERENCES users("UserID"),
    FOREIGN KEY ("ReceiverID") REFERENCES users("UserID")
);
';

try {
    $pdo->exec($createMessagesTable);
    echo "✅ Messages table created successfully.\n";
} catch (PDOException $e) {
    die("❌ Failed to create messages table: " . $e->getMessage() . "\n");
}

echo "✅ Migration complete!\n";
