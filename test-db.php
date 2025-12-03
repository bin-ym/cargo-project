<?php
// test-db.php - run this in browser: http://localhost/cargo-project/test-db.php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/config/database.php';

use Dotenv\Dotenv;

try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/backend/config');
    $dotenv->load();

    $pdo = Database::getConnection();
    $stmt = $pdo->query("SELECT username, role FROM users WHERE username = 'admin'");
    $user = $stmt->fetch();

    echo "<pre>";
    print_r($user);
    echo "</pre>";

    echo "DATABASE CONNECTED AND USER FOUND!";
} catch (Exception $e) {
    die("ERROR: " . $e->getMessage());
}