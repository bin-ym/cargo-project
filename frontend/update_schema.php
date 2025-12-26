<?php
// frontend/update_schema.php
require_once __DIR__ . '/../backend/config/database.php';

echo "<h1>Update Schema</h1>";

try {
    $pdo = Database::getConnection();
    
    // 1. Add columns to cargo_requests and users
    $columns = [
        "ALTER TABLE cargo_requests ADD COLUMN pickup_lat DECIMAL(10, 8) NULL AFTER pickup_location",
        "ALTER TABLE cargo_requests ADD COLUMN pickup_lng DECIMAL(11, 8) NULL AFTER pickup_lat",
        "ALTER TABLE cargo_requests ADD COLUMN dropoff_lat DECIMAL(10, 8) NULL AFTER dropoff_location",
        "ALTER TABLE cargo_requests ADD COLUMN dropoff_lng DECIMAL(11, 8) NULL AFTER dropoff_lat",
        "ALTER TABLE cargo_requests ADD COLUMN distance_km DECIMAL(10, 2) NULL AFTER dropoff_lng",
        "ALTER TABLE cargo_requests ADD COLUMN price DECIMAL(10, 2) NULL AFTER distance_km",
        "ALTER TABLE cargo_requests ADD COLUMN vehicle_type VARCHAR(50) NULL AFTER price",
        "ALTER TABLE cargo_requests ADD COLUMN tx_ref VARCHAR(100) NULL AFTER vehicle_type",
        "ALTER TABLE cargo_requests ADD COLUMN payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending' AFTER status",
        "ALTER TABLE cargo_requests ADD COLUMN rejection_reason TEXT AFTER status",
        "ALTER TABLE users ADD COLUMN is_verified BOOLEAN DEFAULT FALSE",
        "ALTER TABLE users ADD COLUMN verification_token VARCHAR(100) NULL"
    ];

    foreach ($columns as $sql) {
        try {
            $pdo->exec($sql);
            echo "Executed: $sql<br>";
        } catch (Exception $e) {
            // Ignore if column exists
            echo "Skipped: $sql. Error: " . $e->getMessage() . "<br>";
        }
    }

    // 2. Add columns to shipments
    $shipmentCols = [
        "ADD COLUMN current_lat DECIMAL(10, 8) NULL AFTER status",
        "ADD COLUMN current_lng DECIMAL(11, 8) NULL AFTER current_lat",
        "ADD COLUMN last_updated TIMESTAMP NULL AFTER current_lng"
    ];

    foreach ($shipmentCols as $col) {
        try {
            $pdo->exec("ALTER TABLE shipments $col");
            echo "Executed: ALTER TABLE shipments $col<br>";
        } catch (Exception $e) {
            echo "Skipped (maybe exists): $col<br>";
        }
    }

    echo "<h3>Schema Update Completed</h3>";
    
    echo "<h4>Current Columns in cargo_requests:</h4>";
    $stmt = $pdo->query("SHOW COLUMNS FROM cargo_requests");
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo implode(", ", $cols);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
