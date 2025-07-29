<?php
/**
 * Fix Categories Database - Ensure categories exist for product creation
 */

require_once __DIR__ . '/../bootstrap.php';
require_once UTILS_PATH . '/DatabaseService.util.php';

echo "🔧 FIXING CATEGORIES DATABASE ISSUE\n";
echo "=====================================\n";

try {
    $db = DatabaseService::getInstance()->getConnection();
    
    // Check current categories in database
    echo "1. Checking existing categories in database...\n";
    $stmt = $db->query("SELECT name FROM categories ORDER BY name");
    $existingCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "   Found " . count($existingCategories) . " categories in database:\n";
    foreach ($existingCategories as $cat) {
        echo "   - $cat\n";
    }
    
    // Load static categories used in frontend
    echo "\n2. Loading static categories from frontend...\n";
    $staticCategories = require_once __DIR__ . '/../staticData/dummies/categories.staticData.php';
    
    echo "   Found " . count($staticCategories) . " static categories:\n";
    foreach ($staticCategories as $cat) {
        echo "   - " . $cat['Name'] . "\n";
    }
    
    // Insert missing categories
    echo "\n3. Inserting missing categories...\n";
    $insertStmt = $db->prepare("INSERT INTO categories (name, description) VALUES (:name, :description) ON CONFLICT (name) DO NOTHING");
    
    $inserted = 0;
    foreach ($staticCategories as $category) {
        try {
            $insertStmt->execute([
                'name' => $category['Name'],
                'description' => $category['Description']
            ]);
            
            if ($insertStmt->rowCount() > 0) {
                echo "   ✅ Inserted: " . $category['Name'] . "\n";
                $inserted++;
            } else {
                echo "   ⚠️  Already exists: " . $category['Name'] . "\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Failed to insert " . $category['Name'] . ": " . $e->getMessage() . "\n";
        }
    }
    
    // Verify final state
    echo "\n4. Final verification...\n";
    $stmt = $db->query("SELECT name FROM categories ORDER BY name");
    $finalCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "   Database now has " . count($finalCategories) . " categories:\n";
    foreach ($finalCategories as $cat) {
        echo "   ✅ $cat\n";
    }
    
    echo "\n🎉 CATEGORIES DATABASE FIX COMPLETE!\n";
    echo "✅ Inserted $inserted new categories\n";
    echo "✅ Database now has " . count($finalCategories) . " total categories\n";
    echo "✅ Product creation should now work!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
