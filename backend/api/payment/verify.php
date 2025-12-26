<?php
// backend/api/payment/verify.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../lib/Chapa/Chapa.php';
require_once __DIR__ . '/../../lib/Chapa/Util.php';

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
    
    // Debug Log Function
    function logDebug($msg) {
        $logFile = __DIR__ . '/debug_payment.txt';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $msg\n", FILE_APPEND);
    }

    logDebug("Starting verification for TX_REF: $txRef");

    // 1. Find request by tx_ref
    $stmt = $db->prepare("SELECT * FROM cargo_requests WHERE tx_ref = ?");
    $stmt->execute([$txRef]);
    $request = $stmt->fetch();

    if (!$request) {
        logDebug("Transaction not found in DB for TX_REF: $txRef");
        throw new Exception("Transaction not found");
    }

    logDebug("Found Request ID: " . $request['id'] . " | Current Status: " . $request['payment_status']);

    if ($request['payment_status'] === 'paid') {
        logDebug("Request already paid.");
        echo json_encode(['success' => true, 'message' => 'Already paid']);
        exit();
    }

    // 2. Verify with Chapa (or bypass for localhost/test)
    $isLocalhost = ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
    
    if ($isLocalhost && strpos($txRef, 'TX_') === 0) {
        // LOCALHOST TEST MODE: Auto-approve without calling Chapa
        logDebug("LOCALHOST TEST MODE: Auto-approving payment");
        
        $db->beginTransaction();
        $update = $db->prepare("
            UPDATE cargo_requests 
            SET payment_status = 'paid',
                updated_at = NOW()
            WHERE id = ?
        ");
        $update->execute([$request['id']]);
        $db->commit();
        
        logDebug("Database updated successfully for Request ID: " . $request['id']);
        echo json_encode(['success' => true, 'message' => 'Payment verified successfully (TEST MODE)']);
        exit();
    }

    // Production: Call Chapa API
    $chapa = new Chapa($_ENV['CHAPA_SECRET_KEY']);
    $response = $chapa->verify($txRef);

    logDebug("Chapa Response Status: " . $response->getStatus());

    if ($response->getStatus() === 'success') {
        // 3. Update Database
        $db->beginTransaction();

        $update = $db->prepare("
            UPDATE cargo_requests 
            SET payment_status = 'paid',
                updated_at = NOW()
            WHERE id = ?
        ");
        $update->execute([$request['id']]);

        $db->commit();
        logDebug("Database updated successfully for Request ID: " . $request['id']);

        echo json_encode(['success' => true, 'message' => 'Payment verified successfully']);
    } else {
        logDebug("Chapa verification failed. Message: " . json_encode($response->getMessage()));
        throw new Exception("Payment verification failed: " . json_encode($response->getMessage()));
    }

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    if (function_exists('logDebug')) {
        logDebug("ERROR: " . $e->getMessage());
    } else {
        // Fallback if error happens before function definition
        file_put_contents(__DIR__ . '/debug_payment.txt', "[ERROR] " . $e->getMessage() . "\n", FILE_APPEND);
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
