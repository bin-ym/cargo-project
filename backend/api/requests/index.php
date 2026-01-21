<?php
// backend/api/requests/index.php
require_once __DIR__ . '/../../config/session.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../../controllers/RequestController.php';

$controller = new RequestController();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        if (!is_numeric($id)) {
            $id = Security::decryptId($id);
        }
        $request = $controller->getById($id);
        if ($request) {
            $request['eid'] = Security::encryptId($request['id']);
        }
        echo json_encode($request ? ["success" => true, "data" => $request] : ["success" => false, "error" => "Not found"]);
    } else {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $requests = $controller->getAll($status);
        foreach ($requests as &$req) {
            $req['eid'] = Security::encryptId($req['id']);
        }
        echo json_encode(["success" => true, "data" => $requests]);
    }
} elseif ($method === 'PUT') {
    $id = $_GET['id'] ?? null;
    if ($id && !is_numeric($id)) {
        $id = Security::decryptId($id);
    }
    $data = json_decode(file_get_contents("php://input"), true);
    if ($id && isset($data['status'])) {
        if ($controller->updateStatus($id, $data['status'])) {
            echo json_encode(["success" => true, "message" => "Status updated"]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to update status"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Missing ID or status"]);
    }
} elseif ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if ($id && !is_numeric($id)) {
        $id = Security::decryptId($id);
    }
    if ($id && $controller->delete($id)) {
        echo json_encode(["success" => true, "message" => "Request deleted"]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete request"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method not allowed"]);
}
