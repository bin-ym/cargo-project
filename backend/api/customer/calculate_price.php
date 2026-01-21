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

    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        throw new Exception("Invalid JSON input");
    }

    $distance = floatval($input['distance_km'] ?? 0);
    $weight = floatval($input['weight'] ?? 0);
    $quantity = intval($input['quantity'] ?? 1);
    $vehicleType = $input['vehicle_type'] ?? 'pickup';
    $pickupDate = $input['pickup_date'] ?? null;

    if ($distance <= 0 || $weight <= 0 || $quantity <= 0 || !$pickupDate) {
        throw new Exception("Invalid input values");
    }

    /* ===============================
       BASE PRICING
    ================================*/
    $baseRate = 150; // ETB
    $vehicleRates = [
        'pickup'  => 1.0,
        'isuzu'   => 1.5,
        'trailer' => 2.5
    ];

    $vehicleFactor = $vehicleRates[$vehicleType] ?? 1.0;
    $scalingFactor = 0.2;

    $variableCost = $distance * $weight * $quantity * $vehicleFactor * $scalingFactor;
    $price = $baseRate + $variableCost;

    /* ===============================
       DATE-BASED PRICE ADJUSTMENT
    ================================*/
    $today = new DateTime('today');
    $pickup = new DateTime($pickupDate);
    $daysDiff = (int)$today->diff($pickup)->format('%r%a');

    $dateMultiplier = 1.0;

    if ($daysDiff <= 1) {
        $dateMultiplier = 1.20; // +20% urgent
    } elseif ($daysDiff <= 3) {
        $dateMultiplier = 1.10; // +10%
    } elseif ($daysDiff <= 7) {
        $dateMultiplier = 1.00; // normal
    } elseif ($daysDiff <= 14) {
        $dateMultiplier = 0.90; // -10%
    } else {
        $dateMultiplier = 0.80; // -20%
    }

    $finalPrice = $price * $dateMultiplier;

    echo json_encode([
        'success' => true,
        'price' => round($finalPrice, 2),
        'breakdown' => [
            'base_rate' => $baseRate,
            'variable_cost' => round($variableCost, 2),
            'vehicle_factor' => $vehicleFactor,
            'days_until_pickup' => $daysDiff,
            'date_multiplier' => $dateMultiplier
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}