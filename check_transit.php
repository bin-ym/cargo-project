<?php
require_once __DIR__ . '/backend/config/db.php';
$stmt = $pdo->query("SELECT id, shipment_status FROM requests WHERE shipment_status = 'in-transit' LIMIT 1");
$request = $stmt->fetch();
if ($request) {
    echo "Found in-transit request ID: " . $request['id'];
} else {
    echo "No in-transit requests found.";
}
?>
