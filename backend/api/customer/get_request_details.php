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

if ($request && $request['customer_id'] == $_SESSION['user_id']) {
    echo json_encode(['success' => true, 'data' => $request]);
} else {
    echo json_encode(['success' => false, 'error' => 'Request not found or unauthorized']);
}
