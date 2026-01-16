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
            $sql = "SELECT t.*, u.full_name as name, u.email, u.phone, u.username 
                    FROM transporters t 
                    JOIN users u ON t.user_id = u.id 
                    ORDER BY t.created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getAll: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            // Search by transporter ID or User ID for robustness
            $sql = "SELECT t.*, u.full_name as name, u.email, u.phone, u.username, u.id as user_id
                    FROM transporters t 
                    JOIN users u ON t.user_id = u.id 
                    WHERE t.id = ? OR u.id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }

    public function getDetails($id) {
        try {
            // 1. Get Basic Info
            $transporter = $this->getById($id);
            if (!$transporter) return null;

            // 2. Get Vehicles (assigned via shipments)
            try {
                $stmt = $this->db->prepare("SELECT DISTINCT v.* 
                                           FROM vehicles v 
                                           JOIN shipments s ON v.id = s.vehicle_id 
                                           WHERE s.transporter_id = ?");
                $stmt->execute([$transporter['user_id']]);
                $transporter['vehicles'] = $stmt->fetchAll();
            } catch (PDOException $e) {
                error_log("Error fetching vehicles for transporter: " . $e->getMessage());
                $transporter['vehicles'] = [];
            }

            // 3. Get Recent Requests
            try {
                $stmt = $this->db->prepare("SELECT r.*, u.full_name as customer_name 
                                           FROM cargo_requests r 
                                           JOIN users u ON r.customer_id = u.id 
                                           JOIN shipments s ON r.id = s.request_id
                                           WHERE s.transporter_id = ? 
                                           ORDER BY r.created_at DESC LIMIT 5");
                $stmt->execute([$transporter['user_id']]);
                $transporter['recent_requests'] = $stmt->fetchAll();
            } catch (PDOException $e) {
                error_log("Error fetching requests for transporter: " . $e->getMessage());
                $transporter['recent_requests'] = [];
            }

            return $transporter;
        } catch (Exception $e) {
            error_log("Error in getDetails: " . $e->getMessage());
            return null;
        }
    }

    public function create($data) {
        try {
            $this->db->beginTransaction();

            // 1. Check for duplicates
            $check = $this->db->prepare("SELECT id FROM users WHERE email = ? OR phone = ? OR username = ?");
            $check->execute([$data['email'], $data['phone'], $data['username']]);
            if ($check->fetch()) {
                $this->db->rollBack();
                return ["success" => false, "error" => "Email, phone, or username already exists"];
            }

            // 2. Handle File Upload
            $licensePath = null;
            if (isset($data['license_copy']) && is_array($data['license_copy'])) {
                $file = $data['license_copy'];
                $projectRoot = dirname(__DIR__, 2);
                $uploadDir = $projectRoot . '/uploads/licenses/';
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('license_', true) . '.' . $ext;
                $destination = $uploadDir . $filename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $licensePath = 'uploads/licenses/' . $filename;
                }
            }

            // 3. Create User
            $stmt = $this->db->prepare("INSERT INTO users (username, email, phone, full_name, password, role, is_verified) VALUES (?, ?, ?, ?, ?, 'transporter', 1)");
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->execute([$data['username'], $data['email'], $data['phone'], $data['name'], $hashedPassword]);
            $userId = $this->db->lastInsertId();

            // 4. Create Transporter Profile
            $stmt = $this->db->prepare("INSERT INTO transporters (user_id, status, license_copy) VALUES (?, ?, ?)");
            $stmt->execute([
                $userId, 
                $data['status'] ?? 'pending',
                $licensePath
            ]);

            $this->db->commit();
            return ["success" => true];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
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

            // 1. Handle File Upload (if new license copy provided)
            $licensePath = $transporter['license_copy'];
            if (isset($data['license_copy']) && is_array($data['license_copy'])) {
                $file = $data['license_copy'];
                $projectRoot = dirname(__DIR__, 2);
                $uploadDir = $projectRoot . '/uploads/licenses/';
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('license_', true) . '.' . $ext;
                $destination = $uploadDir . $filename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $licensePath = 'uploads/licenses/' . $filename;
                }
            }

            // 2. Update User
            $stmt = $this->db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$data['name'], $data['email'], $data['phone'], $userId]);

            // 3. Update Transporter Profile
            $stmt = $this->db->prepare("UPDATE transporters SET status = ?, license_copy = ? WHERE id = ?");
            $stmt->execute([
                $data['status'], 
                $licensePath,
                $id
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
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
