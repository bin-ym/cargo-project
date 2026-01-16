<?php
// backend/api/transporters/index.php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../../controllers/TransporterController.php';

$controller = new TransporterController();
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_GET['_method'])) {
    $method = strtoupper($_GET['_method']);
}

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        if (isset($_GET['details']) && $_GET['details'] === 'true') {
            $transporter = $controller->getDetails($_GET['id']);
        } else {
            $transporter = $controller->getById($_GET['id']);
        }
        echo json_encode($transporter ? ["success" => true, "data" => $transporter] : ["success" => false, "error" => "Not found"]);
    } else {
        $transporters = $controller->getAll();
        echo json_encode(["success" => true, "data" => $transporters]);
    }
} elseif ($method === 'POST') {
    // Gather data from POST and FILES
    $data = $_POST;
    if (isset($_FILES['license_copy']) && $_FILES['license_copy']['error'] === UPLOAD_ERR_OK) {
        $data['license_copy'] = $_FILES['license_copy'];
    }

    $result = $controller->create($data);
    echo json_encode($result);
} elseif ($method === 'PUT') {
    if (!isset($_GET['id'])) {
        echo json_encode(["success" => false, "error" => "ID required for update"]);
        exit();
    }

    // If it was a real PUT (not spoofed), we need to read php://input
    // But our frontend uses spoofing for file uploads.
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents("php://input"), true);
    } else {
        // Spoofed PUT (POST with _method=PUT)
        $data = $_POST;
        if (isset($_FILES['license_copy']) && $_FILES['license_copy']['error'] === UPLOAD_ERR_OK) {
            $data['license_copy'] = $_FILES['license_copy'];
        }
    }

    if ($controller->update($_GET['id'], $data)) {
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
