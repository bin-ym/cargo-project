<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// Authorization
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
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

// Collect inputs
$username  = trim($data['username'] ?? '');
$full_name = trim($data['name'] ?? '');
$phone     = trim($data['phone'] ?? '');
$address   = trim($data['address'] ?? '');
$city      = trim($data['city'] ?? '');
$password  = $data['password'] ?? '';

// Validate required fields
if ($username === '' || $full_name === '' || $phone === '' || $address === '' || $city === '') {
    echo json_encode(['success' => false, 'error' => 'Required fields are missing']);
    exit;
}

// Regex Validation
if (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
    echo json_encode(['success' => false, 'error' => 'Full Name must contain only letters']);
    exit;
}
if (!preg_match("/^[a-zA-Z\s]+$/", $username)) {
    echo json_encode(['success' => false, 'error' => 'Username must contain only letters']);
    exit;
}
if (!preg_match("/^(09\d{8}|\+251\d{9})$/", $phone)) {
    echo json_encode(['success' => false, 'error' => 'Invalid phone format (must start with 09 or +251)']);
    exit;
}

$db = Database::getConnection();

try {
    $db->beginTransaction();

    // Check if username is already taken by someone else
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$username, $user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Username already taken']);
        exit;
    }

    // Update users table
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare(
            "UPDATE users 
             SET username = ?, full_name = ?, phone = ?, password = ? 
             WHERE id = ?"
        );
        $stmt->execute([$username, $full_name, $phone, $hashed, $user_id]);
    } else {
        $stmt = $db->prepare(
            "UPDATE users 
             SET username = ?, full_name = ?, phone = ? 
             WHERE id = ?"
        );
        $stmt->execute([$username, $full_name, $phone, $user_id]);
    }

    // Update customers table
    $stmt = $db->prepare(
        "UPDATE customers 
         SET address = ?, city = ? 
         WHERE user_id = ?"
    );
    $stmt->execute([$address, $city, $user_id]);

    $db->commit();

    // Update session immediately
    $_SESSION['full_name'] = $full_name;
    $_SESSION['username']  = $username;

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);

} catch (Exception $e) {
    $db->rollBack();
    error_log("Profile update error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Update failed'
    ]);
}