<?php
// Include bootstrap to define constants first
require_once BASE_PATH . '/bootstrap.php';

// Disable all output and error display to prevent contaminating JSON
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/php-error.log');

// Clean any existing output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Start fresh output buffering
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define this BEFORE bootstrap to prevent output buffer cleaning
define('KEEP_OUTPUT_BUFFER', true);

require_once UTILS_PATH . '/dbConnect.util.php';

// Clear any output that might have been generated during includes
ob_clean();

header('Content-Type: application/json');

$user = $_SESSION['user'] ?? null;
if (!$user || !isset($user['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $pdo = connectPostgres();
    $userID = $user['user_id'];
    
    switch ($action) {
        case 'get_purchases':
            // Get purchase history with listing details
            $stmt = $pdo->prepare("
                SELECT 
                    ph.purchase_history_id,
                    ph.quantity,
                    ph.price_each,
                    ph.total_price,
                    ph.purchase_date,
                    ph.payment_method,
                    ph.delivery_status,
                    ph.notes,
                    l.title as product_title,
                    l.item_image as product_image,
                    l.category as product_category,
                    t.transaction_status
                FROM purchase_history ph
                JOIN listings l ON l.listing_id = ph.listing_id
                JOIN transactions t ON t.transaction_id = ph.transaction_id
                WHERE ph.user_id = :userID
                ORDER BY ph.purchase_date DESC
            ");
            $stmt->execute(['userID' => $userID]);
            $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'purchases' => $purchases,
                'total_count' => count($purchases)
            ]);
            break;
            
        case 'get_sales':
            // Get sales history for vendors
            $stmt = $pdo->prepare("
                SELECT 
                    ph.purchase_history_id,
                    ph.quantity,
                    ph.price_each,
                    ph.total_price,
                    ph.purchase_date,
                    ph.payment_method,
                    ph.delivery_status,
                    l.title as product_title,
                    l.item_image as product_image,
                    l.category as product_category,
                    u.username as buyer_name
                FROM purchase_history ph
                JOIN listings l ON l.listing_id = ph.listing_id
                JOIN users u ON u.user_id = ph.user_id
                WHERE l.vendor_id = :userID
                ORDER BY ph.purchase_date DESC
            ");
            $stmt->execute(['userID' => $userID]);
            $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'sales' => $sales,
                'total_count' => count($sales)
            ]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Purchase history error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to fetch purchase history']);
}
