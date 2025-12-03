<?php
$projectRoot = dirname(__DIR__, 3);
require_once $projectRoot . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable($projectRoot);
$dotenv->load();

echo "Environment loaded successfully!\n";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'Not set') . "\n";
echo "Project root: $projectRoot\n";