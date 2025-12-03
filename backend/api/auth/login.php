<?php
// backend/api/auth/login.php

$projectRoot = dirname(__DIR__, 3);
require_once $projectRoot . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load ENV
$dotenv = Dotenv::createImmutable($projectRoot);
$dotenv->load();

// Core includes
require_once $projectRoot . '/backend/config/session.php';
require_once $projectRoot . '/backend/config/database.php';

header('Content-Type: application/json; charset=utf-8');

// CORS (adjust if needed)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(["success" => false, "error" => "Missing credentials"]);
    exit();
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        SELECT id, username, email, password, role, full_name, must_change_password 
        FROM users 
        WHERE username = ? OR email = ?
    ");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(["success" => false, "error" => "Invalid username or password"]);
        exit();
    }

    // Login user (session.php)
    login_user($user);

    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "must_change_password" => (bool)$user['must_change_password'],
        "user" => [
            "id" => $user['id'],
            "username" => $user['username'],
            "email" => $user['email'],
            "role" => $user['role'],
            "full_name" => $user['full_name']
        ]
    ]);

} catch (Exception $e) {
    error_log("Login Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Server error"]);
}
