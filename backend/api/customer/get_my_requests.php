<?php
// backend/api/customer/get_my_requests.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/RequestController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $controller = new RequestController();
    $requests = $controller->getByCustomerId($_SESSION['user_id']);
    
    // Calculate counts
    $counts = [
        'total' => count($requests),
        'pending' => 0,
        'approved' => 0,
        'inTransit' => 0,
        'completed' => 0
    ];
    
    foreach ($requests as &$req) {
        $req['eid'] = Security::encryptId($req['id']);
        
        // Count Logic
        if ($req['status'] === 'pending') {
            $counts['pending']++;
        } elseif ($req['status'] === 'approved' && $req['shipment_status'] !== 'in-transit' && $req['shipment_status'] !== 'delivered' && $req['shipment_status'] !== 'completed') {
            $counts['approved']++;
        }
        
        if ($req['shipment_status'] === 'in-transit') {
            $counts['inTransit']++;
        }
        
        if ($req['shipment_status'] === 'delivered' || $req['shipment_status'] === 'completed' || $req['status'] === 'completed') {
            $counts['completed']++;
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'requests' => $requests,
            'counts' => $counts
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
