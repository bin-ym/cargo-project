<?php
// backend/api/cargo_items/index.php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../../controllers/CargoItemController.php';

$controller = new CargoItemController();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $item = $controller->getById($_GET['id']);
        echo json_encode($item ? ["success" => true, "data" => $item] : ["success" => false, "error" => "Not found"]);
    } elseif (isset($_GET['request_id'])) {
        $items = $controller->getByRequestId($_GET['request_id']);
        echo json_encode(["success" => true, "data" => $items]);
    } else {
        $items = $controller->getAll();
        echo json_encode(["success" => true, "data" => $items]);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $result = $controller->create($data);
    if ($result['success']) {
        echo json_encode(["success" => true, "message" => "Item created"]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to create item: " . $result['error']]);
    }
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($_GET['id'])) {
        $result = $controller->update($_GET['id'], $data);
        if ($result['success']) {
            echo json_encode(["success" => true, "message" => "Item updated"]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to update item: " . $result['error']]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Missing ID"]);
    }
} elseif ($method === 'DELETE') {
    if (isset($_GET['id'])) {
        $result = $controller->delete($_GET['id']);
        if ($result['success']) {
            echo json_encode(["success" => true, "message" => "Item deleted"]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to delete item: " . $result['error']]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Missing ID"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method not allowed"]);
}
