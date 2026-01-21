<?php
// backend/api/customer/check_rating.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/RequestController.php';
require_once __DIR__ . '/../../lib/Security.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $requestId = $_GET['request_id'] ?? null;
    if (!$requestId) {
        http_response_code(400);
        throw new Exception("Request ID is required");
    }

    $db = Database::getConnection();

    // Optional: encrypt/decrypt request ID if needed
    $decryptedId = Security::decryptId($requestId);

    // Verify the request belongs to the customer
    $stmtCheck = $db->prepare("SELECT id FROM cargo_requests WHERE id = ?"); // Removed customer check for now since we pass encryption ID to fetch, but ideally we check ownership via customer table linkage if strict ownership needed. But let's fix table name first.
    // Actually, `cargo_requests` has `customer_id`. `$_SESSION['user_id']` is User ID. Need valid check.
    // `SELECT id FROM cargo_requests r JOIN customers c ON r.customer_id = c.id WHERE r.id = ? AND c.user_id = ?`
    $stmtCheck = $db->prepare("SELECT r.id FROM cargo_requests r JOIN customers c ON r.customer_id = c.id WHERE r.id = ? AND c.user_id = ?");
    $stmtCheck->execute([$decryptedId, $_SESSION['user_id']]);
    $requestExists = $stmtCheck->fetch();
    if (!$requestExists) {
        http_response_code(404);
        throw new Exception("Request not found or does not belong to the customer");
    }

    // Check if rating exists
    $stmt = $db->prepare("SELECT rating, comment, created_at FROM ratings WHERE request_id = ? AND customer_id = ?");
    $stmt->execute([$decryptedId, $_SESSION['user_id']]);
    $rating = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'hasRated' => !empty($rating),
        'rating' => $rating ? [
            'rating' => (int)$rating['rating'],
            'comment' => $rating['comment'],
            'created_at' => $rating['created_at']
        ] : null
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}