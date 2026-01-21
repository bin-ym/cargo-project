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
            $sql = "SELECT r.*, u.full_name as customer_name, u.phone, u.email,
                           s.transporter_id, t_user.full_name as transporter_name, s.status as shipment_status
                    FROM cargo_requests r 
                    JOIN customers c ON r.customer_id = c.id
                    JOIN users u ON c.user_id = u.id
                    LEFT JOIN shipments s ON r.id = s.request_id
                    LEFT JOIN users t_user ON s.transporter_id = t_user.id
                    WHERE r.payment_status = 'paid'"; // Only show paid requests
            
            if ($status) {
                $sql .= " AND r.status = ?";
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
            $sql = "SELECT r.*, u.full_name as customer_name, u.phone, u.email,
                           s.transporter_id, t_user.full_name as transporter_name, s.status as shipment_status, s.delivered_at, s.picked_up_at
                    FROM cargo_requests r 
                    JOIN customers c ON r.customer_id = c.id
                    JOIN users u ON c.user_id = u.id 
                    LEFT JOIN shipments s ON r.id = s.request_id
                    LEFT JOIN users t_user ON s.transporter_id = t_user.id
                    WHERE r.id = ? AND r.payment_status = 'paid'"; // Only show paid requests
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

    public function getByCustomerId($userId) {
        try {
            $sql = "SELECT r.*, 
                           s.transporter_id, t_user.full_name as transporter_name, s.status as shipment_status
                    FROM cargo_requests r 
                    JOIN customers c ON r.customer_id = c.id
                    LEFT JOIN shipments s ON r.id = s.request_id
                    LEFT JOIN users t_user ON s.transporter_id = t_user.id
                    WHERE c.user_id = ?
                    ORDER BY r.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function assignTransporter($requestId, $transporterId, $vehicleId = null) {
        try {
            $this->db->beginTransaction();

            // 1. Update Request Status
            $stmt = $this->db->prepare("UPDATE cargo_requests SET status = 'approved' WHERE id = ?");
            $stmt->execute([$requestId]);

            // 2. Create or Update Shipment
            $check = $this->db->prepare("SELECT id FROM shipments WHERE request_id = ?");
            $check->execute([$requestId]);
            $exists = $check->fetch();

            if ($exists) {
                $sql = "UPDATE shipments SET transporter_id = ?, status = 'assigned', assigned_at = NOW()";
                $params = [$transporterId];
                if ($vehicleId) {
                    $sql .= ", vehicle_id = ?";
                    $params[] = $vehicleId;
                }
                $sql .= " WHERE request_id = ?";
                $params[] = $requestId;
                $stmtShip = $this->db->prepare($sql);
                $stmtShip->execute($params);
            } else {
                if ($vehicleId) {
                    $stmtShip = $this->db->prepare("INSERT INTO shipments (request_id, transporter_id, vehicle_id, status) VALUES (?, ?, ?, 'assigned')");
                    $stmtShip->execute([$requestId, $transporterId, $vehicleId]);
                } else {
                    $stmtShip = $this->db->prepare("INSERT INTO shipments (request_id, transporter_id, status) VALUES (?, ?, 'assigned')");
                    $stmtShip->execute([$requestId, $transporterId]);
                }
            }

            // 3. Update vehicle status if provided
            if ($vehicleId) {
                $stmtVeh = $this->db->prepare("UPDATE vehicles SET status = 'in-use' WHERE id = ?");
                $stmtVeh->execute([$vehicleId]);
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function updateStatus($id, $status, $reason = null) {
        try {
            $sql = "UPDATE cargo_requests SET status = ?";
            $params = [$status];

            if ($reason) {
                $sql .= ", rejection_reason = ?";
                $params[] = $reason;
            }

            $sql .= " WHERE id = ?";
            $params[] = $id;

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
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
    public function getByTransporterId($transporterId) {
        try {
            $sql = "SELECT r.*, u.full_name as customer_name, u.phone, u.email,
                           s.status as shipment_status, s.id as shipment_id
                    FROM cargo_requests r 
                    JOIN shipments s ON r.id = s.request_id
                    JOIN customers c ON r.customer_id = c.id
                    JOIN users u ON c.user_id = u.id
                    WHERE s.transporter_id = ?
                    ORDER BY r.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$transporterId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function updateShipmentStatus($requestId, $status) {
        try {
            $this->db->beginTransaction();

            $sql = "UPDATE shipments SET status = ? WHERE request_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status, $requestId]);
            
            // If in-transit, set picked_up_at
            if ($status === 'in-transit') {
                $stmtPick = $this->db->prepare("UPDATE shipments SET picked_up_at = NOW() WHERE request_id = ?");
                $stmtPick->execute([$requestId]);
            }

            // If delivered, also mark request as completed and release vehicle
            if ($status === 'delivered') {
                // 1. Update request status
                $stmtReq = $this->db->prepare("UPDATE cargo_requests SET status = 'completed' WHERE id = ?");
                $stmtReq->execute([$requestId]);

                // 2. Set delivered_at
                $stmtTime = $this->db->prepare("UPDATE shipments SET delivered_at = NOW() WHERE request_id = ?");
                $stmtTime->execute([$requestId]);

                // 3. Release vehicle
                $stmtShip = $this->db->prepare("SELECT vehicle_id FROM shipments WHERE request_id = ?");
                $stmtShip->execute([$requestId]);
                $shipment = $stmtShip->fetch();
                if ($shipment && $shipment['vehicle_id']) {
                    $stmtVeh = $this->db->prepare("UPDATE vehicles SET status = 'available' WHERE id = ?");
                    $stmtVeh->execute([$shipment['vehicle_id']]);
                }
            }

            // 4. Notify Customer
            $stmtUser = $this->db->prepare("SELECT c.user_id FROM cargo_requests r JOIN customers c ON r.customer_id = c.id WHERE r.id = ?");
            $stmtUser->execute([$requestId]);
            $user = $stmtUser->fetch();

            if ($user) {
                $title = "Shipment Update";
                $message = "Your shipment #{$requestId} is now: " . ucfirst(str_replace('_', ' ', $status));
                $type = ($status === 'delivered') ? 'success' : 'info';
                
                $stmtNotif = $this->db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
                $stmtNotif->execute([$user['user_id'], $title, $message, $type]);
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // Return error message for debugging
            return "DB Error: " . $e->getMessage();
        }
    }
}
