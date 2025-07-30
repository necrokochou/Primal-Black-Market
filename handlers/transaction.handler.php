<?php
// Include bootstrap to define constants first
require_once BASE_PATH . '/bootstrap.php';

// Disable all output and error display to prevent contaminating JSON
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Configure error logging
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
require_once UTILS_PATH . '/auth.util.php';

// Clear any output that might have been generated during includes
ob_clean();

header('Content-Type: application/json');

$user = $_SESSION['user'] ?? null;
if (!$user || !isset($user['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    $pdo = connectPostgres();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

switch ($action) {
    case 'checkout':
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            $userID = $user['user_id'];
            $paymentMethod = $_POST['payment_method'] ?? 'Credit Card';
            $deliveryNotes = $_POST['delivery_notes'] ?? '';
            
            // Get cart items
            $stmt = $pdo->prepare("
                SELECT c.cart_id, c.listing_id, c.quantity, l.price, l.vendor_id
                FROM cart c
                JOIN listings l ON l.listing_id = c.listing_id
                WHERE c.user_id = :userID
            ");
            $stmt->execute(['userID' => $userID]);
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($cartItems)) {
                throw new Exception('Cart is empty');
            }
            
            // Debug: Log cart items with vendor info
            foreach ($cartItems as $cartItem) {
                error_log("Cart item: listing_id={$cartItem['listing_id']}, vendor_id={$cartItem['vendor_id']}, buyer_id={$userID}");
            }
            
            $processedTransactions = [];
            
            // Process each cart item
            foreach ($cartItems as $item) {
                $quantity = (int)$item['quantity'];
                $unitPrice = (float)$item['price'];
                
                // Validate data
                if ($quantity <= 0) {
                    throw new Exception('Invalid quantity: ' . $quantity);
                }
                if ($unitPrice < 0) {
                    throw new Exception('Invalid price: ' . $unitPrice);
                }
                
                $totalPrice = $quantity * $unitPrice;
                
                // Convert total price to cents (integer) for transactions table
                // NOTE: transactions table has int column, but prices should be decimal
                $totalPriceCents = (int)round($totalPrice * 100);
                
                // Validate the cents conversion doesn't overflow or lose precision
                if ($totalPriceCents <= 0 || $totalPriceCents > 2147483647) { // Max int32
                    throw new Exception('Price out of range: $' . $totalPrice);
                }
                
                // Create transaction record
                $stmt = $pdo->prepare("
                    INSERT INTO transactions (buyer_id, listing_id, quantity, total_price, transaction_status, timestamp)
                    VALUES (:buyerID, :listingID, :quantity, :totalPrice, 'Completed', CURRENT_DATE)
                    RETURNING transaction_id
                ");
                $stmt->execute([
                    'buyerID' => $userID,
                    'listingID' => $item['listing_id'],
                    'quantity' => $quantity,
                    'totalPrice' => $totalPriceCents  // Use cents for integer column
                ]);
                $transactionResult = $stmt->fetch(PDO::FETCH_ASSOC);
                $transactionID = $transactionResult['transaction_id'];
                
                // Create purchase history record
                $stmt = $pdo->prepare("
                    INSERT INTO purchase_history (user_id, listing_id, transaction_id, quantity, price_each, total_price, payment_method, delivery_status, notes)
                    VALUES (:userID, :listingID, :transactionID, :quantity, :priceEach, :totalPrice, :paymentMethod, 'Processed', :notes)
                ");
                $stmt->execute([
                    'userID' => $userID,
                    'listingID' => $item['listing_id'],
                    'transactionID' => $transactionID,
                    'quantity' => $quantity,
                    'priceEach' => $unitPrice,
                    'totalPrice' => $totalPrice,
                    'paymentMethod' => $paymentMethod,
                    'notes' => $deliveryNotes
                ]);
                
                // Debug: Log purchase history creation
                error_log("Created purchase_history record: buyer={$userID}, listing={$item['listing_id']}, transaction={$transactionID}");
                
                $processedTransactions[] = [
                    'transaction_id' => $transactionID,
                    'listing_id' => $item['listing_id'],
                    'quantity' => $quantity,
                    'total_price' => $totalPrice
                ];
            }
            
            // Clear the cart after successful purchase
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :userID");
            $stmt->execute(['userID' => $userID]);
            
            // Commit transaction
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Purchase completed successfully!',
                'transactions' => $processedTransactions,
                'total_items' => count($processedTransactions)
            ]);
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $pdo->rollBack();
            error_log('Transaction error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Purchase failed: ' . $e->getMessage()]);
        }
        break;
        
    case 'get_purchase_history':
        try {
            $userID = $user['user_id'];
            
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
                    l.title AS product_title,
                    l.item_image,
                    t.transaction_id,
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
                'total_purchases' => count($purchases)
            ]);
            
        } catch (Exception $e) {
            error_log('Get purchase history error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to retrieve purchase history']);
        }
        break;
        
    case 'get_sales_history':
        try {
            $userID = $user['user_id'];
            
            // Get sales for vendors (where they are the seller of the listing)
            $stmt = $pdo->prepare("
                SELECT 
                    ph.purchase_history_id,
                    ph.quantity,
                    ph.price_each,
                    ph.total_price,
                    ph.purchase_date,
                    ph.payment_method,
                    ph.delivery_status,
                    l.title AS product_title,
                    l.item_image,
                    l.vendor_id,
                    t.transaction_id,
                    t.transaction_status,
                    u.username AS buyer_name
                FROM purchase_history ph
                JOIN listings l ON l.listing_id = ph.listing_id
                JOIN transactions t ON t.transaction_id = ph.transaction_id
                JOIN users u ON u.user_id = ph.user_id
                WHERE l.vendor_id = :userID
                ORDER BY ph.purchase_date DESC
            ");
            $stmt->execute(['userID' => $userID]);
            $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug logging to help identify the issue
            error_log("Sales query for vendor $userID returned " . count($sales) . " results");
            if (count($sales) === 0) {
                // Check if this user has any listings
                $checkStmt = $pdo->prepare("SELECT COUNT(*) as listing_count FROM listings WHERE vendor_id = :userID");
                $checkStmt->execute(['userID' => $userID]);
                $listingCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['listing_count'];
                error_log("Vendor $userID has $listingCount listings");
                
                // Check if there are any purchases for this vendor's listings
                $purchaseCheckStmt = $pdo->prepare("
                    SELECT COUNT(*) as purchase_count 
                    FROM purchase_history ph 
                    JOIN listings l ON l.listing_id = ph.listing_id 
                    WHERE l.vendor_id = :userID
                ");
                $purchaseCheckStmt->execute(['userID' => $userID]);
                $purchaseCount = $purchaseCheckStmt->fetch(PDO::FETCH_ASSOC)['purchase_count'];
                error_log("Found $purchaseCount purchases for vendor $userID's listings");
            }
            
            echo json_encode([
                'success' => true,
                'sales' => $sales,
                'total_sales' => count($sales)
            ]);
            
        } catch (Exception $e) {
            error_log('Get sales history error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to retrieve sales history']);
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}
