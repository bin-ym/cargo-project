<?php
// backend/api/admin/reject_request.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../controllers/RequestController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['request_id']) || !isset($data['reason'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit();
}

$controller = new RequestController();
if ($controller->updateStatus($data['request_id'], 'rejected', $data['reason'])) {
    echo json_encode(['success' => true, 'message' => 'Request rejected successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to reject request']);
}
