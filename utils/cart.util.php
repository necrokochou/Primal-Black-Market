<?php
require_once UTILS_PATH . '/dbConnect.util.php';
require_once UTILS_PATH . '/auth.util.php';

class CartHandler {
    private PDO $pdo;
    private \App\Utils\Auth $auth;
    private ?string $userID;

    public function __construct() {
        session_start(); // ensure session is started
        try {
            $this->pdo = connectPostgres();
            echo "Connected to Postgres database successfully."; // debug
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage(); // debug
        }
        $this->auth = new \App\Utils\Auth($this->pdo);
        $this->userID = $this->auth->getLoggedInUserID();

        if (!$this->userID) {
            $error = "Not logged in";
            http_response_code(401);
            // require TEMPLATES_PATH . '/error.template.php'; // TODO: error page
            exit;
        }
    }

    public function getCart() {
        $stmt = $this->pdo->prepare("
            SELECT ci.Cart_Item_ID, ci.Listins_ID, ci.Quantity, p.Name, p.Total_Price
            FROM cart_items ci
            JOIN carts c ON c.Cart_ID = ci.Cart_ID
            JOIN listings p ON p.Listings_ID = ci.Listings_ID
            WHERE c.User_ID = :userID
        ");
        $stmt->execute(['userID' => $this->userID]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ⬇️ Template display
        // require TEMPLATES_PATH . '/cart.view.template.php'; // TODO: cart
    }

    public function addToCart($listingsID, $quantity) {
        $cartID = $this->getOrCreateCart();

        $stmt = $this->pdo->prepare("
            SELECT Cart_Item_ID, Quantity FROM cart_items
            WHERE Cart_ID = :cartID AND Listings_ID = :listingsID
        ");
        $stmt->execute(['cartID' => $cartID, 'listingsID' => $listingsID]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $stmt = $this->pdo->prepare("
                UPDATE cart_items SET Quantity = Quantity + :quantity
                WHERE Cart_Item_ID = :id
            ");
            $stmt->execute(['quantity' => $quantity, 'id' => $item['Cart_Item_ID']]);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO cart_items (Cart_ID, Listings_ID, Quantity)
                VALUES (:cartID, :listingsID, :quantity)
            ");
            $stmt->execute(['cartID' => $cartID, 'listingsID' => $listingsID, 'quantity' => $quantity]);
        }

        // After successful add, redirect to cart
        header('Location: /pages/cart.php?action=view'); // TODO: Redirect to cart after add
        exit;
    }

    public function updateItem($itemID, $quantity) {
        $stmt = $this->pdo->prepare("
            UPDATE cart_items SET quantity = :quantity
            WHERE cart_item_id = :itemID
        ");
        $stmt->execute(['quantity' => $quantity, 'itemID' => $itemID]);

        header('Location: /pages/cart.php?action=view'); // TODO: Redirect to cart after update
        exit;
    }

    public function removeItem($itemID) {
        $stmt = $this->pdo->prepare("
            DELETE FROM cart_items WHERE Cart_Item_ID = :itemID
        ");
        $stmt->execute(['itemID' => $itemID]);

        header('Location: /pages/cart.php?action=view'); // TODO: Redirect to cart after remove
        exit;
    }

    private function getOrCreateCart() {
        $stmt = $this->pdo->prepare("
            SELECT Cart_ID FROM carts WHERE User_ID = :userID
        ");
        $stmt->execute(['userID' => $this->userID]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart) return $cart['Cart_ID'];

        $stmt = $this->pdo->prepare("
            INSERT INTO carts (User_ID) VALUES (:userID) RETURNING Cart_ID
        ");
        $stmt->execute(['userID' => $this->userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['Cart_ID'];
    }
}
