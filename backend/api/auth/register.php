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
$role = $_POST['role'] ?? 'customer';

if ($name === '' || $email === '' || $username === '' || $password === '') {
    echo json_encode(["success" => false, "error" => "All fields are required"]);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode(["success" => false, "error" => "Password must be at least 6 characters"]);
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

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32)); // Generate verification token

    $insert = $pdo->prepare("
        INSERT INTO users (full_name, email, phone, username, password, role, is_verified, verification_token)
        VALUES (?, ?, ?, ?, ?, ?, 0, ?)
    ");
    $insert->execute([$name, $email, $phone, $username, $hashed, $role, $token]);
    $userId = $pdo->lastInsertId();

    // Create Profile based on Role
    if ($role === 'customer') {
        $stmtProfile = $pdo->prepare("INSERT INTO customers (user_id) VALUES (?)");
        $stmtProfile->execute([$userId]);
    } elseif ($role === 'transporter') {
        $stmtProfile = $pdo->prepare("INSERT INTO transporters (user_id, status) VALUES (?, 'pending')");
        $stmtProfile->execute([$userId]);
    }

    // Send Verification Email
    $verifyLink = "http://localhost/cargo-project/backend/api/auth/verify_email.php?token=$token";
    $subject = "Verify Your Email - Cargo Transport System";
    $message = "Hi $name,\n\nPlease click the link below to verify your email address:\n$verifyLink\n\nIf you did not sign up, please ignore this email.";
    $headers = "From: no-reply@cargo-project.com";

    // Note: mail() requires a configured SMTP server (e.g., Mercury/Sendmail in XAMPP)
    // For production, use PHPMailer.
    @mail($email, $subject, $message, $headers);

    echo json_encode(["success" => true, "message" => "Registration successful! Please check your email to verify your account."]);

} catch (Exception $e) {
    error_log("Registration Error: " . $e->getMessage());
    echo json_encode(["success" => false, "error" => "Server error"]);
}
