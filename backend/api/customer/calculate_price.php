<?php
// backend/api/customer/calculate_price.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }

    // Decode input safely
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        throw new Exception("Invalid input JSON");
    }

    $distance = floatval($input['distance_km'] ?? 0);
    $weight = floatval($input['weight'] ?? 0);
    $quantity = intval($input['quantity'] ?? 1);
    $vehicleType = $input['vehicle_type'] ?? 'pickup';

    if ($distance <= 0 || $weight <= 0 || $quantity <= 0) {
        throw new Exception("Distance, weight, and quantity must be positive numbers");
    }

    // Pricing Model
    $baseRate = 150; // Base fee
    $vehicleRates = [
        'pickup' => 1.0,
        'isuzu' => 1.5,
        'trailer' => 2.5
    ];
    $vehicleFactor = $vehicleRates[$vehicleType] ?? 1.0;
    $scalingFactor = 0.2;

    $variableCost = $distance * $weight * $quantity * $vehicleFactor * $scalingFactor;
    $price = $baseRate + $variableCost;

    echo json_encode([
        'success' => true,
        'price' => round($price, 2),
        'breakdown' => [
            'base_fare' => $baseRate,
            'variable_cost' => round($variableCost, 2),
            'vehicle_factor' => $vehicleFactor
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}