<?php
// backend/api/requests/index.php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../../controllers/RequestController.php';

$controller = new RequestController();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $request = $controller->getById($_GET['id']);
        echo json_encode($request ? ["success" => true, "data" => $request] : ["success" => false, "error" => "Not found"]);
    } else {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $requests = $controller->getAll($status);
        echo json_encode(["success" => true, "data" => $requests]);
    }
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($_GET['id']) && isset($data['status'])) {
        if ($controller->updateStatus($_GET['id'], $data['status'])) {
            echo json_encode(["success" => true, "message" => "Status updated"]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to update status"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Missing ID or status"]);
    }
} elseif ($method === 'DELETE') {
    if (isset($_GET['id']) && $controller->delete($_GET['id'])) {
        echo json_encode(["success" => true, "message" => "Request deleted"]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete request"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method not allowed"]);
}
