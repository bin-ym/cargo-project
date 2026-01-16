<?php
// backend/api/auth/register.php

$projectRoot = dirname(__DIR__, 3);
require_once $projectRoot . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load ENV
$dotenv = Dotenv::createImmutable($projectRoot);
$dotenv->load();

require_once $projectRoot . '/backend/config/database.php';

header('Content-Type: application/json');

// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'];

// Additional fields
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');

if ($name === '' || $email === '' || $username === '' || $password === '') {
    echo json_encode(["success" => false, "error" => "All fields are required"]);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode(["success" => false, "error" => "Password must be at least 6 characters"]);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "error" => "Invalid email format"]);
    exit();
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ? OR phone = ?");
    $stmt->execute([$username, $email, $phone]);

    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "error" => "Username, email, or phone already exists"]);
        exit();
    }

    // Handle File Upload for Transporter
    $licensePath = null;
    if ($role === 'transporter') {
        if (!isset($_FILES['license_copy']) || $_FILES['license_copy']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(["success" => false, "error" => "License copy is required for transporters"]);
            exit();
        }

        $file = $_FILES['license_copy'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(["success" => false, "error" => "Invalid file type. Only JPG, PNG, and PDF allowed."]);
            exit();
        }

        // Create upload directory if not exists
        $uploadDir = $projectRoot . '/uploads/licenses/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('license_', true) . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            echo json_encode(["success" => false, "error" => "Failed to upload license file"]);
            exit();
        }
        
        // Store relative path
        $licensePath = 'uploads/licenses/' . $filename;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    // Generate 6-digit OTP
    $token = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

    $pdo->beginTransaction();

    $insert = $pdo->prepare("
        INSERT INTO users (full_name, email, phone, username, password, role, is_verified, verification_token)
        VALUES (?, ?, ?, ?, ?, ?, 0, ?)
    ");
    $insert->execute([$name, $email, $phone, $username, $hashed, $role, $token]);
    $userId = $pdo->lastInsertId();

    // Create Profile based on Role
    if ($role === 'customer') {
        $stmtProfile = $pdo->prepare("INSERT INTO customers (user_id, address, city) VALUES (?, ?, ?)");
        $stmtProfile->execute([$userId, $address, $city]);
    } elseif ($role === 'transporter') {
        $stmtProfile = $pdo->prepare("INSERT INTO transporters (user_id, status, license_copy) VALUES (?, 'pending', ?)");
        $stmtProfile->execute([$userId, $licensePath]);
    }

    $pdo->commit();

    // Send Verification Email with OTP
    $subject = "Verify Your Email - Cargo Transport System";
    $message = "
        <h2>Welcome to Cargo Connect, $name!</h2>
        <p>Your verification code is:</p>
        <h1 style='letter-spacing: 5px; color: #2563eb;'>$token</h1>
        <p>Please enter this code to verify your account.</p>
        <p>If you did not sign up, please ignore this email.</p>
    ";

    require_once $projectRoot . '/backend/lib/Mailer.php';
    $mailSent = Mailer::send($email, $subject, $message);
    
    // Log for localhost debugging
    error_log("OTP for $email: $token");

    echo json_encode([
        "success" => true, 
        "message" => "Registration successful! Please check your email for the OTP.",
        "email" => $email,
        "debug_otp" => $token // Remove in production
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Registration Error: " . $e->getMessage());
    echo json_encode(["success" => false, "error" => "Server error: " . $e->getMessage()]);
}