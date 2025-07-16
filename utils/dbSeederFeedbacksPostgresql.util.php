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

echo "Seeding feedbacks table...\n";

// Load dummy data
$dummyFeedbacks = require_once DUMMIES_PATH . '/dummydata.feedbacks.php';

// Clear existing data
$pdo->exec('DELETE FROM feedbacks');
echo "✅ Cleared existing feedbacks data.\n";

// Get user IDs for reviewers and vendors
$userQuery = $pdo->query('SELECT "UserID" FROM users');
$userIds = $userQuery->fetchAll(PDO::FETCH_COLUMN);

$vendorQuery = $pdo->query('SELECT "UserID" FROM users WHERE "IsVendor" = TRUE');
$vendorIds = $vendorQuery->fetchAll(PDO::FETCH_COLUMN);

if (empty($userIds) || empty($vendorIds)) {
    echo "⚠️  No users or vendors found in users table. Please seed users first.\n";
    exit(1);
}

// Insert dummy data
$insertStmt = $pdo->prepare('
    INSERT INTO feedbacks ("ReviewerID", "VendorID", "Rating", "Comments", "PostedAt") 
    VALUES (:reviewerId, :vendorId, :rating, :comments, :postedAt)
');

$insertedCount = 0;
foreach ($dummyFeedbacks as $feedback) {
    try {
        // Assign random reviewer and vendor IDs
        $randomReviewerId = $userIds[array_rand($userIds)];
        $randomVendorId = $vendorIds[array_rand($vendorIds)];
        
        $insertStmt->execute([
            ':reviewerId' => $randomReviewerId,
            ':vendorId' => $randomVendorId,
            ':rating' => $feedback['Rating'],
            ':comments' => $feedback['Comments'],
            ':postedAt' => $feedback['PostedAt']
        ]);
        $insertedCount++;
        echo "✅ Inserted feedback with rating: {$feedback['Rating']}\n";
    } catch (PDOException $e) {
        echo "❌ Failed to insert feedback: " . $e->getMessage() . "\n";
    }
}

echo "✅ Seeding complete! Inserted {$insertedCount} feedbacks.\n";
