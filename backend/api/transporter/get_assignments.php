<?php
// backend/api/transporter/get_assignments.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../controllers/RequestController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'transporter') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$controller = new RequestController();
$assignments = $controller->getByTransporterId($_SESSION['user_id']);

foreach ($assignments as &$row) {
    $row['eid'] = Security::encryptId($row['id']);
}

echo json_encode(['success' => true, 'data' => $assignments]);
