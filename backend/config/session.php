<?php
// backend/config/session.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_session() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "error" => "Unauthorized"]);
        exit();
    }
}

function require_role($roles) {
    require_session();

    $roles = (array)$roles;
    if (!in_array($_SESSION['role'], $roles)) {
        http_response_code(403);
        echo json_encode(["success" => false, "error" => "Forbidden"]);
        exit();
    }
}

function login_user($user) {
    session_regenerate_id(true);
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["role"] = $user["role"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["full_name"] = $user["full_name"];
}