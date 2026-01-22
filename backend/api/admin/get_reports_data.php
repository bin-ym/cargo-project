<?php
// backend/api/admin/get_reports_data.php

// --------------------------------------------------
// Output buffering to prevent accidental output
// --------------------------------------------------
ob_start();

// --------------------------------------------------
// Bootstrap
// --------------------------------------------------
$projectRoot = dirname(__DIR__, 3);

require_once $projectRoot . '/backend/config/database.php';

// Require admin role BEFORE auth_check
$required_role = 'admin';
require_once $projectRoot . '/backend/config/auth_check.php';

// --------------------------------------------------
// Headers
// --------------------------------------------------
header('Content-Type: application/json; charset=utf-8');

// --------------------------------------------------
// Logic
// --------------------------------------------------
try {
    $pdo = Database::getConnection();

    $type = $_GET['type'] ?? 'system';
    $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $endDate = $_GET['end_date'] ?? date('Y-m-d');

    $data = [];

    // --------------------------------------------------
    // FINANCIAL REPORT
    // --------------------------------------------------
    if ($type === 'financial') {

        // Daily Revenue
        $stmt = $pdo->prepare("
            SELECT 
                DATE(updated_at) AS date, 
                SUM(price) AS daily_total
            FROM cargo_requests
            WHERE payment_status = 'paid'
              AND DATE(updated_at) BETWEEN ? AND ?
            GROUP BY DATE(updated_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$startDate, $endDate]);
        $data['revenue_over_time'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Transactions (latest 50)
        $stmt = $pdo->prepare("
            SELECT 
                r.id,
                u.full_name AS customer,
                r.price,
                r.status,
                r.updated_at
            FROM cargo_requests r
            JOIN customers c ON r.customer_id = c.id
            JOIN users u ON c.user_id = u.id
            WHERE r.payment_status = 'paid'
              AND DATE(r.updated_at) BETWEEN ? AND ?
            ORDER BY r.updated_at DESC
            LIMIT 50
        ");
        $stmt->execute([$startDate, $endDate]);
        $data['transactions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    // --------------------------------------------------
    // USER REPORT
    // --------------------------------------------------
    elseif ($type === 'user') {

        // Top Customers
        $stmt = $pdo->prepare("
            SELECT 
                u.full_name,
                COUNT(r.id) AS total_requests,
                COALESCE(SUM(r.price), 0) AS total_spent
            FROM cargo_requests r
            JOIN customers c ON r.customer_id = c.id
            JOIN users u ON c.user_id = u.id
            WHERE DATE(r.created_at) BETWEEN ? AND ?
            GROUP BY u.id
            ORDER BY total_spent DESC
            LIMIT 10
        ");
        $stmt->execute([$startDate, $endDate]);
        $data['top_customers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Top Transporters
        $stmt = $pdo->prepare("
            SELECT 
                u.full_name,
                COUNT(s.id) AS total_deliveries
            FROM shipments s
            JOIN users u ON s.transporter_id = u.id
            WHERE s.status IN ('delivered', 'completed')
              AND DATE(s.assigned_at) BETWEEN ? AND ?
            GROUP BY u.id
            ORDER BY total_deliveries DESC
            LIMIT 10
        ");
        $stmt->execute([$startDate, $endDate]);
        $data['top_transporters'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --------------------------------------------------
    // VEHICLE REPORT
    // --------------------------------------------------
    elseif ($type === 'vehicle') {

        $stmt = $pdo->prepare("
            SELECT 
                vehicle_type, 
                COUNT(*) AS count
            FROM cargo_requests
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY vehicle_type
        ");
        $stmt->execute([$startDate, $endDate]);

        $data['vehicle_usage'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // --------------------------------------------------
    // SYSTEM REPORT (DEFAULT)
    // --------------------------------------------------
    else {

        // Request Status Breakdown
        $stmt = $pdo->prepare("
            SELECT status, COUNT(*) AS count
            FROM cargo_requests
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY status
        ");
        $stmt->execute([$startDate, $endDate]);
        $statusData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $statuses = ['pending', 'approved', 'rejected', 'completed'];
        $breakdown = [];
        foreach ($statuses as $status) {
            $breakdown[$status] = $statusData[$status] ?? 0;
        }
        $data['breakdown'] = $breakdown;

        // Revenue
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(price), 0) AS total
            FROM cargo_requests
            WHERE payment_status = 'paid'
              AND DATE(created_at) BETWEEN ? AND ?
        ");
        $stmt->execute([$startDate, $endDate]);
        $data['revenue'] = (float) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // New Customers
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS count
            FROM customers
            WHERE DATE(created_at) BETWEEN ? AND ?
        ");
        $stmt->execute([$startDate, $endDate]);
        $data['new_customers'] = (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // New Transporters
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS count
            FROM transporters
            WHERE DATE(created_at) BETWEEN ? AND ?
        ");
        $stmt->execute([$startDate, $endDate]);
        $data['new_transporters'] = (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    // --------------------------------------------------
    // SUCCESS RESPONSE
    // --------------------------------------------------
    ob_clean();
    echo json_encode([
        'success' => true,
        'type'    => $type,
        'data'    => $data
    ]);
    exit;

} catch (Throwable $e) {

    // --------------------------------------------------
    // ERROR RESPONSE
    // --------------------------------------------------
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
    exit;
}