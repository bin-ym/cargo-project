//backend/testConfig.php

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure Composer's autoload is included
require __DIR__ . '/../vendor/autoload.php'; // Adjust the path if necessary

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../backend/config');
$dotenv->load();

// Access and print the environment variables
echo "Database Host: " . $_ENV['DB_HOST'] . "\n";
echo "Database Name: " . $_ENV['DB_NAME'] . "\n";
echo "Chapa Secret Key: " . $_ENV['CHAPA_SECRET_KEY'] . "\n";
echo "Base URL: " . $_ENV['BASE_URL'] . "\n";
echo "Session Timeout: " . $_ENV['SESSION_TIMEOUT'] . "\n";