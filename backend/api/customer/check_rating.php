<?php
// backend/api/customer/check_rating.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $requestId = $_GET['request_id'] ?? null;
    
    if (!$requestId) {
        throw new Exception("Request ID required");
    }
    
    $db = Database::getConnection();
    
    // Check if rating exists
    $stmt = $db->prepare("SELECT rating, comment, created_at FROM ratings WHERE request_id = ? AND customer_id = ?");
    $stmt->execute([$requestId, $_SESSION['user_id']]);
    $rating = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'hasRated' => $rating ? true : false,
        'rating' => $rating
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
