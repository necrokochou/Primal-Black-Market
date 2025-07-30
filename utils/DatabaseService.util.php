<?php
// Ensure bootstrap.php constants are available
require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';
class DatabaseService
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            // Use environment variables for database connection
            $host = $_ENV['PG_HOST'] ?? 'host.docker.internal';
            $port = $_ENV['PG_PORT'] ?? '5432';
            $dbname = $_ENV['PG_DB'] ?? 'primal-black-market';
            $user = $_ENV['PG_USER'] ?? 'user';
            $password = $_ENV['PG_PASS'] ?? 'password';

            $this->pdo = new PDO(
                "pgsql:host=$host;port=$port;dbname=$dbname",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    // Get all categories
    public function getCategories()
    {
        $stmt = $this->pdo->query("SELECT categories_id, name, description FROM categories ORDER BY name");
        return $stmt->fetchAll();
    }

    // Get all active listings
    public function getListings($category = null, $limit = null, $offset = 0, $includeInactive = false)
    {
        $sql = "SELECT l.listing_id, l.vendor_id, l.categories_id, l.title, l.description, 
                       l.category, l.price, l.quantity, l.is_active, l.publish_date, l.item_image,
                       c.name as category_name 
                FROM listings l 
                JOIN categories c ON l.categories_id = c.categories_id";

        $params = [];
        $whereConditions = [];

        if (!$includeInactive) {
            $whereConditions[] = "l.is_active = true";
        }

        if ($category) {
            $whereConditions[] = "c.name = :category";
            $params['category'] = $category;
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        $sql .= " ORDER BY l.publish_date DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            if (in_array($key, ['limit', 'offset'])) {
                $stmt->bindValue(":$key", (int)$value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":$key", $value);
            }
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get listings by category for homepage
    public function getListingsByCategory($limit = 6)
    {
        $categories = $this->getCategories();
        $result = [];

        foreach ($categories as $category) {
            $listings = $this->getListings($category['name'], $limit);
            if (!empty($listings)) {
                $result[$category['name']] = $listings;
            }
        }

        return $result;
    }

    // Get featured listings for homepage
    public function getFeaturedListings($limit = 8)
    {
        $sql = "SELECT l.listing_id, l.vendor_id, l.categories_id, l.title, l.description, 
                       l.category, l.price, l.quantity, l.is_active, l.publish_date, l.item_image,
                       c.name as category_name 
                FROM listings l 
                JOIN categories c ON l.categories_id = c.categories_id 
                WHERE l.is_active = true 
                ORDER BY l.publish_date DESC 
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get user by username or email
    public function getUser($usernameOrEmail)
    {
        $sql = "SELECT * FROM users WHERE \"Username\" = :identifier OR \"Email\" = :identifier";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':identifier', $usernameOrEmail);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Create new user
    public function createUser($username, $email, $password, $alias = null)
    {
        try {
            // Check if username already exists
            $checkSql = "SELECT user_id FROM users WHERE username = :username";
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->bindValue(':username', $username);
            $checkStmt->execute();

            if ($checkStmt->fetch()) {
                return ['success' => false, 'error' => 'Username already exists'];
            }

            // Check if email already exists
            $checkSql = "SELECT user_id FROM users WHERE email = :email";
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->bindValue(':email', $email);
            $checkStmt->execute();

            if ($checkStmt->fetch()) {
                return ['success' => false, 'error' => 'Email already registered'];
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $alias = $alias ?: $username;

            $sql = "INSERT INTO users (\"Username\", \"Email\", \"Password\", \"Alias\", \"TrustLevel\", \"Is_Vendor\", \"Is_Admin\", \"Created_At\") 
                    VALUES (:username, :email, :password, :alias, 0.0, false, false, CURRENT_TIMESTAMP)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':password', $hashedPassword);
            $stmt->bindValue(':alias', $alias);

            $success = $stmt->execute();

            if ($success) {
                return ['success' => true];
            } else {
                return ['success' => false, 'error' => 'Failed to create user'];
            }
        } catch (PDOException $e) {
            error_log("Database error in createUser: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error occurred'];
        }
    }

    // Get cart items for user
    public function getCartItems($userId)
    {
        // For now, we'll use session-based cart
        // In the future, this can be moved to database
        return $_SESSION['cart'] ?? [];
    }

    // Add item to cart (session-based for now)
    public function addToCart($productId, $quantity = 1)
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            // Get product details
            $product = $this->getListingById($productId);
            if ($product) {
                $_SESSION['cart'][$productId] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            }
        }
    }

    // Get single listing by ID
    public function getListingById($listingId)
    {
        $sql = "SELECT l.*, c.name as category_name 
                FROM listings l 
                JOIN categories c ON l.category_id = c.category_id 
                WHERE l.listing_id = :listing_id AND l.is_active = true";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':listing_id', (int)$listingId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Search listings
    public function searchListings($searchTerm, $category = null, $limit = 20)
    {
        $sql = "SELECT l.*, c.name as category_name 
                FROM listings l 
                JOIN categories c ON l.category_id = c.category_id 
                WHERE l.is_active = true 
                AND (l.name ILIKE :search OR l.description ILIKE :search)";

        $params = ['search' => "%$searchTerm%"];

        if ($category) {
            $sql .= " AND c.name = :category";
            $params['category'] = $category;
        }

        $sql .= " ORDER BY l.created_at DESC LIMIT :limit";
        $params['limit'] = $limit;

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            if ($key === 'limit') {
                $stmt->bindValue(":$key", (int)$value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":$key", $value);
            }
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get all users (for admin dashboard)
    // public function getAllUsers()
    // {
    //     $sql = "SELECT user_id, username, email, alias, trustlevel, is_vendor, is_admin, 
    //         FROM users";

    //     $stmt = $this->pdo->query($sql);
    //     $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     //return $stmt->fetchAll(PDO::FETCH_ASSOC);


    //     // Ensure created_at is always set to a valid value
    //     foreach ($users as &$user) {
    //         if (empty($user['created_at']) || strtotime($user['created_at']) === false) {
    //             $user['created_at'] = date('Y-m-d H:i:s');
    //         }
    //     }

    //     return $users;
    // }

    //use this when created_at is added to users.model.sql
    public function getAllUsers()
    {
        $sql = "SELECT user_id, username, email, alias, 
                trustlevel, is_vendor, is_admin, 
                is_banned, created_at 
            FROM users 
            ORDER BY created_at DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get user count
    public function getUserCount()
    {
        $sql = "SELECT COUNT(*) FROM users";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    // Get listing count
    public function getListingCount($activeOnly = true)
    {
        $sql = "SELECT COUNT(*) FROM listings";
        if ($activeOnly) {
            $sql .= " WHERE is_active = true";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
    // Permanently delete a user by ID
    public function deleteUser($userId)
    {
        try {
            // First, let's try to delete cart entries with both possible column names
            try {
                $stmtCart = $this->pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmtCart->execute([$userId]);
                error_log("Deleted " . $stmtCart->rowCount() . " cart entries for user $userId");
            } catch (PDOException $e) {
                // Try with the other column name format
                try {
                    $stmtCart = $this->pdo->prepare("DELETE FROM cart WHERE \"user_id\" = ?");
                    $stmtCart->execute([$userId]);
                    error_log("Deleted " . $stmtCart->rowCount() . " cart entries for user $userId (quoted)");
                } catch (PDOException $e2) {
                    error_log("Cart deletion failed with both approaches: " . $e2->getMessage());
                    // Continue anyway - cart entries might not exist
                }
            }

            // Handle transactions - try to nullify buyer references
            try {
                $stmtTxn = $this->pdo->prepare("UPDATE transactions SET buyer_id = NULL WHERE buyer_id = ?");
                $stmtTxn->execute([$userId]);
                error_log("Nullified buyer references in " . $stmtTxn->rowCount() . " transactions");
            } catch (PDOException $e) {
                error_log("Transaction update failed: " . $e->getMessage());
                // Continue anyway
            }

            // Handle listings - deactivate them
            try {
                $stmtListings = $this->pdo->prepare("UPDATE listings SET is_active = false WHERE vendor_id = ?");
                $stmtListings->execute([$userId]);
                error_log("Deactivated " . $stmtListings->rowCount() . " listings for user $userId");
            } catch (PDOException $e) {
                error_log("Listings update failed: " . $e->getMessage());
                // Continue anyway
            }

            // Finally, delete the user
            $stmtUser = $this->pdo->prepare("DELETE FROM users WHERE user_id = ?");
            $result = $stmtUser->execute([$userId]);
            
            if ($result && $stmtUser->rowCount() > 0) {
                error_log("Successfully deleted user $userId");
                return true;
            } else {
                error_log("Failed to delete user $userId - user not found");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Error deleting user $userId: " . $e->getMessage());
            throw new Exception("Database error occurred while deleting user: " . $e->getMessage());
        }
    }


    // Permanently delete a listing by ID
    public function deleteListing($listingId)
    {
        // Step 0: Delete cart items referencing this listing
        $stmtCart = $this->pdo->prepare("DELETE FROM cart WHERE \"Listing_ID\" = :id");
        $stmtCart->bindValue(':id', $listingId);
        $stmtCart->execute();

        // Step 1: Delete transactions tied to this listing
        $stmtTxn = $this->pdo->prepare("DELETE FROM transactions WHERE \"Listing_ID\" = :id");
        $stmtTxn->bindValue(':id', $listingId);
        $stmtTxn->execute();

        // Step 2: Delete the listing
        $stmt = $this->pdo->prepare("DELETE FROM listings WHERE \"Listing_ID\" = :id");
        $stmt->bindValue(':id', $listingId);
        return $stmt->execute();
    }

    // Ban/Unban user functionality
    public function banUser($userId, $banned = true)
    {
        try {
            $sql = "UPDATE users SET is_banned = :banned WHERE user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':banned', $banned, PDO::PARAM_BOOL);
            $stmt->bindValue(':user_id', $userId);
            $result = $stmt->execute();
            
            if ($result && $stmt->rowCount() > 0) {
                error_log("Successfully " . ($banned ? 'banned' : 'unbanned') . " user $userId");
                return true;
            } else {
                error_log("Failed to " . ($banned ? 'ban' : 'unban') . " user $userId - user not found or no changes made");
                return false;
            }
        } catch (Exception $e) {
            error_log("Error " . ($banned ? 'banning' : 'unbanning') . " user $userId: " . $e->getMessage());
            throw $e;
        }
    }

    // Get user ban status
    public function isUserBanned($userId)
    {
        $sql = "SELECT is_banned FROM users WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (bool)$result['is_banned'] : false;
    }
}
