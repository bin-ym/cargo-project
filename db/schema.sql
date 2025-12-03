-- Users Table (Provided)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'finance', 'transporter', 'customer') NOT NULL,
    must_change_password BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transporters Profile
CREATE TABLE IF NOT EXISTS transporters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_type VARCHAR(100),
    plate_number VARCHAR(50),
    capacity VARCHAR(50),
    status ENUM('pending', 'approved', 'suspended') DEFAULT 'pending',
    license_copy VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Customers Profile
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address TEXT,
    city VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Cargo Requests
CREATE TABLE IF NOT EXISTS cargo_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    pickup_location VARCHAR(255) NOT NULL,
    dropoff_location VARCHAR(255) NOT NULL,
    pickup_date DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Cargo Items
CREATE TABLE IF NOT EXISTS cargo_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity INT DEFAULT 1,
    weight VARCHAR(50),
    category VARCHAR(100),
    description TEXT,
    FOREIGN KEY (request_id) REFERENCES cargo_requests(id) ON DELETE CASCADE
);

-- Shipments (Orders)
CREATE TABLE IF NOT EXISTS shipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    transporter_id INT,
    status ENUM('assigned', 'in-transit', 'delivered', 'cancelled') DEFAULT 'assigned',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivered_at TIMESTAMP NULL,
    FOREIGN KEY (request_id) REFERENCES cargo_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (transporter_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert sample data for testing (only if tables are empty)
-- Note: Passwords are hashed, using a placeholder for now or assuming the provided ones work.

-- 1. Insert Users (if not exist)
INSERT INTO users (username, email, phone, full_name, password, role)
SELECT * FROM (SELECT 'amanuel', 'amanuel@debark.edu.et', '094011116', 'Amanuel Asmare', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2...', 'transporter') AS tmp
WHERE NOT EXISTS (SELECT username FROM users WHERE username = 'amanuel') LIMIT 1;

INSERT INTO users (username, email, phone, full_name, password, role)
SELECT * FROM (SELECT 'abiye', 'abiye@debark.edu.et', '091401083', 'Abiye Birhan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2...', 'customer') AS tmp
WHERE NOT EXISTS (SELECT username FROM users WHERE username = 'abiye') LIMIT 1;

-- 2. Insert Transporter Profile
INSERT INTO transporters (user_id, vehicle_type, plate_number, capacity, status)
SELECT id, 'Isuzu FSR', 'ABC-123', '3000kg', 'approved'
FROM users WHERE username = 'amanuel'
AND NOT EXISTS (SELECT user_id FROM transporters WHERE user_id = users.id);

-- 3. Insert Customer Profile
INSERT INTO customers (user_id, address, city)
SELECT id, 'Kebele 01', 'Debark'
FROM users WHERE username = 'abiye'
AND NOT EXISTS (SELECT user_id FROM customers WHERE user_id = users.id);
