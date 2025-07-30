<?php
/**
 * Database Health Check for Product Creation Issues
 */

require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/DatabaseService.util.php';

echo "🏥 PRIMAL BLACK MARKET - DATABASE HEALTH CHECK\n";
echo "===============================================\n";

try {
    echo "1. Testing database connection...\n";
    $db = DatabaseService::getInstance()->getConnection();
    echo "   ✅ Database connected successfully\n";
    
    echo "\n2. Checking required tables...\n";
    $requiredTables = ['users', 'categories', 'listings'];
    foreach ($requiredTables as $table) {
        try {
            $stmt = $db->query("SELECT COUNT(*) FROM {$table}");
            $count = $stmt->fetchColumn();
            echo "   ✅ Table '{$table}': {$count} records\n";
        } catch (Exception $e) {
            echo "   ❌ Table '{$table}': ERROR - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n3. Checking categories specifically...\n";
    $stmt = $db->query("SELECT categories_id, name FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($categories)) {
        echo "   ❌ NO CATEGORIES FOUND! This is likely the root cause.\n";
        echo "   💡 Solution: Run the category fix utility\n";
    } else {
        echo "   ✅ Found " . count($categories) . " categories:\n";
        foreach ($categories as $cat) {
            echo "      - {$cat['name']} (ID: {$cat['categories_id']})\n";
        }
    }
    
    echo "\n4. Testing category resolution...\n";
    $testCategories = ['Weapons', 'Food', 'General Equipment'];
    foreach ($testCategories as $testCat) {
        try {
            $stmt = $db->prepare("SELECT categories_id FROM categories WHERE LOWER(name) = LOWER(:name)");
            $stmt->execute(['name' => $testCat]);
            $id = $stmt->fetchColumn();
            
            if ($id) {
                echo "   ✅ '{$testCat}' resolves to: {$id}\n";
            } else {
                echo "   ❌ '{$testCat}' NOT FOUND in database\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Error testing '{$testCat}': " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n5. Checking foreign key constraints...\n";
    try {
        $stmt = $db->query("
            SELECT 
                tc.constraint_name, 
                tc.table_name, 
                kcu.column_name, 
                ccu.table_name AS foreign_table_name,
                ccu.column_name AS foreign_column_name 
            FROM 
                information_schema.table_constraints AS tc 
                JOIN information_schema.key_column_usage AS kcu
                  ON tc.constraint_name = kcu.constraint_name
                JOIN information_schema.constraint_column_usage AS ccu
                  ON ccu.constraint_name = tc.constraint_name
            WHERE constraint_type = 'FOREIGN KEY' 
            AND tc.table_name = 'listings'
        ");
        
        $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($constraints as $constraint) {
            echo "   ✅ FK: {$constraint['table_name']}.{$constraint['column_name']} -> {$constraint['foreign_table_name']}.{$constraint['foreign_column_name']}\n";
        }
    } catch (Exception $e) {
        echo "   ⚠️  Could not check foreign keys: " . $e->getMessage() . "\n";
    }
    
    echo "\n6. Testing sample product creation data...\n";
    $sampleData = [
        'vendor_id' => '12345678-1234-1234-1234-123456789012', // Sample UUID
        'title' => 'Test Product',
        'description' => 'This is a test product description that is long enough.',
        'category' => 'Weapons',
        'price' => 19.99,
        'quantity' => 1
    ];
    
    foreach ($sampleData as $field => $value) {
        echo "   ✅ {$field}: " . (is_string($value) ? "'{$value}'" : $value) . "\n";
    }
    
    echo "\n🎉 HEALTH CHECK COMPLETE!\n";
    echo "=====================================\n";
    
    if (empty($categories)) {
        echo "❌ CRITICAL ISSUE FOUND: No categories in database\n";
        echo "💡 SOLUTION: Run this command to fix:\n";
        echo "   php utils/fixCategoriesDatabase.util.php\n";
    } else {
        echo "✅ Database appears healthy for product creation\n";
        echo "💡 If product creation still fails, check the error logs\n";
    }
    
} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "💡 Check your database configuration and connection\n";
}
