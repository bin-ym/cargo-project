<?php
// backend/controllers/TransporterController.php

require_once __DIR__ . '/../config/database.php';

class TransporterController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        try {
            $sql = "SELECT t.*, u.full_name as name, u.email, u.phone 
                    FROM transporters t 
                    JOIN users u ON t.user_id = u.id 
                    ORDER BY t.created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT t.*, u.full_name as name, u.email, u.phone 
                    FROM transporters t 
                    JOIN users u ON t.user_id = u.id 
                    WHERE t.id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function create($data) {
        try {
            $this->db->beginTransaction();

            // 1. Check for duplicates
            $check = $this->db->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
            $check->execute([$data['email'], $data['phone']]);
            if ($check->fetch()) {
                $this->db->rollBack();
                return ["success" => false, "error" => "Email or phone already exists"];
            }

            // 2. Create User
            $stmt = $this->db->prepare("INSERT INTO users (username, email, phone, full_name, password, role, must_change_password) VALUES (?, ?, ?, ?, ?, 'transporter', 1)");
            // Generate a username from email if not provided, or just use email prefix
            $username = explode('@', $data['email'])[0] . rand(100, 999);
            
            // Set default password
            $rawPassword = 'cargo123';
            $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);
            
            $stmt->execute([$username, $data['email'], $data['phone'], $data['name'], $hashedPassword]);
            $userId = $this->db->lastInsertId();

            // 2. Create Transporter Profile
            $stmt = $this->db->prepare("INSERT INTO transporters (user_id, vehicle_type, plate_number, capacity, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId, 
                $data['vehicle_type'] ?? null, 
                $data['plate_number'] ?? null, 
                $data['capacity'] ?? null, 
                $data['status'] ?? 'pending'
            ]);

            $this->db->commit();
            // error_log("Created user with password: " . $rawPassword); 
            return ["success" => true, "password" => $rawPassword];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            $this->db->beginTransaction();

            // Get user_id
            $transporter = $this->getById($id);
            if (!$transporter) return false;
            $userId = $transporter['user_id'];

            // 1. Update User
            $stmt = $this->db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$data['name'], $data['email'], $data['phone'], $userId]);

            // 2. Update Transporter Profile
            $stmt = $this->db->prepare("UPDATE transporters SET vehicle_type = ?, plate_number = ?, capacity = ?, status = ? WHERE id = ?");
            $stmt->execute([
                $data['vehicle_type'] ?? $transporter['vehicle_type'], 
                $data['plate_number'] ?? $transporter['plate_number'], 
                $data['capacity'] ?? $transporter['capacity'], 
                $data['status'], 
                $id
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function delete($id) {
        try {
            // Get user_id to delete the user (cascade will delete transporter)
            $transporter = $this->getById($id);
            if (!$transporter) return false;
            $userId = $transporter['user_id'];

            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
