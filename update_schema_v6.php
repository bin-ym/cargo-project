<?php
require_once __DIR__ . '/backend/config/database.php';

try {
    $pdo = Database::getConnection();
    
    // Check if column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM shipments LIKE 'picked_up_at'");
    $stmt->execute();
    if ($stmt->fetch()) {
        echo "Column 'picked_up_at' already exists.";
    } else {
        $pdo->exec("ALTER TABLE shipments ADD COLUMN picked_up_at DATETIME NULL AFTER assigned_at");
        echo "Column 'picked_up_at' added successfully.";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
