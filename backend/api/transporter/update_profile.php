<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'transporter') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$name = $data['full_name'] ?? ''; // Frontend sends 'full_name'
$phone = $data['phone'] ?? '';
$password = $data['password'] ?? '';

if (empty($name) || empty($phone)) {
    echo json_encode(['success' => false, 'error' => 'Name and phone are required']);
    exit;
}

try {
    $db = Database::getConnection();
    $db->beginTransaction();

    // Update Users table
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET full_name = ?, phone = ?, password = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $hashed, $user_id]);
    } else {
        $stmt = $db->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $user_id]);
    }

    $db->commit();
    
    // Update session name if changed
    $_SESSION['full_name'] = $name;

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'error' => 'Update failed: ' . $e->getMessage()]);
}
