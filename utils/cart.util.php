<?php
require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/dbConnect.util.php';
require_once UTILS_PATH . '/auth.util.php';

class CartHandler
{
    private PDO $pdo;
    private \App\Utils\Auth $auth;
    private ?string $userID;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        try {
            $this->pdo = connectPostgres();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Database connection failed']);
            exit;
        }

        $this->auth = new \App\Utils\Auth($this->pdo);
        $this->userID = $this->auth->getLoggedInUserID();

        if (!$this->userID) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            exit;
        }
    }

    public function getPdo(): PDO {
        return $this->pdo;
    }

    public function getCart(): array {
        $stmt = $this->pdo->prepare("
            SELECT c.Cart_ID, c.Listing_ID, c.Quantity,
                l.Title AS title, l.Price AS unit_price, l.Item_Image
            FROM cart c
            JOIN listings l ON l.Listing_ID = c.Listing_ID
            WHERE c.User_ID = :userID
        ");
        $stmt->execute(['userID' => $this->userID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addToCart($listingID, $quantity)
    {
        $stmt = $this->pdo->prepare("
            SELECT Quantity FROM cart
            WHERE User_ID = :userID AND Listing_ID = :listingID
        ");
        $stmt->execute(['userID' => $this->userID, 'listingID' => $listingID]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $stmt = $this->pdo->prepare("
                UPDATE cart SET Quantity = Quantity + :quantity
                WHERE User_ID = :userID AND Listing_ID = :listingID
            ");
            $stmt->execute([
                'quantity' => $quantity,
                'userID' => $this->userID,
                'listingID' => $listingID
            ]);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO cart (User_ID, Listing_ID, Quantity)
                VALUES (:userID, :listingID, :quantity)
            ");
            $stmt->execute([
                'userID' => $this->userID,
                'listingID' => $listingID,
                'quantity' => $quantity
            ]);
        }
    }

    public function updateItem($cartID, $quantity)
    {
        $stmt = $this->pdo->prepare("
            UPDATE cart SET Quantity = :quantity
            WHERE Cart_ID = :cartID AND User_ID = :userID
        ");
        $stmt->execute([
            'quantity' => $quantity,
            'cartID' => $cartID,
            'userID' => $this->userID
        ]);
    }

    public function removeItem($cartID)
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM cart WHERE Cart_ID = :cartID AND User_ID = :userID
        ");
        $stmt->execute([
            'cartID' => $cartID,
            'userID' => $this->userID
        ]);
    }

    public function clearCart($userId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
    }

    public function getCartCount($userID)
    {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(Quantity), 0) AS total
            FROM cart WHERE User_ID = :uid
        ");
        $stmt->execute(['uid' => $userID]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
}
