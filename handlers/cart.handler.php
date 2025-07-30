<?php
require_once BASE_PATH . '/bootstrap.php';
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/php-error.log');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;

require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/cart.util.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    $cart = new CartHandler();
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

switch ($action) {
    case 'add':
        $listingID = $_POST['listing_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;

        if (!$listingID || $listingID === 'null') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid listing ID']);
            exit;
        }

        $cart->addToCart($listingID, (int)$quantity);
        echo json_encode(['success' => true]);
        break;

    case 'get':
        $items = $cart->getCart();
        echo json_encode(['success' => true, 'items' => $items]);
        break;

    case 'update':
        $cartID = $_POST['cart_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;

        if (!$cartID || $quantity === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing cart_id or quantity']);
            exit;
        }

        $cart->updateItem($cartID, (int)$quantity);
        echo json_encode(['success' => true]);
        break;

    case 'remove':
        $cartID = $_POST['cart_id'] ?? '';
        if (!$cartID) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing item ID']);
            exit;
        }

        $cart->removeItem($cartID);
        echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
        break;

    case 'count':
        if (!$user || !isset($user['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'User not authenticated']);
            exit;
        }

        $count = $cart->getCartCount($user['user_id']);
        echo json_encode(['success' => true, 'count' => $count]);
        break;

    case 'clear':
        if (!$user || !isset($user['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'User not authenticated']);
            exit;
        }

        $cart->clearCart($user['user_id']);
        echo json_encode(['success' => true, 'message' => 'Cart cleared']);
        break;

    case 'checkout':
        if (!$user || !isset($user['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'User not authenticated']);
            exit;
        }

        $userID = $user['user_id'];

        try {
            $cartItems = $cart->getCart();

            if (empty($cartItems)) {
                echo json_encode(['success' => false, 'error' => 'Cart is empty']);
                exit;
            }

            $pdo = $cart->getPdo();
            $pdo->beginTransaction();

            // Random value pools
            $paymentMethods = ['Cash on Delivery', 'Cryptocurrency', 'Credit Card', 'PayPal', 'Bitcoin'];
            $deliveryStatuses = ['Pending', 'Shipped', 'Delivered', 'In Transit', 'Awaiting Pickup'];
            $noteSamples = ['Handle with care', 'Deliver ASAP', 'Leave at door', 'Call before delivery', 'Gift item'];

            foreach ($cartItems as $item) {
                // Random values
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $deliveryStatus = $deliveryStatuses[array_rand($deliveryStatuses)];
                $note = $noteSamples[array_rand($noteSamples)];

                // Insert into transactions
                $transactionStmt = $pdo->prepare("
                    INSERT INTO transactions (
                        transaction_id, buyer_id, listing_id, quantity, total_price, transaction_status, timestamp
                    ) VALUES (
                        gen_random_uuid(), :buyerID, :listingID, :qty, :total, :status, NOW()
                    ) RETURNING transaction_id
                ");
                $transactionStmt->execute([
                    'buyerID'   => $userID,
                    'listingID' => $item['listing_id'],
                    'qty'       => $item['quantity'],
                    'total'     => $item['unit_price'] * $item['quantity'],
                    'status'    => $deliveryStatus
                ]);
                $transactionID = $transactionStmt->fetchColumn();

                // Insert into purchase_history
                $purchaseStmt = $pdo->prepare("
                    INSERT INTO purchase_history (
                        purchase_history_id, user_id, listing_id, transaction_id,
                        quantity, price_each, total_price,
                        payment_method, delivery_status, notes
                    ) VALUES (
                        gen_random_uuid(), :userID, :listingID, :transactionID,
                        :quantity, :priceEach, :total,
                        :method, :status, :notes
                    )
                ");
                $purchaseStmt->execute([
                    'userID'        => $userID,
                    'listingID'     => $item['listing_id'],
                    'transactionID' => $transactionID,
                    'quantity'      => $item['quantity'],
                    'priceEach'     => $item['unit_price'],
                    'total'         => $item['unit_price'] * $item['quantity'],
                    'method'        => $paymentMethod,
                    'status'        => $deliveryStatus,
                    'notes'         => $note
                ]);
            }

            $cart->clearCart($userID);
            $pdo->commit();

            echo json_encode(['success' => true, 'message' => 'Checkout complete']);
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log('Checkout error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Server error during checkout']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}
