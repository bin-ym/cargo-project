<?php
// backend/controllers/CargoItemController.php

require_once __DIR__ . '/../config/database.php';

class CargoItemController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        try {
            $sql = "SELECT ci.*, r.id as request_id 
                    FROM cargo_items ci 
                    JOIN cargo_requests r ON ci.request_id = r.id 
                    ORDER BY ci.id DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getByRequestId($requestId) {
        try {
            $sql = "SELECT * FROM cargo_items WHERE request_id = ? ORDER BY id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$requestId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT * FROM cargo_items WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function create($data) {
        try {
            $sql = "INSERT INTO cargo_items (request_id, item_name, quantity, weight, category, description) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['request_id'],
                $data['item_name'],
                $data['quantity'] ?? 1,
                $data['weight'],
                $data['category'],
                $data['description']
            ]);
            return ["success" => true];
        } catch (PDOException $e) {
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            $sql = "UPDATE cargo_items SET item_name = ?, quantity = ?, weight = ?, category = ?, description = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['item_name'],
                $data['quantity'],
                $data['weight'],
                $data['category'],
                $data['description'],
                $id
            ]);
            return ["success" => true];
        } catch (PDOException $e) {
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM cargo_items WHERE id = ?");
            $stmt->execute([$id]);
            return ["success" => true];
        } catch (PDOException $e) {
            return ["success" => false, "error" => $e->getMessage()];
        }
    }
}
