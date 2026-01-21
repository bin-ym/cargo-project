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
    // $weight & $quantity are now in $items, but kept for fallback logic below
    $vehicleType = $input['vehicle_type'] ?? 'pickup';
    $pickupDate = $input['pickup_date'] ?? null;

    if ($distance <= 0 || !$pickupDate) {
        throw new Exception("Invalid input values");
    }

    /* ===============================
       ITEMS & BASE CALCULATIONS
    ================================*/
    $items = $input['items'] ?? [];
    
    // Fallback for single item (backward compatibility if needed, though frontend sends items now)
    if (empty($items) && isset($input['weight'])) {
        $items = [[
            'weight' => $input['weight'],
            'quantity' => $input['quantity'] ?? 1
        ]];
    }

    $baseRate = 150; // ETB
    $vehicleRates = [
        'pickup'  => 1.0,
        'isuzu'   => 1.5,
        'trailer' => 2.5
    ];
    $vehicleFactor = $vehicleRates[$vehicleType] ?? 1.0;
    $scalingFactor = 0.2;

    $totalItemCost = 0;
    foreach ($items as $item) {
        $w = floatval($item['weight'] ?? 0);
        $q = intval($item['quantity'] ?? 1);
        if ($w > 0 && $q > 0) {
            $totalItemCost += ($distance * $w * $q * $vehicleFactor * $scalingFactor);
        }
    }

    $price = $baseRate + $totalItemCost;

    /* ===============================
       DATE-BASED PRICE ADJUSTMENT
    ================================*/
    $today = new DateTime('today');
    $pickup = new DateTime($pickupDate);
    $daysDiff = (int)$today->diff($pickup)->format('%r%a');

    // "High impact" if <= 3 days
    if ($daysDiff <= 3) {
        $dateMultiplier = 1.50; // +50%
    } else {
        $dateMultiplier = 1.00; // Normal
    }

    $finalPrice = $price * $dateMultiplier;

    echo json_encode([
        'success' => true,
        'price' => round($finalPrice, 2),
        'breakdown' => [
            'base_rate' => $baseRate,
            'variable_cost' => round($totalItemCost, 2),
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