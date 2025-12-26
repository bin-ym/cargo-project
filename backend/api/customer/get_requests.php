<?php
// backend/api/customer/get_requests.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../controllers/RequestController.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$controller = new RequestController();
$requests = $controller->getByCustomerId($_SESSION['user_id']);

echo json_encode(['success' => true, 'data' => $requests]);
