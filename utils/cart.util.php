<?php
require_once UTILS_PATH . '/dbConnect.util.php';
require_once UTILS_PATH . '/auth.util.php';

class CartHandler {
    private PDO $pdo;
    private \App\Utils\Auth $auth;
    private ?string $userID;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        try {
            $this->pdo = connectPostgres();
            // echo "Connected to Postgres database successfully.";
        } catch (PDOException $e) {
            // echo "Connection failed: " . $e->getMessage();
        }
        $this->auth = new \App\Utils\Auth($this->pdo);
        $this->userID = $this->auth->getLoggedInUserID();

        if (!$this->userID) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            exit;
        }
    }

    public function getCart(): array {
        $stmt = $this->pdo->prepare("
            SELECT c.Cart_ID, c.Listing_ID, c.Quantity, l.Name, l.Total_Price
            FROM cart c
            JOIN listings l ON l.Listing_ID = c.Listing_ID
            WHERE c.User_ID = :userID
        ");
        $stmt->execute(['userID' => $this->userID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addToCart($listingID, $quantity) {
        // Check if item already exists in the user's cart
        $stmt = $this->pdo->prepare("
            SELECT Quantity FROM cart
            WHERE User_ID = :userID AND Listing_ID = :listingID
        ");
        $stmt->execute(['userID' => $this->userID, 'listingID' => $listingID]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update existing item quantity
            $stmt = $this->pdo->prepare("
                UPDATE cart SET Quantity = Quantity + :quantity
                WHERE User_ID = :userID AND Listing_ID = :listingID
            ");
            $stmt->execute(['quantity' => $quantity, 'userID' => $this->userID, 'listingID' => $listingID]);
        } else {
            // Insert new item
            $stmt = $this->pdo->prepare("
                INSERT INTO cart (User_ID, Listing_ID, Quantity)
                VALUES (:userID, :listingID, :quantity)
            ");
            $stmt->execute(['userID' => $this->userID, 'listingID' => $listingID, 'quantity' => $quantity]);
        }
    }

    public function updateItem($itemID, $quantity) {
        $stmt = $this->pdo->prepare("
            UPDATE cart_items SET quantity = :quantity
            WHERE cart_item_id = :itemID
        ");
        $stmt->execute(['quantity' => $quantity, 'itemID' => $itemID]);
    }

    public function removeItem($listingID) {
        $stmt = $this->pdo->prepare("
            DELETE FROM cart WHERE User_ID = :userID AND Listing_ID = :listingID
        ");
        $stmt->execute(['userID' => $this->userID, 'listingID' => $listingID]);
    }

    public function getCartCount($userID) {
        $stmt = $this->db->prepare("SELECT SUM(Quantity) AS total FROM cart WHERE User_ID = :uid");
        $stmt->execute([':uid' => $userID]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    // private function getOrCreateCart() {
    //     $stmt = $this->pdo->prepare("
    //         SELECT Cart_ID FROM carts WHERE User_ID = :userID
    //     ");
    //     $stmt->execute(['userID' => $this->userID]);
    //     $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    //     if ($cart) return $cart['Cart_ID'];

    //     $stmt = $this->pdo->prepare("
    //         INSERT INTO carts (User_ID) VALUES (:userID) RETURNING Cart_ID
    //     ");
    //     $stmt->execute(['userID' => $this->userID]);
    //     return $stmt->fetch(PDO::FETCH_ASSOC)['Cart_ID'];
    // }
}
