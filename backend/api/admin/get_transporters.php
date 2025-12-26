<?php
// backend/api/admin/get_transporters.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $pdo = Database::getConnection();
    // Fetch users who are transporters AND have an approved profile
    $sql = "SELECT u.id, u.full_name, t.vehicle_type, t.plate_number 
            FROM users u
            JOIN transporters t ON u.id = t.user_id
            WHERE u.role = 'transporter' AND t.status = 'approved'";
    
    $stmt = $pdo->query($sql);
    $transporters = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $transporters]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
