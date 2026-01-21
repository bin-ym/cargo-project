<?php
// backend/api/cargo_items/index.php
require_once __DIR__ . '/../../config/session.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../../controllers/CargoItemController.php';

$controller = new CargoItemController();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        if (!is_numeric($id)) {
            $id = Security::decryptId($id);
        }
        $item = $controller->getById($id);
        if ($item) {
            $item['eid'] = Security::encryptId($item['id']);
            $item['request_eid'] = Security::encryptId($item['request_id']);
        }
        echo json_encode($item ? ["success" => true, "data" => $item] : ["success" => false, "error" => "Not found"]);
    } elseif (isset($_GET['request_id'])) {
        $requestId = $_GET['request_id'];
        if (!is_numeric($requestId)) {
            $requestId = Security::decryptId($requestId);
        }
        $items = $controller->getByRequestId($requestId);
        foreach ($items as &$it) {
            $it['eid'] = Security::encryptId($it['id']);
            $it['request_eid'] = Security::encryptId($it['request_id']);
        }
        echo json_encode(["success" => true, "data" => $items]);
    } else {
        $items = $controller->getAll();
        foreach ($items as &$it) {
            $it['eid'] = Security::encryptId($it['id']);
            $it['request_eid'] = Security::encryptId($it['request_id']);
        }
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
    $id = $_GET['id'] ?? null;
    if ($id && !is_numeric($id)) {
        $id = Security::decryptId($id);
    }
    $data = json_decode(file_get_contents("php://input"), true);
    if ($id) {
        $result = $controller->update($id, $data);
        if ($result['success']) {
            echo json_encode(["success" => true, "message" => "Item updated"]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to update item: " . $result['error']]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Missing ID"]);
    }
} elseif ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if ($id && !is_numeric($id)) {
        $id = Security::decryptId($id);
    }
    if ($id) {
        $result = $controller->delete($id);
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
