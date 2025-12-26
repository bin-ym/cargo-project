<?php
// backend/api/admin/assign_transporter.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['request_id']) || !isset($data['transporter_id']) || !isset($data['vehicle_id'])) {
        throw new Exception("Missing required parameters");
    }
    
    $requestId = $data['request_id'];
    $transporterId = $data['transporter_id'];
    $vehicleId = $data['vehicle_id'];
    
    $db = Database::getConnection();
    $db->beginTransaction();
    
    // 1. Update Request Status
    $stmt = $db->prepare("UPDATE cargo_requests SET status = 'approved' WHERE id = ?");
    $stmt->execute([$requestId]);
    
    // 2. Create or Update Shipment with vehicle_id
    $check = $db->prepare("SELECT id FROM shipments WHERE request_id = ?");
    $check->execute([$requestId]);
    $exists = $check->fetch();
    
    if ($exists) {
        $stmtShip = $db->prepare("UPDATE shipments SET transporter_id = ?, vehicle_id = ?, status = 'assigned', assigned_at = NOW() WHERE request_id = ?");
        $stmtShip->execute([$transporterId, $vehicleId, $requestId]);
    } else {
        $stmtShip = $db->prepare("INSERT INTO shipments (request_id, transporter_id, vehicle_id, status) VALUES (?, ?, ?, 'assigned')");
        $stmtShip->execute([$requestId, $transporterId, $vehicleId]);
    }
    
    // 3. Update vehicle status to in-use
    $stmt = $db->prepare("UPDATE vehicles SET status = 'in-use' WHERE id = ?");
    $stmt->execute([$vehicleId]);
    
    $db->commit();
    
    echo json_encode(['success' => true, 'message' => 'Transporter and vehicle assigned successfully']);
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
