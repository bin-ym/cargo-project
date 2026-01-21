<?php
// backend/api/customer/rate_transporter.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

require_once __DIR__ . '/../../lib/Security.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['request_id']) || !isset($data['rating'])) {
        throw new Exception("Missing required parameters");
    }
    
    $requestId = $data['request_id'];
    if ($requestId && !is_numeric($requestId)) {
        $requestId = Security::decryptId($requestId);
    }
    $rating = (int)$data['rating'];
    $comment = $data['comment'] ?? '';
    
    // Validate rating
    if ($rating < 1 || $rating > 5) {
        throw new Exception("Rating must be between 1 and 5");
    }
    
    $db = Database::getConnection();
    
    // Get request and shipment details
    $stmt = $db->prepare("
        SELECT r.customer_id, s.transporter_id, s.status, c.user_id as owner_user_id
        FROM cargo_requests r
        JOIN customers c ON r.customer_id = c.id
        JOIN shipments s ON r.id = s.request_id
        WHERE r.id = ?
    ");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch();
    
    if (!$request) {
        throw new Exception("Request not found");
    }
    
    // Verify customer owns this request
    if ($request['owner_user_id'] != $_SESSION['user_id']) {
        throw new Exception("Unauthorized");
    }
    
    // Verify shipment is delivered or completed
    if ($request['status'] !== 'delivered' && $request['status'] !== 'completed') {
        throw new Exception("Can only rate completed deliveries");
    }
    
    // Check if already rated
    $check = $db->prepare("SELECT id FROM ratings WHERE request_id = ? AND customer_id = ?");
    $check->execute([$requestId, $_SESSION['user_id']]);
    if ($check->fetch()) {
        throw new Exception("You have already rated this delivery");
    }
    
    // Insert rating
    $stmt = $db->prepare("
        INSERT INTO ratings (request_id, customer_id, transporter_id, rating, comment)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$requestId, $_SESSION['user_id'], $request['transporter_id'], $rating, $comment]);
    
    echo json_encode(['success' => true, 'message' => 'Rating submitted successfully']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
