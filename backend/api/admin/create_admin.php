<?php
// backend/api/admin/create_admin.php
// This script should only be used for initial admin account creation

$projectRoot = dirname(__DIR__, 3);
require_once $projectRoot . '/backend/config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$fullName = trim($_POST['full_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

// Validate inputs
if (empty($fullName) || empty($username) || empty($email) || empty($phone) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email format']);
    exit;
}

try {
    $pdo = Database::getConnection();
    
    // Check if admin already exists
    $checkAdmin = $pdo->prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    $checkAdmin->execute();
    
    if ($checkAdmin->fetch()) {
        echo json_encode(['success' => false, 'error' => 'An admin account already exists. For security reasons, only one admin can be created through this interface.']);
        exit;
    }
    
    // Check if username or email already exists
    $checkUser = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $checkUser->execute([$username, $email]);
    
    if ($checkUser->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Username or email already exists']);
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert admin user
    $insert = $pdo->prepare("
        INSERT INTO users (full_name, username, email, phone, password, role, is_verified) 
        VALUES (?, ?, ?, ?, ?, 'admin', 1)
    ");
    
    $insert->execute([$fullName, $username, $email, $phone, $hashedPassword]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Admin account created successfully! Redirecting to login...'
    ]);
    
} catch (Exception $e) {
    error_log("Create Admin Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
