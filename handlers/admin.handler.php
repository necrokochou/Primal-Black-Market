<?php
// CRITICAL: Prevent ANY output before JSON response
ob_start();

// Custom error handler to prevent HTML error output
set_error_handler(function($severity, $message, $file, $line) {
    error_log("PHP Error: $message in $file on line $line");
    // Don't let PHP output HTML errors
    return true;
});

// Include bootstrap file with correct path
try {
    require_once __DIR__ . '/../bootstrap.php';
    require_once UTILS_PATH . '/DatabaseService.util.php';
} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Bootstrap error: ' . $e->getMessage()]);
    exit;
}

session_start();

// Clear any output that might have been generated
ob_clean();

// Set JSON header and disable error output to browser to prevent HTML in JSON response
header('Content-Type: application/json');
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Only allow logged-in admin users
$user = $_SESSION['user'] ?? null;
if (!isset($user) || !($user['is_admin'] ?? false)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Wrap everything in try-catch to ensure JSON response even on fatal errors
try {
    $db = DatabaseService::getInstance();
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$action = $_POST['action'] ?? '';
$response = ['success' => false];

try {
    switch ($action) {
        case 'delete_user':
            $userId = $_POST['user_id'] ?? null;
            
            // Debug logging
            error_log("Admin delete_user attempt - User ID: $userId, Session User: " . ($_SESSION['user']['user_id'] ?? 'null'));

            // Prevent self-deletion
            if ($userId === ($_SESSION['user']['user_id'] ?? null)) {
                $response['error'] = 'You cannot delete your own account while logged in.';
                break;
            }

            if (!$userId) {
                $response['error'] = 'User ID is required';
                break;
            }

            try {
                if ($db->deleteUser($userId)) {
                    $response['success'] = true;
                    error_log("User $userId successfully deleted");
                } else {
                    $response['error'] = 'User deletion failed';
                    error_log("User $userId deletion failed - database returned false");
                }
            } catch (Exception $e) {
                $response['error'] = $e->getMessage();
                error_log("User $userId deletion exception: " . $e->getMessage());
            }
            break;

        case 'delete_listing':
            $listingId = $_POST['listing_id'] ?? null;
            if ($listingId && $db->deleteListing($listingId)) {
                $response['success'] = true;
            } else {
                $response['error'] = 'Listing deletion failed';
            }
            break;

        case 'ban_user':
            $userId = $_POST['user_id'] ?? null;
            $banStatus = filter_var($_POST['ban_status'] ?? true, FILTER_VALIDATE_BOOLEAN);

            // Prevent self-banning
            if ($userId === ($_SESSION['user']['user_id'] ?? null)) {
                $response['error'] = 'You cannot ban your own account.';
                break;
            }

            if ($userId && $db->banUser($userId, $banStatus)) {
                $response['success'] = true;
                $response['banned'] = $banStatus;
            } else {
                $response['error'] = 'User ban operation failed';
            }
            break;

        default:
            $response['error'] = 'Invalid action';
    }
} catch (Exception $e) {
    error_log("Admin handler exception: " . $e->getMessage());
    if (strpos($e->getMessage(), 'foreign key') !== false || strpos($e->getMessage(), '23503') !== false) {
        $response['error'] = 'Cannot delete this user — their account is still linked to other records (e.g., transactions or cart).';
    } else {
        $response['error'] = 'Exception: ' . $e->getMessage();
    }
}

// End output buffering and ensure clean JSON output
if (ob_get_level() > 0) {
    ob_end_clean();
}

// Final check to ensure we only send JSON
header('Content-Type: application/json');
echo json_encode($response);
