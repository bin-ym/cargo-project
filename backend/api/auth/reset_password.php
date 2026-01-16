<?php
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$token = $data['token'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($token) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Email, OTP, and password are required']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
    exit;
}

try {
    $pdo = Database::getConnection();
    
    // Check email AND token to ensure uniqueness for 6-digit OTP
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$email, $token]);
    $user = $stmt->fetch();

    if ($user) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $update->execute([$hashed, $user['id']]);
        
        echo json_encode(['success' => true, 'message' => 'Password reset successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
    }

} catch (Exception $e) {
    error_log("Reset Password Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
