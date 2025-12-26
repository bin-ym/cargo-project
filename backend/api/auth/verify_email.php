<?php
// backend/api/auth/verify_email.php

require_once __DIR__ . '/../../config/database.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Invalid token.");
}

try {
    $pdo = Database::getConnection();
    
    // Find user with this token
    $stmt = $pdo->prepare("SELECT id FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Verify user
        $update = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $update->execute([$user['id']]);
        
        // Redirect to login with success
        header("Location: /cargo-project/frontend/auth/login.php?verified=true");
        exit();
    } else {
        die("Invalid or expired verification link.");
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
