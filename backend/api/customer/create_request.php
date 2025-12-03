<?php
// backend/api/customer/create_request.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit();
}

$pickup_location = $data['pickup_location'] ?? '';
$dropoff_location = $data['dropoff_location'] ?? '';
$pickup_date = $data['pickup_date'] ?? '';
$items = $data['items'] ?? []; // Array of items

// Basic Validation
if (empty($pickup_location) || empty($dropoff_location) || empty($pickup_date)) {
    echo json_encode(['success' => false, 'error' => 'Please fill in all required fields']);
    exit();
}

// Validate Date (Must be today or future)
$today = date('Y-m-d');
if ($pickup_date < $today) {
    echo json_encode(['success' => false, 'error' => 'Pickup date cannot be in the past']);
    exit();
}

if (empty($items) || !is_array($items)) {
    echo json_encode(['success' => false, 'error' => 'At least one item is required']);
    exit();
}

try {
    $pdo = Database::getConnection();
    $pdo->beginTransaction();

    // 1. Insert Request
    $stmt = $pdo->prepare("
        INSERT INTO cargo_requests (customer_id, pickup_location, dropoff_location, pickup_date, status)
        VALUES (?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $pickup_location,
        $dropoff_location,
        $pickup_date
    ]);
    $request_id = $pdo->lastInsertId();

    // 2. Insert Items
    $stmtItem = $pdo->prepare("
        INSERT INTO cargo_items (request_id, item_name, quantity, weight, category, description)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($items as $item) {
        $stmtItem->execute([
            $request_id,
            $item['item_name'],
            $item['quantity'] ?? 1,
            $item['weight'] ?? '',
            $item['category'] ?? 'Other',
            $item['description'] ?? ''
        ]);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Request submitted successfully']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Create Request Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
