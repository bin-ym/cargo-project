<?php
// frontend/update_schema_v2.php
// Run this to add vehicles and ratings tables

require_once __DIR__ . '/../backend/config/database.php';

try {
    $db = Database::getConnection();
    
    echo "Starting schema updates...\n\n";
    
    // 1. Create vehicles table
    echo "Creating vehicles table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS vehicles (
            id INT PRIMARY KEY AUTO_INCREMENT,
            plate_number VARCHAR(20) UNIQUE NOT NULL,
            vehicle_type ENUM('pickup', 'isuzu', 'trailer') NOT NULL,
            status ENUM('available', 'in-use', 'maintenance') DEFAULT 'available',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "✓ Vehicles table created\n\n";
    
    // 2. Create ratings table
    echo "Creating ratings table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS ratings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            request_id INT NOT NULL,
            customer_id INT NOT NULL,
            transporter_id INT NOT NULL,
            rating INT NOT NULL CHECK(rating BETWEEN 1 AND 5),
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (request_id) REFERENCES cargo_requests(id) ON DELETE CASCADE,
            FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (transporter_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_rating (request_id, customer_id)
        )
    ");
    echo "✓ Ratings table created\n\n";
    
    // 3. Add vehicle_id to shipments table
    echo "Updating shipments table...\n";
    
    // Check if column exists
    $stmt = $db->query("SHOW COLUMNS FROM shipments LIKE 'vehicle_id'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE shipments ADD COLUMN vehicle_id INT NULL");
        $db->exec("ALTER TABLE shipments ADD FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL");
        echo "✓ Added vehicle_id to shipments\n\n";
    } else {
        echo "ℹ vehicle_id already exists in shipments\n\n";
    }
    
    // 4. Insert sample vehicles
    echo "Adding sample vehicles...\n";
    $sampleVehicles = [
        ['ET-001-AA', 'pickup'],
        ['ET-002-BB', 'isuzu'],
        ['ET-003-CC', 'trailer'],
        ['ET-004-DD', 'pickup'],
        ['ET-005-EE', 'isuzu']
    ];
    
    $stmt = $db->prepare("INSERT IGNORE INTO vehicles (plate_number, vehicle_type) VALUES (?, ?)");
    foreach ($sampleVehicles as $v) {
        $stmt->execute($v);
    }
    echo "✓ Sample vehicles added\n\n";
    
    echo "Schema updates completed successfully!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
