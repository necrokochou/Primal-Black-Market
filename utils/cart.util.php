<?php
require_once UTILS_PATH . '/dbConnectPostgresql.util.php';
require_once UTILS_PATH . '/auth.util.php';

class CartHandler {
    private PDO $pdo;
    private \App\Utils\Auth $auth;
    private ?string $userID;

    public function __construct() {
        session_start(); // ensure session is started
        $this->pdo = DBConnectPostgresql::connect();
        $this->auth = new \App\Utils\Auth($this->pdo);
        $this->userID = $this->auth->getLoggedInUserID();

        if (!$this->userID) {
            $error = "Not logged in";
            http_response_code(401);
            require TEMPLATES_PATH . '/error.template.php';
            exit;
        }
    }

    public function getCart() {
        $stmt = $this->pdo->prepare("
            SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.name, p.price
            FROM cart_items ci
            JOIN carts c ON c.cart_id = ci.cart_id
            JOIN products p ON p.product_id = ci.product_id
            WHERE c.user_id = :userID
        ");
        $stmt->execute(['userID' => $this->userID]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ⬇️ Template display
        require TEMPLATES_PATH . '/cart.view.template.php';
    }

    public function addToCart($productID, $quantity) {
        $cartID = $this->getOrCreateCart();

        $stmt = $this->pdo->prepare("
            SELECT cart_item_id, quantity FROM cart_items
            WHERE cart_id = :cartID AND product_id = :productID
        ");
        $stmt->execute(['cartID' => $cartID, 'productID' => $productID]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $stmt = $this->pdo->prepare("
                UPDATE cart_items SET quantity = quantity + :quantity
                WHERE cart_item_id = :id
            ");
            $stmt->execute(['quantity' => $quantity, 'id' => $item['cart_item_id']]);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO cart_items (cart_id, product_id, quantity)
                VALUES (:cartID, :productID, :quantity)
            ");
            $stmt->execute(['cartID' => $cartID, 'productID' => $productID, 'quantity' => $quantity]);
        }

        // After successful add, redirect to cart
        header('Location: /pages/cart.php?action=view');
        exit;
    }

    public function updateItem($itemID, $quantity) {
        $stmt = $this->pdo->prepare("
            UPDATE cart_items SET quantity = :quantity
            WHERE cart_item_id = :itemID
        ");
        $stmt->execute(['quantity' => $quantity, 'itemID' => $itemID]);

        header('Location: /pages/cart.php?action=view');
        exit;
    }

    public function removeItem($itemID) {
        $stmt = $this->pdo->prepare("
            DELETE FROM cart_items WHERE cart_item_id = :itemID
        ");
        $stmt->execute(['itemID' => $itemID]);

        header('Location: /pages/cart.php?action=view');
        exit;
    }

    private function getOrCreateCart() {
        $stmt = $this->pdo->prepare("
            SELECT cart_id FROM carts WHERE user_id = :userID
        ");
        $stmt->execute(['userID' => $this->userID]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart) return $cart['cart_id'];

        $stmt = $this->pdo->prepare("
            INSERT INTO carts (user_id) VALUES (:userID) RETURNING cart_id
        ");
        $stmt->execute(['userID' => $this->userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['cart_id'];
    }
}
