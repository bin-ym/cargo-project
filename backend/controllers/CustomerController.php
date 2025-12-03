<?php
// backend/controllers/CustomerController.php

require_once __DIR__ . '/../config/database.php';

class CustomerController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        try {
            $sql = "SELECT c.*, u.full_name as name, u.email, u.phone 
                    FROM customers c 
                    JOIN users u ON c.user_id = u.id 
                    ORDER BY c.created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT c.*, u.full_name as name, u.email, u.phone 
                    FROM customers c 
                    JOIN users u ON c.user_id = u.id 
                    WHERE c.id = ?";
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

            // 1. Create User
            // 1. Check for duplicates
            $check = $this->db->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
            $check->execute([$data['email'], $data['phone']]);
            if ($check->fetch()) {
                $this->db->rollBack();
                return ["success" => false, "error" => "Email or phone already exists"];
            }

            // 2. Create User
            $stmt = $this->db->prepare("INSERT INTO users (username, email, phone, full_name, password, role, must_change_password) VALUES (?, ?, ?, ?, ?, 'customer', 1)");
            $username = explode('@', $data['email'])[0] . rand(100, 999);
            
            // Set default password
            $rawPassword = 'cargo123';
            $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);
            
            $stmt->execute([$username, $data['email'], $data['phone'], $data['name'], $hashedPassword]);
            $userId = $this->db->lastInsertId();

            // 2. Create Customer Profile
            $stmt = $this->db->prepare("INSERT INTO customers (user_id, address, city) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $data['address'], $data['city']]);

            $this->db->commit();
            return ["success" => true, "password" => $rawPassword];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            $this->db->beginTransaction();

            $customer = $this->getById($id);
            if (!$customer) return false;
            $userId = $customer['user_id'];

            // 1. Update User
            $stmt = $this->db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$data['name'], $data['email'], $data['phone'], $userId]);

            // 2. Update Customer Profile
            $stmt = $this->db->prepare("UPDATE customers SET address = ?, city = ? WHERE id = ?");
            $stmt->execute([$data['address'], $data['city'], $id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function delete($id) {
        try {
            $customer = $this->getById($id);
            if (!$customer) return false;
            $userId = $customer['user_id'];

            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
