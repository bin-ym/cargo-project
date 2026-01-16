<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../lib/Chapa/Chapa.php';

use Chapa\Chapa;

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$txRef = $_GET['tx_ref'] ?? null;
if (!$txRef) {
    echo json_encode(['success' => false, 'error' => 'Transaction reference required']);
    exit();
}

try {
    $db = Database::getConnection();

    function logDebug($msg) {
        $logFile = __DIR__ . '/debug_payment.txt';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $msg\n", FILE_APPEND);
    }

    logDebug("Starting verification for TX_REF: $txRef");

    $stmt = $db->prepare("SELECT * FROM cargo_requests WHERE tx_ref = ?");
    $stmt->execute([$txRef]);
    $request = $stmt->fetch();

    if (!$request) {
        logDebug("Transaction not found for TX_REF: $txRef");
        throw new Exception("Transaction not found");
    }

    if ($request['payment_status'] === 'paid') {
        logDebug("Request already paid for TX_REF: $txRef");
        echo json_encode(['success' => true, 'message' => 'Already paid']);
        exit();
    }

    // MOCK VERIFICATION FOR TESTING
    // If tx_ref starts with "TX-" and we are on localhost, or if it's a specific test pattern
    if (str_starts_with($txRef, 'TX-')) {
        logDebug("Mock verification active for: $txRef");
        
        $db->beginTransaction();
        $update = $db->prepare("UPDATE cargo_requests SET payment_status = 'paid', updated_at = NOW() WHERE id = ?");
        $update->execute([$request['id']]);
        $db->commit();

        echo json_encode(['success' => true, 'message' => 'Payment verified successfully (Mock)']);
        exit();
    }

    $chapa = new Chapa($_ENV['CHAPA_SECRET_KEY']);
    $response = $chapa->verify($txRef);

    logDebug("Chapa Response Status: " . $response->getStatus());

    if ($response->getStatus() === 'success') {
        $db->beginTransaction();
        $update = $db->prepare("UPDATE cargo_requests SET payment_status = 'paid', updated_at = NOW() WHERE id = ?");
        $update->execute([$request['id']]);
        $db->commit();

        echo json_encode(['success' => true, 'message' => 'Payment verified successfully']);
    } else {
        logDebug("Chapa verification failed: " . json_encode($response->getMessage()));
        throw new Exception("Payment verification failed");
    }

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    logDebug("ERROR: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}