<?php
require_once BASE_PATH . '/../bootstrap.php';
require_once UTILS_PATH . '/DatabaseService.util.php';

session_start();

header('Content-Type: application/json');

// Only allow logged-in admin users
if (!isset($_SESSION['user']) || !($_SESSION['user']['is_admin'] ?? false)) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$db = DatabaseService::getInstance();

$action = $_POST['action'] ?? '';
$response = ['success' => false];

try {
    switch ($action) {
        case 'delete_user':
            $userId = $_POST['user_id'] ?? null;

            // Prevent self-deletion
            if ($userId === ($_SESSION['user']['id'] ?? null)) {
                $response['error'] = 'You cannot delete your own account while logged in.';
                break;
            }

            if ($userId && $db->deleteUser($userId)) {
                $response['success'] = true;
            } else {
                $response['error'] = 'User deletion failed';
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

        default:
            $response['error'] = 'Invalid action';
    }
} catch (Exception $e) {
    if (str_contains($e->getMessage(), 'foreign key') || str_contains($e->getMessage(), '23503')) {
        $response['error'] = 'Cannot delete this user — their account is still linked to other records (e.g., transactions or cart).';
    } else {
        $response['error'] = 'Exception: ' . $e->getMessage();
    }
}

echo json_encode($response);
