<?php
// backend/api/customer/create_request.php

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

/* ---------------- SECURITY ---------------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

/* ---------------- PRICE CALCULATION ---------------- */
function calculatePrice(float $distance, array $items, string $vehicleType, string $pickupDate): float
{
    $baseRate = 150;
    $vehicleRates = [
        'pickup'  => 1.0,
        'isuzu'   => 1.5,
        'trailer' => 2.5
    ];
    $vehicleFactor = $vehicleRates[$vehicleType] ?? 1.0;
    $scalingFactor = 0.2;

    $totalItemCost = 0;
    foreach ($items as $item) {
        $w = floatval($item['weight'] ?? 0);
        $q = intval($item['quantity'] ?? 1);
        if ($w > 0 && $q > 0) {
            $totalItemCost += ($distance * $w * $q * $vehicleFactor * $scalingFactor);
        }
    }
    
    $price = $baseRate + $totalItemCost;

    // Date Logic
    $today = new DateTime('today');
    $pickup = new DateTime($pickupDate);
    $daysDiff = (int)$today->diff($pickup)->format('%r%a');

    if ($daysDiff <= 3) {
        $dateMultiplier = 1.50;
    } else {
        $dateMultiplier = 1.00;
    }

    return $price * $dateMultiplier;
}

try {
    /* ---------------- INPUT ---------------- */
    $raw = file_get_contents('php://input');
    if (!$raw) throw new Exception("No input received");

    $data = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON payload");
    }

    if (
        empty($data['pickup_location']) ||
        empty($data['dropoff_location']) ||
        empty($data['pickup_date']) ||
        empty($data['items'])
    ) {
        throw new Exception("Missing required fields");
    }

    /* ---------------- ITEM VALIDATION ---------------- */
    $items       = $data['items'];
    $distance    = isset($data['distance_km']) ? (float)$data['distance_km'] : 0;
    $vehicleType = isset($data['vehicle_type']) ? $data['vehicle_type'] : 'pickup';
    $pickupDate  = $data['pickup_date'];

    if ($distance <= 0) {
         throw new Exception("Invalid distance");
    }
    
    foreach ($items as $it) {
        if (empty($it['item_name']) || (float)($it['weight'] ?? 0) <= 0) {
             throw new Exception("Invalid item details");
        }
    }

    /* ---------------- PRICE (BACKEND TRUTH) ---------------- */
    $price      = calculatePrice($distance, $items, $vehicleType, $pickupDate);
    if (!is_numeric($price) || $price <= 0) {
        throw new Exception("Invalid calculated price");
    }
    $finalPrice = number_format((float)$price, 2, '.', '');

    /* ---------------- DATABASE ---------------- */
    $db = Database::getConnection();
    $db->beginTransaction();

    $txRef = Util::generateToken('TX');

    /* ---------------- GET CUSTOMER ID ---------------- */
    $stmtCust = $db->prepare("SELECT id FROM customers WHERE user_id = ?");
    $stmtCust->execute([$_SESSION['user_id']]);
    $custId = $stmtCust->fetchColumn();

    if (!$custId) {
        // Auto-create customer record if missing
        $stmtCreate = $db->prepare("INSERT INTO customers (user_id, address, city) VALUES (?, '', '')");
        $stmtCreate->execute([$_SESSION['user_id']]);
        $custId = $db->lastInsertId();
    }

    /* ---------------- CREATE REQUEST ---------------- */
    $stmt = $db->prepare("
        INSERT INTO cargo_requests (
            customer_id,
            pickup_location,
            dropoff_location,
            pickup_lat,
            pickup_lng,
            dropoff_lat,
            dropoff_lng,
            distance_km,
            price,
            payment_status,
            pickup_date,
            status,
            tx_ref,
            vehicle_type
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, 'pending', ?, ?)
    ");

    $stmt->execute([
        $custId,
        $data['pickup_location'],
        $data['dropoff_location'],
        $data['pickup_lat'] ?? null,
        $data['pickup_lng'] ?? null,
        $data['dropoff_lat'] ?? null,
        $data['dropoff_lng'] ?? null,
        $distance,
        $finalPrice,
        $data['pickup_date'],
        $txRef,
        $vehicleType
    ]);

    $requestId = $db->lastInsertId();

    /* ---------------- ITEMS ---------------- */
    $stmtItem = $db->prepare("
        INSERT INTO cargo_items (
            request_id,
            item_name,
            quantity,
            weight,
            category,
            description
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($data['items'] as $it) {
        $stmtItem->execute([
            $requestId,
            $it['item_name'],
            $it['quantity'],
            $it['weight'],
            $it['category'],
            $it['description']
        ]);
    }

    $db->commit();

    /* ---------------- USER INFO ---------------- */
    $u = $db->prepare("SELECT email, full_name FROM users WHERE id = ?");
    $u->execute([$_SESSION['user_id']]);
    $user = $u->fetch();

    if (!$user) {
        throw new Exception("User not found in database");
    }

    $email     = $user['email'];
    $fullName  = $user['full_name'];
    $_SESSION['email']     = $email;
    $_SESSION['full_name'] = $fullName;

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address in database. Please update your profile.");
    }

    $parts     = explode(' ', $fullName);
    $firstName = $parts[0] ?? 'Customer';
    $lastName  = implode(' ', array_slice($parts, 1)) ?: 'User';

    /* ---------------- CHAPA ---------------- */
    $chapa = new Chapa($_ENV['CHAPA_SECRET_KEY']);
    $postData = new PostData();

    $postData
        ->amount($finalPrice)
        ->currency('ETB')
        ->email($email)
        ->firstname($firstName)
        ->lastname($lastName)
        ->transactionRef($txRef)
        ->callbackUrl('http://localhost/cargo-project/backend/api/payment/callback.php')
        ->returnUrl("http://localhost/cargo-project/frontend/customer/dashboard.php?tx_ref=$txRef")
        ->customizations([
            'title' => 'Cargo Request Payment',
            'description' => 'Cargo Request #' . $requestId
        ]);

    // Debug Logging
    $logFile = __DIR__ . '/debug_payment.txt';
    $debugData = [
        'timestamp'    => date('Y-m-d H:i:s'),
        'tx_ref'       => $txRef,
        'amount'       => $finalPrice,
        'email'        => $email,
        'callback_url' => 'http://localhost/cargo-project/backend/api/payment/callback.php',
        'return_url'   => "http://localhost/cargo-project/frontend/customer/dashboard.php?tx_ref=$txRef"
    ];
    file_put_contents($logFile, "INIT PAYLOAD: " . json_encode($debugData) . "\n", FILE_APPEND);

    $response = $chapa->initialize($postData);

    if ($response->getStatus() !== 'success') {
        $msg = $response->getMessage();
        $errorDetail = '';

        if (is_array($msg)) {
            if (isset($msg['email'])) {
                throw new Exception("Payment provider rejected your email address. Please update your profile with a valid email.");
            }
            $errorDetail = json_encode($msg);
        } else {
            $errorDetail = (string)$msg;
        }

        throw new Exception("Chapa init failed: " . $errorDetail);
    }

    echo json_encode([
        'success'      => true,
        'request_id'   => $requestId,
        'request_eid'  => Security::encryptId($requestId),
        'price'        => $finalPrice,
        'payment_url'  => $response->getData()['checkout_url']
    ]);

} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    file_put_contents(
        __DIR__ . '/error_log.txt',
        date('[Y-m-d H:i:s] ') . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n",
        FILE_APPEND
    );

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}