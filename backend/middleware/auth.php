<?php
require_once __DIR__ . '/../config/session.php';

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /cargo-project/frontend/auth/login.php");
        exit();
    }
}

function require_role($role) {
    require_login();
    if (($_SESSION['role'] ?? '') !== $role) {
        header("Location: /cargo-project/frontend/auth/login.php?error=unauthorized");
        exit();
    }
}
