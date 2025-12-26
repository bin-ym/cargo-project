<?php
// backend/api/admin/delete_vehicle.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        throw new Exception("Vehicle ID is required");
    }
    
    $db = Database::getConnection();
    $stmt = $db->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true, 'message' => 'Vehicle deleted successfully']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
