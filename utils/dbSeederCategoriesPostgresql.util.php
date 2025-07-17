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

echo "Seeding categories table...\n";

// Load dummy data
$dummyCategories = require_once DUMMIES_PATH . '/categories.staticData.php';

// Clear existing data
$pdo->exec('DELETE FROM categories');
echo "✅ Cleared existing categories data.\n";

// Insert dummy data
$insertStmt = $pdo->prepare('
    INSERT INTO categories ("Name", "Description") 
    VALUES (:name, :description)
');

$insertedCount = 0;
foreach ($dummyCategories as $category) {
    try {
        $insertStmt->execute([
            ':name' => $category['Name'],
            ':description' => $category['Description']
        ]);
        $insertedCount++;
        echo "✅ Inserted category: {$category['Name']}\n";
    } catch (PDOException $e) {
        echo "❌ Failed to insert category {$category['Name']}: " . $e->getMessage() . "\n";
    }
}

echo "✅ Seeding complete! Inserted {$insertedCount} categories.\n";
