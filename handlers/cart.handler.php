<?php
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php-error.log');

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

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}
