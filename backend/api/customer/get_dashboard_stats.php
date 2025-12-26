<?php
// backend/api/customer/get_dashboard_stats.php

require_once __DIR__ . '/../../config/session.php';
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

    $stats = [
        'totalRequests' => count($requests),
        'completedRequests' => 0,
        'pendingRequests' => 0,
        'approvedRequests' => 0,
        'inTransitCount' => 0,
        'recentActivity' => [],
        'pendingPayments' => []
    ];

    foreach ($requests as $req) {
        // Count Stats
        if ($req['status'] === 'pending') {
            $stats['pendingRequests']++;
        } elseif ($req['status'] === 'approved' && $req['shipment_status'] !== 'in-transit' && $req['shipment_status'] !== 'delivered') {
            // Approved but not yet in transit
            $stats['approvedRequests']++;
        }
        
        if ($req['shipment_status'] === 'in-transit') {
            $stats['inTransitCount']++;
        }
        
        if ($req['shipment_status'] === 'delivered' || $req['status'] === 'completed') {
            $stats['completedRequests']++;
        }

        // Add to Pending Payments if applicable
        if ($req['payment_status'] === 'pending' && $req['status'] !== 'cancelled') {
            $stats['pendingPayments'][] = [
                'id' => $req['id'],
                'price' => $req['price'],
                'created_at' => $req['created_at'],
                'pickup_location' => $req['pickup_location'],
                'dropoff_location' => $req['dropoff_location']
            ];
        }

        // Build Activity Log
        // We'll use the request creation and status as activity items
        
        // Add "Created" activity
        $stats['recentActivity'][] = [
            'message' => "Request #CT-" . str_pad($req['id'], 4, '0', STR_PAD_LEFT) . " was created",
            'time' => $req['created_at'],
            'icon' => 'plus-circle'
        ];

        // Add "Status" activity if not pending
        if ($req['status'] === 'approved') {
            $stats['recentActivity'][] = [
                'message' => "Request #CT-" . str_pad($req['id'], 4, '0', STR_PAD_LEFT) . " was approved",
                'time' => $req['updated_at'] ?? $req['created_at'], // Fallback if updated_at is null
                'icon' => 'check-circle'
            ];
        }
        if ($req['shipment_status'] === 'in-transit') {
            $stats['recentActivity'][] = [
                'message' => "Shipment #CT-" . str_pad($req['id'], 4, '0', STR_PAD_LEFT) . " is in transit",
                'time' => $req['updated_at'] ?? $req['created_at'],
                'icon' => 'truck'
            ];
        }
        if ($req['shipment_status'] === 'delivered') {
            $stats['recentActivity'][] = [
                'message' => "Shipment #CT-" . str_pad($req['id'], 4, '0', STR_PAD_LEFT) . " was delivered",
                'time' => $req['updated_at'] ?? $req['created_at'],
                'icon' => 'package'
            ];
        }
    }

    // Sort activity by time desc and take top 5
    usort($stats['recentActivity'], function($a, $b) {
        return strtotime($b['time']) - strtotime($a['time']);
    });
    $stats['recentActivity'] = array_slice($stats['recentActivity'], 0, 5);

    echo json_encode(['success' => true, 'data' => $stats]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
