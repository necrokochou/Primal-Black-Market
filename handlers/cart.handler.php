<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap.php';
require_once UTILS_PATH . '/DatabaseService.util.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $productId = $_POST['product_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $image = $_POST['image'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 1);

    if (!$title || !$price) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing product information']);
        exit;
    }

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if item already exists in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['title'] === $title && $item['price'] === $price) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    // If not found, add new item
    if (!$found) {
        $_SESSION['cart'][] = [
            'product_id' => $productId,
            'title' => $title,
            'price' => $price,
            'image' => $image,
            'quantity' => $quantity
        ];
    }

    // Calculate cart count
    $cartCount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }

    echo json_encode([
        'success' => true,
        'cart_count' => $cartCount,
        'message' => 'Item added to cart'
    ]);
    exit;
}

if ($action === 'get') {
    $cart = $_SESSION['cart'] ?? [];
    $cartCount = 0;
    foreach ($cart as $item) {
        $cartCount += $item['quantity'];
    }

    echo json_encode([
        'success' => true,
        'cart' => $cart,
        'cart_count' => $cartCount
    ]);
    exit;
}

if ($action === 'remove') {
    $title = $_POST['title'] ?? '';
    $price = floatval($_POST['price'] ?? 0);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Remove item from cart
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($title, $price) {
        return !($item['title'] === $title && $item['price'] === $price);
    });

    // Reindex array
    $_SESSION['cart'] = array_values($_SESSION['cart']);

    // Calculate cart count
    $cartCount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }

    echo json_encode([
        'success' => true,
        'cart_count' => $cartCount,
        'message' => 'Item removed from cart'
    ]);
    exit;
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    
    echo json_encode([
        'success' => true,
        'cart_count' => 0,
        'message' => 'Cart cleared'
    ]);
    exit;
}

// If invalid action
http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Invalid action']);
exit;
