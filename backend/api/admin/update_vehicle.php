<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (
        empty($data['id']) ||
        empty($data['plate_number']) ||
        empty($data['vehicle_type'])
    ) {
        throw new Exception('Required fields are missing');
    }

    $allowedStatus = ['available', 'in-use', 'maintenance'];
    $status = $data['status'] ?? 'available';

    if (!in_array($status, $allowedStatus)) {
        throw new Exception('Invalid vehicle status');
    }

    $db = Database::getConnection();

    $stmt = $db->prepare("
        UPDATE vehicles
        SET plate_number = ?, vehicle_type = ?, status = ?
        WHERE id = ?
    ");

    $stmt->execute([
        trim($data['plate_number']),
        $data['vehicle_type'],
        $status,
        $data['id']
    ]);

    // if ($stmt->rowCount() === 0) {
    //     // It's okay if no rows changed (same data)
    // }

    echo json_encode([
        'success' => true,
        'message' => 'Vehicle updated successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
