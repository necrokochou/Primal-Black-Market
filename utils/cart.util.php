<?php
require_once UTILS_PATH . '/envSetter.util.php';
require_once UTILS_PATH . '/auth.util.php';

class CartHandler {
    private PDO $pdo;
    private \App\Utils\Auth $auth;
    private ?string $userID;

    public function __construct() {
        session_start(); // ensure session is started
        
        // Initialize database connection using the same pattern as other utils
        $pgConfig = getPostgresEnv();
        $dsn = "pgsql:host={$pgConfig['host']};port={$pgConfig['port']};dbname={$pgConfig['db']}";
        
        try {
            $this->pdo = new PDO($dsn, $pgConfig['user'], $pgConfig['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (PDOException $e) {
            die("❌ Database connection failed: " . $e->getMessage());
        }
        
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
            SELECT c.Cart_ID, c.User_ID, c.Listing_ID, c.Quantity, l.Title, l.Price
            FROM cart c
            JOIN listings l ON l.Listing_ID = c.Listing_ID
            WHERE c.User_ID = :userID
        ");
        $stmt->execute(['userID' => $this->userID]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ⬇️ Template display
        require TEMPLATES_PATH . '/cart.view.template.php';
    }

    public function addToCart($listingID, $quantity) {
        // Check if item already exists in cart
        $stmt = $this->pdo->prepare("
            SELECT Cart_ID, Quantity FROM cart
            WHERE User_ID = :userID AND Listing_ID = :listingID
        ");
        $stmt->execute(['userID' => $this->userID, 'listingID' => $listingID]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Update existing cart item
            $stmt = $this->pdo->prepare("
                UPDATE cart SET Quantity = Quantity + :quantity
                WHERE Cart_ID = :cartID
            ");
            $stmt->execute(['quantity' => $quantity, 'cartID' => $item['Cart_ID']]);
        } else {
            // Insert new cart item
            $stmt = $this->pdo->prepare("
                INSERT INTO cart (User_ID, Listing_ID, Quantity, Added_At)
                VALUES (:userID, :listingID, :quantity, CURRENT_TIMESTAMP)
            ");
            $stmt->execute(['userID' => $this->userID, 'listingID' => $listingID, 'quantity' => $quantity]);
        }

        // After successful add, redirect to cart
        header('Location: /pages/cart.php?action=view');
        exit;
    }

    public function updateItem($cartID, $quantity) {
        $stmt = $this->pdo->prepare("
            UPDATE cart SET Quantity = :quantity
            WHERE Cart_ID = :cartID AND User_ID = :userID
        ");
        $stmt->execute(['quantity' => $quantity, 'cartID' => $cartID, 'userID' => $this->userID]);

        header('Location: /pages/cart.php?action=view');
        exit;
    }

    public function removeItem($cartID) {
        $stmt = $this->pdo->prepare("
            DELETE FROM cart WHERE Cart_ID = :cartID AND User_ID = :userID
        ");
        $stmt->execute(['cartID' => $cartID, 'userID' => $this->userID]);

        header('Location: /pages/cart.php?action=view');
        exit;
    }
}
