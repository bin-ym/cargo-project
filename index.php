<?php
// File: C:\xampp\htdocs\cargo-project\index.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

// Redirect logged-in users to their dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $redirects = [
        'customer'    => '/cargo-project/frontend/customer/dashboard.php',
        'transporter' => '/cargo-project/frontend/transporter/dashboard.php',
        'admin'       => '/cargo-project/frontend/admin/dashboard.php',
        'manager'     => '/cargo-project/frontend/manager/dashboard.php',
        'finance'     => '/cargo-project/frontend/finance/dashboard.php'
    ];
    $path = $redirects[$_SESSION['role']] ?? '/cargo-project/frontend/auth/login.php';
    header("Location: $path");
    exit();
}

// Not logged in → go to login
header("Location: /cargo-project/frontend/auth/login.php");
exit();
?>