<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../lib/Mailer.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');

if (empty($email)) {
    echo json_encode(['success' => false, 'error' => 'Email is required']);
    exit;
}

try {
    $pdo = Database::getConnection();
    
    $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate 6-digit OTP
        $token = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Use MySQL's NOW() to avoid timezone mismatches between PHP and DB
        $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?");
        $update->execute([$token, $user['id']]);

        $subject = "Reset Your Password - Cargo Connect";
        $message = "
            <h2>Password Reset Request</h2>
            <p>Hi {$user['full_name']},</p>
            <p>Your password reset code is:</p>
            <h1 style='letter-spacing: 5px; color: #2563eb;'>$token</h1>
            <p>This code expires in 1 hour.</p>
            <p>If you did not request this, please ignore this email.</p>
        ";

        Mailer::send($email, $subject, $message);
        
        // Log for localhost
        error_log("RESET OTP for $email: $token");

        echo json_encode(['success' => true, 'message' => 'An OTP has been sent to your email.']);
    } else {
        echo json_encode(['success' => false, 'error' => "Account didn't exist"]);
    }

} catch (Exception $e) {
    error_log("Forgot Password Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
