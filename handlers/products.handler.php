<?php
/**
 * products.handler.php
 *
 * CRUD for listings
 * Table:
 *  listings(
 *    Listing_ID uuid pk,
 *    Vendor_ID uuid,
 *    Categories_ID uuid,
 *    Title varchar(256),
 *    Description text,
 *    Category varchar(100),
 *    Price real,
 *    Quantity int,
 *    Is_Active boolean,
 *    Publish_Date date,
 *    Item_Image varchar(256)
 *  )
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../bootstrap.php';
require_once UTILS_PATH . '/DatabaseService.util.php';

$db = DatabaseService::getInstance()->getConnection();
$userId = $_SESSION['user']['user_id']; // assumes UUID

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? ''; // works for both GET/POST/DELETE with query/string

try {
    switch ($method) {
        case 'GET':
            // read or list
            if ($action === 'read') {
                $id = $_GET['id'] ?? null;
                if (!$id) throw new Exception('Missing id');
                echo json_encode(readProduct($db, $id, $userId));
            } else {
                // default: list
                echo json_encode(listProducts($db, $userId));
            }
            break;

        case 'POST':
            // create or update or toggle/duplicate
            if ($action === 'create') {
                echo json_encode(createProduct($db, $userId));
            } elseif ($action === 'update') {
                echo json_encode(updateProduct($db, $userId));
            } elseif ($action === 'toggle') {
                echo json_encode(toggleProduct($db, $userId));
            } elseif ($action === 'duplicate') {
                echo json_encode(duplicateProduct($db, $userId));
            } else {
                throw new Exception('Invalid action');
            }
            break;

        case 'DELETE':
            // delete
            parse_str(file_get_contents('php://input'), $body);
            $id = $body['id'] ?? $_GET['id'] ?? null;
            if (!$id) throw new Exception('Missing id');
            echo json_encode(deleteProduct($db, $id, $userId));
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/* ============ HELPERS ============ */

function createProduct(PDO $db, string $vendorId): array
{
    // Expect multipart/form-data
    $title        = trim($_POST['title'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $category     = trim($_POST['category'] ?? '');
    $price        = $_POST['price'] ?? null;
    $quantity     = $_POST['stock'] ?? $_POST['quantity'] ?? null; // your JS used "stock"
    $categoriesId = $_POST['categories_id'] ?? null; // you can pass it; else resolve from "category"
    $status       = ($_POST['status'] ?? 'active') === 'active';
    $publishDate  = date('Y-m-d'); // now

    if ($title === '' || $description === '' || $category === '') {
        throw new Exception('Missing required fields');
    }
    if (!is_numeric($price) || !is_numeric($quantity)) {
        throw new Exception('Price and quantity must be numeric');
    }

    $imagePath = handleImageUpload($vendorId);

    // if you don't have categories_id in UI, you may want to resolve it here.
    if (!$categoriesId) {
        // Example: look up by name (optional)
        // $categoriesId = resolveCategoryIdByName($db, $category);
        // For now, fail if missing:
        $categoriesId = getDefaultCategoryId($db); // fallback if you want a default category
    }

    $sql = "INSERT INTO listings (Vendor_ID, Categories_ID, Title, Description, Category, Price, Quantity, Is_Active, Publish_Date, Item_Image)
            VALUES (:vendor, :cat_id, :title, :description, :category, :price, :quantity, :active, :publish_date, :item_image)
            RETURNING Listing_ID";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        'vendor'       => $vendorId,
        'cat_id'       => $categoriesId,
        'title'        => $title,
        'description'  => $description,
        'category'     => $category,
        'price'        => (float)$price,
        'quantity'     => (int)$quantity,
        'active'       => $status,
        'publish_date' => $publishDate,
        'item_image'   => $imagePath,
    ]);

    $id = $stmt->fetchColumn();

    return [
        'success' => true,
        'message' => 'Product created successfully',
        'id'      => $id,
    ];
}

