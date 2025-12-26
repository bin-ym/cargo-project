<?php
// backend/api/admin/add_vehicle.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['plate_number']) || empty($data['vehicle_type'])) {
        throw new Exception("Plate number and vehicle type are required");
    }
    
    $db = Database::getConnection();
    $stmt = $db->prepare("INSERT INTO vehicles (plate_number, vehicle_type) VALUES (?, ?)");
    $stmt->execute([$data['plate_number'], $data['vehicle_type']]);
    
    echo json_encode(['success' => true, 'message' => 'Vehicle added successfully', 'id' => $db->lastInsertId()]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
