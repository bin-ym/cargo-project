<?php
require_once __DIR__ . '/../../backend/config/session.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer • Cargo Transport</title>
    <link rel="stylesheet" href="/cargo-project/frontend/assets/css/customer.css">
    <link rel="stylesheet" href="/cargo-project/frontend/assets/css/toast.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="/cargo-project/frontend/assets/js/toast.js" defer></script>
</head>
<body>
    <button id="sidebarToggle" class="mobile-menu-toggle">
        <i data-feather="menu"></i>
    </button>