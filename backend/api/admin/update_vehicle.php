<?php
// backend/api/admin/update_vehicle.php

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
    
    if (empty($data['id']) || empty($data['plate_number']) || empty($data['vehicle_type']) || empty($data['status'])) {
        throw new Exception("All fields are required");
    }
    
    $db = Database::getConnection();
    $stmt = $db->prepare("UPDATE vehicles SET plate_number = ?, vehicle_type = ?, status = ? WHERE id = ?");
    $stmt->execute([
        $data['plate_number'],
        $data['vehicle_type'],
        $data['status'],
        $data['id']
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Vehicle updated successfully']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
