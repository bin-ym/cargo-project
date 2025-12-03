<?php
// backend/api/transporters/index.php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../../controllers/TransporterController.php';

$controller = new TransporterController();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $transporter = $controller->getById($_GET['id']);
        echo json_encode($transporter ? ["success" => true, "data" => $transporter] : ["success" => false, "error" => "Not found"]);
    } else {
        $transporters = $controller->getAll();
        echo json_encode(["success" => true, "data" => $transporters]);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $result = $controller->create($data);
    if ($result['success']) {
        echo json_encode($result);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to create transporter: " . ($result['error'] ?? 'Unknown error')]);
    }
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($_GET['id']) && $controller->update($_GET['id'], $data)) {
        echo json_encode(["success" => true, "message" => "Transporter updated"]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to update transporter"]);
    }
} elseif ($method === 'DELETE') {
    if (isset($_GET['id']) && $controller->delete($_GET['id'])) {
        echo json_encode(["success" => true, "message" => "Transporter deleted"]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete transporter"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method not allowed"]);
}
