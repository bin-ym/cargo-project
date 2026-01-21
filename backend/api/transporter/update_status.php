<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../controllers/RequestController.php';
require_once __DIR__ . '/../../lib/Security.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'transporter') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['request_id']) || empty($data['status'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit();
}

/* ✅ DECRYPT ID */
$requestId = $data['request_id'];
if (!is_numeric($requestId)) {
    $requestId = Security::decryptId($requestId);
}

if (!$requestId) {
    echo json_encode(['success' => false, 'error' => 'Invalid request ID']);
    exit();
}

$allowed = ['in-transit', 'delivered'];
if (!in_array($data['status'], $allowed)) {
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit();
}

$controller = new RequestController();

$result = $controller->updateShipmentStatus($requestId, $data['status']);

if ($result === true) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Update failed: ' . $result]);
}
