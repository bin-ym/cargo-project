<?php
// backend/api/admin/get_dashboard_stats.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../controllers/RequestController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $controller = new RequestController();
    $requests = $controller->getAll();

    $stats = [
        'totalRequests' => count($requests),
        'approvedRequests' => 0,
        'pendingRequests' => 0,
        'revenue' => 0,
        'recentActivity' => []
    ];

    foreach ($requests as $req) {
        // Count Stats
        if ($req['status'] === 'approved' || $req['status'] === 'completed') {
            $stats['approvedRequests']++;
        } elseif ($req['status'] === 'pending') {
            $stats['pendingRequests']++;
        }

        // Calculate Revenue (Estimated $150 per request)
        // In a real app, this would be a sum of `price` column
        $stats['revenue'] += 150;

        // Build Activity Log
        $stats['recentActivity'][] = [
            'message' => "New request #CT-" . str_pad($req['id'], 4, '0', STR_PAD_LEFT) . " from " . $req['customer_name'],
            'time' => $req['created_at'],
            'icon' => 'user-plus'
        ];

        if ($req['status'] === 'approved') {
            $stats['recentActivity'][] = [
                'message' => "Request #CT-" . str_pad($req['id'], 4, '0', STR_PAD_LEFT) . " approved",
                'time' => $req['updated_at'] ?? $req['created_at'],
                'icon' => 'check-circle'
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
