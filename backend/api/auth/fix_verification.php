<?php
// backend/api/auth/fix_verification.php
require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = Database::getConnection();
    
    // Set is_verified = 1 for ALL users
    $stmt = $pdo->query("UPDATE users SET is_verified = 1");
    
    echo "<h1>Success</h1>";
    echo "<p>All users have been verified. You can now log in.</p>";
    echo "<p>Rows affected: " . $stmt->rowCount() . "</p>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
