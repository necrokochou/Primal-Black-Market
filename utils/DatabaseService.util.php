<?php
require_once BASE_PATH . '/utils/envSetter.util.php';
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
        $sql = "SELECT * FROM users WHERE username = :identifier OR email = :identifier";
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

            $sql = "INSERT INTO users (username, email, password, alias, trust_level, is_vendor, is_admin, created_at) 
                    VALUES (:username, :email, :password, :alias, 0.0, false, false, NOW())";

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
        $sql = "SELECT user_id, username, email, alias, trustlevel, is_vendor, is_admin, created_at 
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
        // Step 0: Delete cart entries (FK: cart.user_id)
        $stmtCart = $this->pdo->prepare("DELETE FROM cart WHERE user_id = :id");
        $stmtCart->bindValue(':id', $userId);
        $stmtCart->execute();

        // Step 0.1: Delete transactions where user is the buyer (FK: transactions.buyer_id)
        $stmtTxn = $this->pdo->prepare("DELETE FROM transactions WHERE buyer_id = :id");
        $stmtTxn->bindValue(':id', $userId);
        $stmtTxn->execute();

        // Step 1: Get all listings by the user
        $stmt = $this->pdo->prepare("SELECT listing_id FROM listings WHERE vendor_id = :id");
        $stmt->bindValue(':id', $userId);
        $stmt->execute();
        $listings = $stmt->fetchAll();

        // Step 2: Delete each listing (also removes cart + transaction entries for each listing)
        foreach ($listings as $listing) {
            $this->deleteListing($listing['listing_id']);
        }

        // Step 3: Delete the user itself
        $stmtUser = $this->pdo->prepare("DELETE FROM users WHERE user_id = :id");
        $stmtUser->bindValue(':id', $userId);
        return $stmtUser->execute();
    }


    // Permanently delete a listing by ID
    public function deleteListing($listingId)
    {
        // Step 0: Delete cart items referencing this listing
        $stmtCart = $this->pdo->prepare("DELETE FROM cart WHERE listing_id = :id");
        $stmtCart->bindValue(':id', $listingId);
        $stmtCart->execute();

        // Step 1: Delete transactions tied to this listing
        $stmtTxn = $this->pdo->prepare("DELETE FROM transactions WHERE listing_id = :id");
        $stmtTxn->bindValue(':id', $listingId);
        $stmtTxn->execute();

        // Step 2: Delete the listing
        $stmt = $this->pdo->prepare("DELETE FROM listings WHERE listing_id = :id");
        $stmt->bindValue(':id', $listingId);
        return $stmt->execute();
    }
}
