<?php
// backend/api/admin/get_vehicles.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $db = Database::getConnection();
    
    // Optional filter by vehicle_type
    $vehicleType = $_GET['vehicle_type'] ?? null;
    
    if ($vehicleType) {
        $stmt = $db->prepare("SELECT * FROM vehicles WHERE vehicle_type = ? ORDER BY created_at DESC");
        $stmt->execute([$vehicleType]);
    } else {
        $stmt = $db->query("SELECT * FROM vehicles ORDER BY created_at DESC");
    }
    
    $vehicles = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $vehicles]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
