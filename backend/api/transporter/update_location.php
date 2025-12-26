<?php
// backend/api/transporter/update_location.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'transporter') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$requestId = $data['request_id'] ?? null;
$lat = $data['lat'] ?? null;
$lng = $data['lng'] ?? null;

if (!$requestId || !$lat || !$lng) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit();
}

try {
    $db = Database::getConnection();
    
    // Verify transporter owns this shipment
    $stmt = $db->prepare("SELECT id FROM shipments WHERE request_id = ? AND transporter_id = ?");
    $stmt->execute([$requestId, $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Shipment not found or unauthorized']);
        exit();
    }

    // Update Location
    $update = $db->prepare("UPDATE shipments SET current_lat = ?, current_lng = ?, last_updated = NOW() WHERE request_id = ?");
    $update->execute([$lat, $lng, $requestId]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
