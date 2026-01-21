<?php
// backend/api/customers/index.php
require_once __DIR__ . '/../../config/session.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../../controllers/CustomerController.php';

$controller = new CustomerController();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        if (!is_numeric($id)) {
            $id = Security::decryptId($id);
        }
        $customer = $controller->getById($id);
        if ($customer) {
            $customer['eid'] = Security::encryptId($customer['id']);
        }
        echo json_encode($customer ? ["success" => true, "data" => $customer] : ["success" => false, "error" => "Not found"]);
    } else {
        $customers = $controller->getAll();
        foreach ($customers as &$c) {
            $c['eid'] = Security::encryptId($c['id']);
        }
        echo json_encode(["success" => true, "data" => $customers]);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $result = $controller->create($data);
    if (is_array($result)) {
        echo json_encode($result);
    } elseif ($result === true) {
        echo json_encode(["success" => true, "message" => "Customer created"]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to create customer"]);
    }
} elseif ($method === 'PUT') {
    $id = $_GET['id'] ?? null;
    if ($id && !is_numeric($id)) {
        $id = Security::decryptId($id);
    }
    $data = json_decode(file_get_contents("php://input"), true);
    if ($id && $controller->update($id, $data)) {
        echo json_encode(["success" => true, "message" => "Customer updated"]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to update customer"]);
    }
} elseif ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if ($id && !is_numeric($id)) {
        $id = Security::decryptId($id);
    }
    if ($id && $controller->delete($id)) {
        echo json_encode(["success" => true, "message" => "Customer deleted"]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete customer"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method not allowed"]);
}
