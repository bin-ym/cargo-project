<?php
// backend/api/admin/get_earnings.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $db = Database::getConnection();
    
    // Total earnings (gross revenue from customers)
    $stmt = $db->query("
        SELECT 
            SUM(price) as total_revenue,
            COUNT(*) as total_requests
        FROM cargo_requests 
        WHERE payment_status = 'paid'
    ");
    $gross = $stmt->fetch();
    
    // Net profit (after 80% transporter commission)
    $netProfit = ($gross['total_revenue'] ?? 0) * 0.2;
    
    // Earnings by month (last 6 months)
    $stmt = $db->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            SUM(price) as revenue,
            COUNT(*) as count
        FROM cargo_requests 
        WHERE payment_status = 'paid'
        AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month DESC
    ");
    $byMonth = $stmt->fetchAll();
    
    // Recent transactions
    $stmt = $db->query("
        SELECT 
            r.id,
            r.price,
            r.created_at,
            u.full_name as customer_name
        FROM cargo_requests r
        JOIN users u ON r.customer_id = u.id
        WHERE r.payment_status = 'paid'
        ORDER BY r.created_at DESC
        LIMIT 10
    ");
    $recent = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'totalRevenue' => number_format($gross['total_revenue'] ?? 0, 2),
            'netProfit' => number_format($netProfit, 2),
            'totalRequests' => $gross['total_requests'] ?? 0,
            'byMonth' => $byMonth,
            'recentTransactions' => $recent
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
