<?php
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
// We can optionally check email too if passed, to be safer with short codes
$email = $data['email'] ?? ''; 

if (empty($token)) {
    echo json_encode(['success' => false, 'error' => 'Token is required']);
    exit;
}

try {
    $pdo = Database::getConnection();
    
    // If email is provided, check both. If not, just token (less secure for short codes but works if unique enough)
    // Given it's 6 digits, collisions are possible. Better to require email.
    
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND verification_token = ? AND is_verified = 0");
        $stmt->execute([$email, $token]);
    } else {
        // Fallback (not recommended for OTP)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE verification_token = ? AND is_verified = 0");
        $stmt->execute([$token]);
    }
    
    $user = $stmt->fetch();

    if ($user) {
        $update = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $update->execute([$user['id']]);
        
        echo json_encode(['success' => true, 'message' => 'Email verified successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid code or email']);
    }

} catch (Exception $e) {
    error_log("Verification Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
