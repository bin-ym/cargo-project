<?php
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
    // Fetch active transporters (users with role 'transporter' and status 'active')
    // Assuming 'users' table has role and 'transporters' table has status. 
    // Or if 'transporters' table is just profile data.
    // Let's check schema assumptions: users (id, role, full_name), transporters (user_id, status)
    
    $sql = "SELECT u.id, u.full_name, u.email, t.status 
            FROM users u 
            JOIN transporters t ON u.id = t.user_id 
            WHERE u.role = 'transporter' AND t.status = 'approved'";
            
    $stmt = $db->query($sql);
    $transporters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $transporters]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
