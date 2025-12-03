<?php
// backend/controllers/RequestController.php

require_once __DIR__ . '/../config/database.php';

class RequestController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll($status = null) {
        try {
            $sql = "SELECT r.*, u.full_name as customer_name, u.phone, u.email 
                    FROM cargo_requests r 
                    JOIN users u ON r.customer_id = u.id";
            
            if ($status) {
                $sql .= " WHERE r.status = ?";
                $stmt = $this->db->prepare($sql . " ORDER BY r.created_at DESC");
                $stmt->execute([$status]);
            } else {
                $stmt = $this->db->query($sql . " ORDER BY r.created_at DESC");
            }
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT r.*, u.full_name as customer_name, u.phone, u.email 
                    FROM cargo_requests r 
                    JOIN users u ON r.customer_id = u.id 
                    WHERE r.id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $request = $stmt->fetch();

            if ($request) {
                // Fetch items for this request
                $stmtItems = $this->db->prepare("SELECT * FROM cargo_items WHERE request_id = ?");
                $stmtItems->execute([$id]);
                $request['items'] = $stmtItems->fetchAll();
            }

            return $request;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function updateStatus($id, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE cargo_requests SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM cargo_requests WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
