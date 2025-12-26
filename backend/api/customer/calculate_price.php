<?php
// backend/api/customer/calculate_price.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $distance = floatval($input['distance_km'] ?? 0);
    $weight = floatval($input['weight'] ?? 0);
    $quantity = intval($input['quantity'] ?? 1);
    
    if ($distance <= 0 || $weight <= 0) {
        throw new Exception("Invalid distance or weight");
    }

    $vehicleType = $input['vehicle_type'] ?? 'pickup';

    // Pricing Model
    $baseRate = 150; // Base fee (Transporter Fee + Platform Fee)
    
    // Vehicle Multipliers (Rate per Km * Weight Factor)
    $vehicleRates = [
        'pickup' => 1.0,  // Standard
        'isuzu' => 1.5,   // Medium
        'trailer' => 2.5  // Large
    ];

    $multiplier = $vehicleRates[$vehicleType] ?? 1.0;
    $ratePerKm = 20 * $multiplier;
    $ratePerKg = 5; // Reduced per kg rate as vehicle type covers capacity

    // Formula: (Distance * RatePerKm) + (Weight * RatePerKg * Quantity) + BaseRate
    // Adjusted to: (Distance * Weight * VehicleFactor) might be too aggressive, let's stick to a balanced linear model
    // User asked for: Distance X Weight X Vehicle type
    // Let's interpret "X" as a factor interaction
    
    // New Formula Interpretation:
    // Cost = (Distance * Weight * VehicleFactor * 0.1) + BaseRate
    // 0.1 is a scaling factor to keep prices realistic
    
    $vehicleFactor = $vehicleRates[$vehicleType] ?? 1.0;
    
    // Example: 10km * 50kg * 1.0 * 0.5 = 250 ETB + 150 = 400 ETB
    // Example: 100km * 1000kg * 2.5 * 0.05 = 12500 ETB
    
    $scalingFactor = 0.2; // Adjustable
    $variableCost = ($distance * $weight * $quantity * $vehicleFactor * $scalingFactor);
    
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
