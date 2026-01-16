<?php
require_once __DIR__ . '/../../config/database.php';

$logFile = __DIR__ . '/debug_payment.txt';
$rawInput = file_get_contents('php://input');
$timestamp = date('Y-m-d H:i:s');
file_put_contents($logFile, "[$timestamp] CALLBACK RECEIVED: $rawInput\n", FILE_APPEND);

$data = json_decode($rawInput, true);

if (isset($data['status']) && $data['status'] === 'success') {
    $txRef = $data['tx_ref'] ?? null;

    if ($txRef) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE cargo_requests SET payment_status = 'paid', updated_at = NOW() WHERE tx_ref = ?");
            $stmt->execute([$txRef]);
            file_put_contents($logFile, "[$timestamp] CALLBACK: Updated Request for TX_REF: $txRef\n", FILE_APPEND);
        } catch (Exception $e) {
            file_put_contents($logFile, "[$timestamp] CALLBACK ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
}

http_response_code(200);
echo json_encode(['status' => 'success']);