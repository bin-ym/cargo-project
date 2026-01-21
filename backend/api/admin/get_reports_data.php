<?php
// backend/api/admin/get_reports_data.php
$projectRoot = dirname(__DIR__, 3);
require_once $projectRoot . '/backend/config/database.php';
require_once $projectRoot . '/backend/config/auth_check.php';

header('Content-Type: application/json');

// Ensure only admin can access reports
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $pdo = Database::getConnection();
    $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $endDate = $_GET['end_date'] ?? date('Y-m-d');

    $type = $_GET['type'] ?? 'system';
    $data = [];

    if ($type === 'financial') {
        // Daily Revenue
        $stmt = $pdo->prepare("
            SELECT DATE(updated_at) as date, SUM(price) as daily_total
            FROM cargo_requests
            WHERE payment_status = 'paid' AND DATE(updated_at) BETWEEN ? AND ?
            GROUP BY DATE(updated_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$startDate, $endDate]);
        $data['revenue_over_time'] = $stmt->fetchAll();

        // Transaction List
        $stmt = $pdo->prepare("
            SELECT r.id, c.full_name as customer, r.price, r.status, r.updated_at
            FROM cargo_requests r
            JOIN users c ON r.customer_id = c.id
            WHERE r.payment_status = 'paid' AND DATE(r.updated_at) BETWEEN ? AND ?
            ORDER BY r.updated_at DESC
            LIMIT 50
        ");
        $stmt->execute([$startDate, $endDate]);
        $data['transactions'] = $stmt->fetchAll();
        
    } elseif ($type === 'user') {
        // Top Customers
        $stmt = $pdo->prepare("
            SELECT u.full_name, COUNT(r.id) as total_requests, SUM(r.price) as total_spent
            FROM cargo_requests r
            JOIN users u ON r.customer_id = u.id
            WHERE DATE(r.created_at) BETWEEN ? AND ?
            GROUP BY u.id
            ORDER BY total_spent DESC
            LIMIT 10
        ");
        $stmt->execute([$startDate, $endDate]);
        $data['top_customers'] = $stmt->fetchAll();

        // Top Transporters
        $stmt = $pdo->prepare("
            SELECT u.full_name, COUNT(s.id) as total_deliveries
            FROM shipments s
            JOIN users u ON s.transporter_id = u.id
            WHERE s.status IN ('delivered', 'completed') AND DATE(s.assigned_at) BETWEEN ? AND ?
            GROUP BY u.id
            ORDER BY total_deliveries DESC
            LIMIT 10
        ");
        $stmt->execute([$startDate, $endDate]);
        $data['top_transporters'] = $stmt->fetchAll();

    } elseif ($type === 'vehicle') {
        // Vehicle Usage Stats
        $stmt = $pdo->prepare("
            SELECT vehicle_type, COUNT(*) as count
            FROM cargo_requests
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY vehicle_type
        ");
        $stmt->execute([$startDate, $endDate]);
        $data['vehicle_usage'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    } else { // System (Default)
        // 1. Request Status Breakdown
        $stmt = $pdo->prepare("
            SELECT status, COUNT(*) as count 
            FROM cargo_requests 
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY status
        ");
        $stmt->execute([$startDate, $endDate]);
        $statusData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $allStatuses = ['pending', 'approved', 'rejected', 'completed'];
        $breakdown = [];
        foreach ($allStatuses as $status) {
            $breakdown[$status] = $statusData[$status] ?? 0;
        }
        $data['breakdown'] = $breakdown;

        // 2. Summary Stats
        $stmt = $pdo->prepare("SELECT SUM(price) as total FROM cargo_requests WHERE payment_status = 'paid' AND DATE(created_at) BETWEEN ? AND ?");
        $stmt->execute([$startDate, $endDate]);
        $data['revenue'] = (float)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM customers WHERE DATE(created_at) BETWEEN ? AND ?");
        $stmt->execute([$startDate, $endDate]);
        $data['new_customers'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM transporters WHERE DATE(created_at) BETWEEN ? AND ?");
        $stmt->execute([$startDate, $endDate]);
        $data['new_transporters'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    echo json_encode([
        'success' => true,
        'data' => $data,
        'type' => $type
    ]);

} catch (Exception $e) {
    error_log("Reports Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
