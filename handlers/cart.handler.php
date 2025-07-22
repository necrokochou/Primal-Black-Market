<?php
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php-error.log'); // set correct path

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;

require_once __DIR__ . '/../utils/cart.util.php';

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
        exit;

    case 'remove':
        $itemID = $_POST['item_id'] ?? '';
        if (!$itemID) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing item ID']);
            exit;
        }
        $cart->removeItem($itemID);
        echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
        exit;

    case 'count':
        $userId = $user['id'] ?? null;

        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'User not authenticated']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT COALESCE(SUM(Quantity), 0) FROM cart WHERE User_ID = ?");
        $stmt->execute([$userId]);
        $count = $stmt->fetchColumn();

        echo json_encode(['success' => true, 'count' => $count]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        exit;
}