<?php
// backend/api/transporter/update_status.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../controllers/RequestController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'transporter') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['request_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit();
}

$controller = new RequestController();
if ($controller->updateShipmentStatus($data['request_id'], $data['status'])) {
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update status']);
}
