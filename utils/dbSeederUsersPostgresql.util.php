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

// Load dummy users from static data
$users = require_once STATICDATAS_PATH . '/dummies/dummydata.users.php';

// Clear existing users (optional - uncomment if you want to reset the table)
// echo "Clearing existing users...\n";
// $pdo->exec('TRUNCATE TABLE users RESTART IDENTITY CASCADE');

// Seeding logic
echo "Seeding users…\n";
$stmt = $pdo->prepare('
    INSERT INTO users ("Username", "Password", "Email", "Alias", "TrustLevel", "IsVendor", "IsAdmin")
    VALUES (:username, :password, :email, :alias, :trustlevel, :isvendor, :isadmin)
    ON CONFLICT ("Username") DO NOTHING
');

$seededCount = 0;
$skippedCount = 0;

foreach ($users as $user) {
    try {
        $result = $stmt->execute([
            ':username' => $user['Username'],
            ':password' => password_hash($user['Password'], PASSWORD_DEFAULT),
            ':email' => $user['Email'] ?? $user['Username'] . '@example.com',
            ':alias' => $user['Alias'],
            ':trustlevel' => $user['TrustLevel'],
            ':isvendor' => $user['IsVendor'] ? 'true' : 'false',
            ':isadmin' => $user['IsAdmin'] ? 'true' : 'false'
        ]);
        
        if ($stmt->rowCount() > 0) {
            echo "👤 User '{$user['Username']}' ({$user['Alias']}) seeded successfully.\n";
            $seededCount++;
        } else {
            echo "⚠️  User '{$user['Username']}' already exists, skipped.\n";
            $skippedCount++;
        }
    } catch (PDOException $e) {
        echo "❌ Failed to seed user '{$user['Username']}': " . $e->getMessage() . "\n";
    }
}

echo "\n✅ PostgreSQL users seeding complete!\n";
echo "📊 Summary: {$seededCount} users seeded, {$skippedCount} users skipped.\n";