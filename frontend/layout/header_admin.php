<?php
require_once __DIR__ . '/../../backend/config/session.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin • Cargo Transport</title>
<link rel="stylesheet" href="/cargo-project/frontend/assets/css/admin.css">
<script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

<!-- Mobile Menu Toggle -->
<button class="mobile-menu-toggle" onclick="toggleSidebar()">
    <i data-feather="menu"></i>
</button>

<!-- Layout wrapper -->
<div class="dashboard-grid">

<!-- Sidebar -->
<?php include __DIR__ . '/../admin/sidebar.php'; ?>
