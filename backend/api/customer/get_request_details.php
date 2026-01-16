<?php
// backend/api/customer/get_request_details.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../controllers/RequestController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Missing ID']);
    exit();
}

$controller = new RequestController();
$request = $controller->getById($id);

// Check if request exists and belongs to the logged-in user
// Note: RequestController::getById now joins customers and users, 
// so we should check if the joined user_id matches the session
if ($request) {
    // We need to verify ownership. 
    // The getById query joins `customers c` and `users u`. 
    // We can check if the email or phone matches, or better, fetch the user_id from the join.
    // Let's assume we need to fetch the user_id column from the users table in the controller first.
    
    // Actually, let's look at the controller again. It selects r.* and u.*. 
    // u.id would be the user_id. Let's verify if u.id is selected.
    // The query is: SELECT r.*, u.full_name... 
    // It does NOT explicitly select u.id. 
    // However, since we join on c.user_id = u.id, we can rely on that.
    
    // Let's update the controller to select u.id as user_id to be safe, 
    // OR we can fetch the customer ID for the current session user and compare that.
    
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT id FROM customers WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentCustomerId = $stmt->fetchColumn();

    if ($request['customer_id'] == $currentCustomerId) {
        echo json_encode(['success' => true, 'data' => $request]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Unauthorized access to this request']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Request not found']);
}