function readProduct(PDO $db, string $id, string $vendorId): array
{
    $stmt = $db->prepare("SELECT * FROM listings WHERE Listing_ID = :id AND Vendor_ID = :vid LIMIT 1");
    $stmt->execute(['id' => $id, 'vid' => $vendorId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        return ['success' => false, 'message' => 'Product not found'];
    }

    return ['success' => true, 'product' => $row];
}

function listProducts(PDO $db, string $vendorId): array
{
    // filters
    $search   = trim($_GET['search'] ?? '');
    $category = trim($_GET['category'] ?? '');
    $status   = $_GET['status'] ?? 'all';

    // pagination
    $page     = max(1, (int)($_GET['page'] ?? 1));
    $limit    = min(100, max(1, (int)($_GET['limit'] ?? 20)));
    $offset   = ($page - 1) * $limit;

    $where   = ["Vendor_ID = :vid"];
    $params  = ['vid' => $vendorId];

    if ($search !== '') {
        $where[] = "(LOWER(Title) LIKE :search OR LOWER(Description) LIKE :search)";
        $params['search'] = '%' . strtolower($search) . '%';
    }

    if ($category !== '') {
        $where[] = "LOWER(Category) = :category";
        $params['category'] = strtolower($category);
    }

    if ($status !== 'all') {
        $where[] = "Is_Active = :active";
        $params['active'] = ($status === 'active');
    }

    $whereSql = implode(' AND ', $where);

    $totalStmt = $db->prepare("SELECT COUNT(*) FROM listings WHERE $whereSql");
    $totalStmt->execute($params);
    $total = (int)$totalStmt->fetchColumn();

    $sql = "SELECT * FROM listings WHERE $whereSql ORDER BY Publish_Date DESC LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($sql);

    foreach ($params as $k => $v) {
        $stmt->bindValue(':' . $k, $v);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'success'   => true,
        'data'      => $rows,
        'page'      => $page,
        'limit'     => $limit,
        'total'     => $total,
        'totalPages'=> (int)ceil($total / $limit),
    ];
}

function updateProduct(PDO $db, string $vendorId): array
{
    $id = $_POST['id'] ?? null;
    if (!$id) throw new Exception('Missing id');

    // Ensure ownership
    if (!ownsProduct($db, $id, $vendorId)) {
        http_response_code(403);
        return ['success' => false, 'message' => 'Forbidden'];
    }

    $title        = trim($_POST['title'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $category     = trim($_POST['category'] ?? '');
    $price        = $_POST['price'] ?? null;
    $quantity     = $_POST['stock'] ?? $_POST['quantity'] ?? null;
    $categoriesId = $_POST['categories_id'] ?? null;
    $isActive     = isset($_POST['status']) ? ($_POST['status'] === 'active') : null;

    $fields = [];
    $params = ['id' => $id];

    if ($title !== '')         { $fields[] = "Title = :title";                $params['title']        = $title; }
    if ($description !== '')   { $fields[] = "Description = :description";    $params['description']  = $description; }
    if ($category !== '')      { $fields[] = "Category = :category";          $params['category']     = $category; }
    if ($categoriesId !== null){ $fields[] = "Categories_ID = :categories_id";$params['categories_id']= $categoriesId; }
    if ($price !== null)       { if (!is_numeric($price)) throw new Exception('Invalid price');
                                 $fields[] = "Price = :price";                $params['price']        = (float)$price; }
    if ($quantity !== null)    { if (!is_numeric($quantity)) throw new Exception('Invalid quantity');
                                 $fields[] = "Quantity = :quantity";          $params['quantity']     = (int)$quantity; }
    if ($isActive !== null)    { $fields[] = "Is_Active = :active";           $params['active']       = $isActive; }

    // Handle (optional) new image
    $newImage = handleImageUpload($vendorId, true);
    if ($newImage) {
        $fields[] = "Item_Image = :item_image";
        $params['item_image'] = $newImage;
    }

    if (empty($fields)) {
        return ['success' => true, 'message' => 'Nothing to update'];
    }

    $sql = "UPDATE listings SET " . implode(", ", $fields) . " WHERE Listing_ID = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    return ['success' => true, 'message' => 'Product updated successfully'];
}

function deleteProduct(PDO $db, string $id, string $vendorId): array
{
    if (!ownsProduct($db, $id, $vendorId)) {
        http_response_code(403);
        return ['success' => false, 'message' => 'Forbidden'];
    }

    $stmt = $db->prepare("DELETE FROM listings WHERE Listing_ID = :id");
    $stmt->execute(['id' => $id]);

    return ['success' => true, 'message' => 'Product deleted successfully'];
}

function toggleProduct(PDO $db, string $vendorId): array
{
    $id = $_POST['id'] ?? null;
    if (!$id) throw new Exception('Missing id');

    if (!ownsProduct($db, $id, $vendorId)) {
        http_response_code(403);
        return ['success' => false, 'message' => 'Forbidden'];
    }

    $stmt = $db->prepare("UPDATE listings SET Is_Active = NOT Is_Active WHERE Listing_ID = :id RETURNING Is_Active");
    $stmt->execute(['id' => $id]);
    $newState = $stmt->fetchColumn();

    return [
        'success' => true,
        'message' => 'Product status toggled',
        'active'  => (bool)$newState
    ];
}

function duplicateProduct(PDO $db, string $vendorId): array
{
    $id = $_POST['id'] ?? null;
    if (!$id) throw new Exception('Missing id');

    if (!ownsProduct($db, $id, $vendorId)) {
        http_response_code(403);
        return ['success' => false, 'message' => 'Forbidden'];
    }

    $stmt = $db->prepare("SELECT * FROM listings WHERE Listing_ID = :id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) throw new Exception('Product not found');

    $stmt = $db->prepare("
        INSERT INTO listings (Vendor_ID, Categories_ID, Title, Description, Category, Price, Quantity, Is_Active, Publish_Date, Item_Image)
        VALUES (:vendor, :cat_id, :title, :description, :category, :price, :quantity, :active, :publish_date, :item_image)
        RETURNING Listing_ID
    ");
    $stmt->execute([
        'vendor'       => $row['vendor_id'],
        'cat_id'       => $row['categories_id'],
        'title'        => $row['title'] . ' (Copy)',
        'description'  => $row['description'],
        'category'     => $row['category'],
        'price'        => $row['price'],
        'quantity'     => $row['quantity'],
        'active'       => $row['is_active'],
        'publish_date' => date('Y-m-d'),
        'item_image'   => $row['item_image'],
    ]);

    $newId = $stmt->fetchColumn();

    return [
        'success' => true,
        'message' => 'Product duplicated successfully',
        'id'      => $newId
    ];
}

function ownsProduct(PDO $db, string $listingId, string $vendorId): bool
{
    $stmt = $db->prepare("SELECT 1 FROM listings WHERE Listing_ID = :id AND Vendor_ID = :vid");
    $stmt->execute(['id' => $listingId, 'vid' => $vendorId]);
    return (bool)$stmt->fetchColumn();
}

/**
 * Returns stored relative path or null
 */
function handleImageUpload(string $vendorId, bool $optional = false): ?string
{
    if (
        !isset($_FILES['images']) &&
        !isset($_FILES['image']) &&
        $optional
    ) {
        return null;
    }

    $fileArray = null;

    if (isset($_FILES['images'])) {
        // Take first only (schema supports single path)
        if (is_array($_FILES['images']['name'])) {
            // multiple
            $fileArray = [
                'name'     => $_FILES['images']['name'][0] ?? null,
                'type'     => $_FILES['images']['type'][0] ?? null,
                'tmp_name' => $_FILES['images']['tmp_name'][0] ?? null,
                'error'    => $_FILES['images']['error'][0] ?? UPLOAD_ERR_NO_FILE,
                'size'     => $_FILES['images']['size'][0] ?? 0,
            ];
        } else {
            // single input named images
            $fileArray = $_FILES['images'];
        }
    } elseif (isset($_FILES['image'])) {
        $fileArray = $_FILES['image'];
    }

    if (!$fileArray || $fileArray['error'] === UPLOAD_ERR_NO_FILE) {
        if ($optional) return null;
        throw new Exception('Image is required');
    }

    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Image upload failed');
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$fileArray['type']])) {
        throw new Exception('Unsupported image type');
    }

    if ($fileArray['size'] > 3 * 1024 * 1024) {
        throw new Exception('Image exceeds 3MB');
    }

    $ext = $allowed[$fileArray['type']];
    $name = bin2hex(random_bytes(16)) . '.' . $ext;

    $baseDir = __DIR__ . '/public/uploads/products';
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0775, true);
    }

    $vendorDir = $baseDir . '/' . $vendorId;
    if (!is_dir($vendorDir)) {
        mkdir($vendorDir, 0775, true);
    }

    $dest = $vendorDir . '/' . $name;

    if (!move_uploaded_file($fileArray['tmp_name'], $dest)) {
        throw new Exception('Failed to move uploaded image');
    }

    // Return relative path that you store in DB (adjust to how you serve static files)
    $relative = "/uploads/products/{$vendorId}/{$name}";
    return $relative;
}

/**
 * If you want a fallback categories_id
 * Adjust/replace with your own logic.
 */
function getDefaultCategoryId(PDO $db): string
{
    // Get first category id as default (or create a "Misc" category)
    $stmt = $db->query("SELECT categories_id FROM categories LIMIT 1");
    $id = $stmt->fetchColumn();
    if (!$id) {
        throw new Exception('No categories available. Provide categories_id.');
    }
    return $id;
}
