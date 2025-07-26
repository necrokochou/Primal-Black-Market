<?php
session_start();
header('Content-Type: application/json');
error_log(print_r($_SESSION['user'], true));

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../bootstrap.php';
require_once UTILS_PATH . '/DatabaseService.util.php';

$db = DatabaseService::getInstance()->getConnection();
$userId = $_SESSION['user']['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['settingType'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$settingType = $data['settingType'];
$response = ['success' => false, 'message' => 'Unknown error'];

try {
    switch ($settingType) {
        case 'alias':
            $alias = trim($data['alias'] ?? '');
            if (!$alias) {
                throw new Exception('Alias cannot be empty');
            }
            $stmt = $db->prepare("UPDATE users SET alias = :alias WHERE user_id = :id");
            $stmt->execute(['alias' => $alias, 'id' => $userId]);
            $_SESSION['user']['alias'] = $alias;
            $response = ['success' => true, 'message' => 'Alias updated successfully'];
            break;

        case 'email':
            $email = trim($data['email'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email');
            }
            $stmt = $db->prepare("UPDATE users SET email = :email WHERE user_id = :id");
            $stmt->execute(['email' => $email, 'id' => $userId]);
            $_SESSION['user']['email'] = $email;
            $response = ['success' => true, 'message' => 'Email updated successfully'];
            break;

        case 'password':
            $current = $data['current_password'] ?? '';
            $new = $data['new_password'] ?? '';

            // Get current password from DB
            $stmt = $db->prepare("SELECT password FROM users WHERE user_id = :id");
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($current, $user['password'])) {
                throw new Exception('Incorrect current password');
            }

            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = :password WHERE user_id = :id");
            $stmt->execute(['password' => $hashed, 'id' => $userId]);
            $response = ['success' => true, 'message' => 'Password updated successfully'];
            break;

        case 'delete':
            // Delete dependent data first (example: listings, transactions, cart)
            $db->prepare("DELETE FROM cart WHERE user_id = :id")->execute(['id' => $userId]);
            $db->prepare("DELETE FROM transactions WHERE user_id = :id")->execute(['id' => $userId]);
            $db->prepare("DELETE FROM listings WHERE vendor_id = :id")->execute(['id' => $userId]);
            $db->prepare("DELETE FROM users WHERE user_id = :id")->execute(['id' => $userId]);

            session_destroy();
            $response = ['success' => true, 'message' => 'Account deleted successfully'];
            break;

        default:
            throw new Exception('Invalid setting type');
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
