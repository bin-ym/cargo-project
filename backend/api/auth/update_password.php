<?php
// backend/api/auth/update_password.php

$projectRoot = dirname(__DIR__, 3);
require_once $projectRoot . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load ENV
$dotenv = Dotenv::createImmutable($projectRoot);
$dotenv->load();

require_once $projectRoot . '/backend/config/session.php';
require_once $projectRoot . '/backend/config/database.php';

header('Content-Type: application/json');

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
    exit();
}

$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode(["success" => false, "error" => "All fields are required"]);
    exit();
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(["success" => false, "error" => "New passwords do not match"]);
    exit();
}

if (strlen($newPassword) < 6) {
    echo json_encode(["success" => false, "error" => "Password must be at least 6 characters"]);
    exit();
}

try {
    $pdo = Database::getConnection();

    // Verify current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($currentPassword, $user['password'])) {
        echo json_encode(["success" => false, "error" => "Incorrect current password"]);
        exit();
    }

    // Update password
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?");
    $update->execute([$hashed, $_SESSION['user_id']]);

    echo json_encode(["success" => true, "message" => "Password updated successfully"]);

} catch (Exception $e) {
    error_log("Password Update Error: " . $e->getMessage());
    echo json_encode(["success" => false, "error" => "Server error"]);
}
