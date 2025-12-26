<?php
// frontend/update_schema_v3.php
require_once __DIR__ . '/../backend/config/database.php';

try {
    $db = Database::getConnection();
    
    echo "<h2>Database Schema Update v3</h2>";
    echo "<ul>";

    // 1. Add updated_at to cargo_requests
    try {
        $db->exec("ALTER TABLE cargo_requests ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
        echo "<li>Added updated_at to cargo_requests</li>";
    } catch (PDOException $e) {
        echo "<li>updated_at already exists in cargo_requests or error: " . $e->getMessage() . "</li>";
    }

    // 2. Add vehicle_id and updated_at to shipments
    try {
        $db->exec("ALTER TABLE shipments ADD COLUMN vehicle_id INT AFTER transporter_id");
        echo "<li>Added vehicle_id to shipments</li>";
    } catch (PDOException $e) {
        echo "<li>vehicle_id already exists in shipments or error: " . $e->getMessage() . "</li>";
    }

    try {
        $db->exec("ALTER TABLE shipments ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER delivered_at");
        echo "<li>Added updated_at to shipments</li>";
    } catch (PDOException $e) {
        echo "<li>updated_at already exists in shipments or error: " . $e->getMessage() . "</li>";
    }

    // 3. Create vehicles table
    $sqlVehicles = "CREATE TABLE IF NOT EXISTS vehicles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        plate_number VARCHAR(20) UNIQUE NOT NULL,
        vehicle_type ENUM('pickup', 'isuzu', 'trailer') NOT NULL,
        status ENUM('available', 'in-use', 'maintenance') DEFAULT 'available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $db->exec($sqlVehicles);
    echo "<li>Ensured vehicles table exists</li>";

    // 4. Create ratings table
    $sqlRatings = "CREATE TABLE IF NOT EXISTS ratings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        request_id INT NOT NULL,
        customer_id INT NOT NULL,
        transporter_id INT NOT NULL,
        rating INT CHECK(rating BETWEEN 1 AND 5),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(request_id, customer_id),
        FOREIGN KEY (request_id) REFERENCES cargo_requests(id) ON DELETE CASCADE,
        FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (transporter_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $db->exec($sqlRatings);
    echo "<li>Ensured ratings table exists</li>";

    // 5. Insert Sample Users
    $passHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
    
    // Admin
    $adminSql = "INSERT INTO users (username, email, phone, full_name, password, role)
                 SELECT * FROM (SELECT 'admin' as un, 'admin@cargo.com' as em, '0911223344' as ph, 'System Admin' as fn, '$passHash' as pw, 'admin' as rl) AS tmp
                 WHERE NOT EXISTS (SELECT username FROM users WHERE username = 'admin') LIMIT 1";
    $db->exec($adminSql);
    echo "<li>Ensured admin user exists</li>";

    // Transporter
    $transSql = "INSERT INTO users (username, email, phone, full_name, password, role)
                 SELECT * FROM (SELECT 'amanuel' as un, 'amanuel@debark.edu.et' as em, '094011116' as ph, 'Amanuel Asmare' as fn, '$passHash' as pw, 'transporter' as rl) AS tmp
                 WHERE NOT EXISTS (SELECT username FROM users WHERE username = 'amanuel') LIMIT 1";
    $db->exec($transSql);
    echo "<li>Ensured transporter user exists</li>";

    // Customer
    $custSql = "INSERT INTO users (username, email, phone, full_name, password, role)
                 SELECT * FROM (SELECT 'abiye' as un, 'abiye@debark.edu.et' as em, '091401083' as ph, 'Abiye Birhan' as fn, '$passHash' as pw, 'customer' as rl) AS tmp
                 WHERE NOT EXISTS (SELECT username FROM users WHERE username = 'abiye') LIMIT 1";
    $db->exec($custSql);
    echo "<li>Ensured customer user exists</li>";

    echo "</ul>";
    echo "<p style='color: green;'><strong>Update completed successfully!</strong></p>";
    echo "<a href='customer/dashboard.php'>Go to Dashboard</a>";

} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>Error: " . $e->getMessage() . "</strong></p>";
}
?>
