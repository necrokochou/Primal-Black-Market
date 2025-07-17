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

echo "Seeding messages table...\n";

// Load dummy data
$dummyMessages = require_once DUMMIES_PATH . '/messages.staticData.php';

// Clear existing data
$pdo->exec('DELETE FROM messages');
echo "✅ Cleared existing messages data.\n";

// Get user IDs for senders and receivers
$userQuery = $pdo->query('SELECT "UserID" FROM users');
$userIds = $userQuery->fetchAll(PDO::FETCH_COLUMN);

if (empty($userIds)) {
    echo "⚠️  No users found in users table. Please seed users first.\n";
    exit(1);
}

// Insert dummy data
$insertStmt = $pdo->prepare('
    INSERT INTO messages ("SenderID", "ReceiverID", "MessagesContent", "SentAt") 
    VALUES (:senderId, :receiverId, :messagesContent, :sentAt)
');

$insertedCount = 0;
foreach ($dummyMessages as $message) {
    try {
        // Assign random sender and receiver IDs (make sure they're different)
        $senderId = $userIds[array_rand($userIds)];
        do {
            $receiverId = $userIds[array_rand($userIds)];
        } while ($senderId === $receiverId && count($userIds) > 1);
        
        $insertStmt->execute([
            ':senderId' => $senderId,
            ':receiverId' => $receiverId,
            ':messagesContent' => $message['MessagesContent'],
            ':sentAt' => $message['SentAt']
        ]);
        $insertedCount++;
        echo "✅ Inserted message: " . substr($message['MessagesContent'], 0, 50) . "...\n";
    } catch (PDOException $e) {
        echo "❌ Failed to insert message: " . $e->getMessage() . "\n";
    }
}

echo "✅ Seeding complete! Inserted {$insertedCount} messages.\n";
