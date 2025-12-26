<?php
// backend/api/transporter/get_transporter_stats.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'transporter') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $db = Database::getConnection();
    $transporterId = $_SESSION['user_id'];

    // Calculate total trips and completed trips
    $stmt = $db->prepare("
        SELECT 
            COUNT(DISTINCT s.id) as total_trips,
            SUM(CASE WHEN s.status = 'delivered' THEN 1 ELSE 0 END) as completed_trips,
            SUM(CASE WHEN s.status = 'delivered' THEN r.price * 0.8 ELSE 0 END) as total_earnings
        FROM shipments s
        JOIN cargo_requests r ON s.request_id = r.id
        WHERE s.transporter_id = ?
    ");
    $stmt->execute([$transporterId]);
    $tripStats = $stmt->fetch();

    $totalTrips = $tripStats['total_trips'] ?? 0;
    $completedTrips = $tripStats['completed_trips'] ?? 0;
    $totalEarnings = $tripStats['total_earnings'] ?? 0;

    // Calculate average rating from ratings table
    $stmt = $db->prepare("
        SELECT AVG(rating) as avg_rating, COUNT(*) as rating_count
        FROM ratings
        WHERE transporter_id = ?
    ");
    $stmt->execute([$transporterId]);
    $ratingData = $stmt->fetch();
    
    $avgRating = $ratingData['avg_rating'] ? number_format($ratingData['avg_rating'], 1) : '0.0';
    $ratingCount = $ratingData['rating_count'] ?? 0;
    
    // Earnings chart - last 7 days
    $stmt = $db->prepare("
        SELECT 
            DATE(s.updated_at) as date,
            SUM(r.price * 0.8) as earnings
        FROM shipments s
        JOIN cargo_requests r ON s.request_id = r.id
        WHERE s.transporter_id = ? 
        AND s.status = 'delivered'
        AND s.updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(s.updated_at)
        ORDER BY date ASC
    ");
    $stmt->execute([$transporterId]);
    $earningsChartData = $stmt->fetchAll();
    
    // Prepare full 7-day chart data even if some days are missing
    $earningsChart = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $found = false;
        foreach ($earningsChartData as $row) {
            if ($row['date'] === $date) {
                $earningsChart[] = ['date' => $date, 'earnings' => (float)$row['earnings']];
                $found = true;
                break;
            }
        }
        if (!$found) {
            $earningsChart[] = ['date' => $date, 'earnings' => 0];
        }
    }
    
    // Recent deliveries
    $stmt = $db->prepare("
        SELECT 
            r.id,
            u.full_name as customer_name,
            r.pickup_location,
            r.dropoff_location,
            s.status as shipment_status,
            r.pickup_date,
            r.price
        FROM shipments s
        JOIN cargo_requests r ON s.request_id = r.id
        JOIN users u ON r.customer_id = u.id
        WHERE s.transporter_id = ?
        ORDER BY s.updated_at DESC
        LIMIT 5
    ");
    $stmt->execute([$transporterId]);
    $recentDeliveries = [];
    while ($row = $stmt->fetch()) {
        $recentDeliveries[] = [
            'id' => $row['id'],
            'customer' => $row['customer_name'],
            'pickup' => $row['pickup_location'],
            'dropoff' => $row['dropoff_location'],
            'status' => $row['shipment_status'],
            'date' => $row['pickup_date'],
            'earning' => number_format($row['price'] * 0.8, 2)
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'totalTrips' => $totalTrips,
            'completedTrips' => $completedTrips,
            'totalEarnings' => number_format($totalEarnings, 2),
            'rating' => $avgRating,
            'ratingCount' => $ratingCount,
            'recentDeliveries' => $recentDeliveries,
            'earningsChart' => $earningsChart
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
