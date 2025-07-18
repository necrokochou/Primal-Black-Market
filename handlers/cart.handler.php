<?php
require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';
require_once UTILS_PATH . '/cart.util.php';

$cart = new CartHandler();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'view') {
    $cart->getCart();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_GET['action'] === 'add') {
        $cart->addToCart($_POST['product_id'], $_POST['quantity']);
    } elseif ($_GET['action'] === 'remove') {
        $cart->removeItem($_POST['item_id']);
    }
}
