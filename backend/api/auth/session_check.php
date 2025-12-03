<?php
// backend/api/auth/session_check.php

require_once __DIR__ . '/../../../backend/config/session.php';

header("Content-Type: application/json");

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        "valid" => true,
        "user_id" => $_SESSION['user_id'],
        "role" => $_SESSION['role'],
        "username" => $_SESSION['username']
    ]);
} else {
    http_response_code(401);
    echo json_encode(["valid" => false]);
}
