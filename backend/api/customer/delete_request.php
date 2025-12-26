<?php
// backend/api/customer/delete_request.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['request_id'])) {
        throw new Exception("Request ID required");
    }
    
    $requestId = $data['request_id'];
    $db = Database::getConnection();
    
    // Verify ownership
    $stmt = $db->prepare("SELECT customer_id, status FROM cargo_requests WHERE id = ?");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch();
    
    if (!$request) {
        throw new Exception("Request not found");
    }
    
    if ($request['customer_id'] != $_SESSION['user_id']) {
        throw new Exception("Unauthorized");
    }
    
    // Only allow deletion of pending requests
    if ($request['status'] !== 'pending') {
        throw new Exception("Can only delete pending requests");
    }
    
    $db->beginTransaction();
    
    // Delete cargo items
    $stmt = $db->prepare("DELETE FROM cargo_items WHERE request_id = ?");
    $stmt->execute([$requestId]);
    
    // Delete request
    $stmt = $db->prepare("DELETE FROM cargo_requests WHERE id = ?");
    $stmt->execute([$requestId]);
    
    $db->commit();
    
    echo json_encode(['success' => true, 'message' => 'Request deleted successfully']);
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
