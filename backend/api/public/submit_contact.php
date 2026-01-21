<?php
// backend/api/public/submit_contact.php
$projectRoot = dirname(__DIR__, 3);
require_once $projectRoot . '/backend/config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$name = trim(($input['first_name'] ?? '') . ' ' . ($input['last_name'] ?? ''));
$email = trim($input['email'] ?? '');
$message = trim($input['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email format']);
    exit;
}

try {
    $pdo = Database::getConnection();
    
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);
    
    // Send email notification
    require_once $projectRoot . '/backend/lib/Mailer.php';
    
    $to = $_ENV['SMTP_USER']; // Send to the configured admin email
    $subject = "New Contact Message from $name";
    $body = "
        <h2>New Contact Message</h2>
        <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
        <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
    ";
    
    Mailer::send($to, $subject, $body);

    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} catch (Exception $e) {
    error_log("Contact Form Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
