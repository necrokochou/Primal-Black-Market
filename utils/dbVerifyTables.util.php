<?php
echo "=== Database Tables Verification ===\n";

$host = 'postgresql';
$port = '5432';
$dbname = 'primal-black-market';
$user = 'user';
$password = 'password';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful!\n\n";
    
    // List of expected tables
    $tables = ['users', 'categories', 'listings', 'feedbacks', 'messages', 'transactions'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "✅ Table '$table' exists and has $count records\n";
        } catch (Exception $e) {
            echo "❌ Table '$table' does not exist or has error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== Table Structure Check ===\n";
    
    // Get all tables in the database
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "All tables in database:\n";
    foreach ($existingTables as $table) {
        echo "- $table\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}
