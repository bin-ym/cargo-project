<?php
// backend/api/customer/initiate_payment.php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../lib/Chapa/Chapa.php';
require_once __DIR__ . '/../../lib/Chapa/Model/PostData.php';
require_once __DIR__ . '/../../lib/Chapa/Model/ResponseData.php';
require_once __DIR__ . '/../../lib/Chapa/Util.php';

use Chapa\Chapa;
use Chapa\Model\PostData;
use Chapa\Util;

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (empty($data['request_id'])) {
        throw new Exception("Request ID is required");
    }

    $requestId = $data['request_id'];
    $db = Database::getConnection();

    // Fetch request details
    $stmt = $db->prepare("
        SELECT * FROM cargo_requests 
        WHERE id = ? AND customer_id = ? AND payment_status = 'pending'
    ");
    $stmt->execute([$requestId, $_SESSION['user_id']]);
    $request = $stmt->fetch();

    if (!$request) {
        throw new Exception("Request not found or already paid");
    }

    // User Info
    $email = $_SESSION['email'] ?? null;
    $fullName = $_SESSION['full_name'] ?? null;

    if (!$email || !$fullName) {
        $u = $db->prepare("SELECT email, full_name FROM users WHERE id = ?");
        $u->execute([$_SESSION['user_id']]);
        $user = $u->fetch();

        if (!$user) throw new Exception("User info missing");

        $email    = $user['email'];
        $fullName = $user['full_name'];

        $_SESSION['email'] = $email;
        $_SESSION['full_name'] = $fullName;
    }

    $parts = explode(' ', $fullName);
    $firstName = $parts[0] ?? 'Customer';
    $lastName = implode(' ', array_slice($parts, 1)) ?: 'User';

    $finalPrice = number_format((float)$request['price'], 2, '.', '');

    // Generate new TX Ref for this attempt
    $txRef = Util::generateToken('TX');
    
    // Debug Log
    $logFile = __DIR__ . '/../payment/debug_payment.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] Initiating Payment for Request #$requestId | TX_REF: $txRef\n", FILE_APPEND);

    // Update TX Ref in DB
    $updateStmt = $db->prepare("UPDATE cargo_requests SET tx_ref = ? WHERE id = ?");
    $updateStmt->execute([$txRef, $requestId]);

    // Initialize Chapa
    $chapa = new Chapa($_ENV['CHAPA_SECRET_KEY']);
    
    $postData = new PostData();
    $postData->amount($finalPrice)
        ->currency('ETB')
        ->email($email)
        ->firstname($firstName)
        ->lastname($lastName)
        ->transactionRef($txRef)
        ->callbackUrl('http://localhost/cargo-project/backend/api/payment/callback.php')
        ->returnUrl("http://localhost/cargo-project/frontend/customer/dashboard.php?tx_ref=$txRef")
        ->customizations([
            'title' => 'Cargo Request Payment',
            'description' => 'Payment for Request #' . $requestId
        ]);

    $response = $chapa->initialize($postData);

    if ($response->getStatus() !== 'success') {
        throw new Exception("Payment initialization failed: " . json_encode($response->getMessage()));
    }

    echo json_encode([
        'success' => true,
        'payment_url' => $response->getData()['checkout_url']
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}