<?php
// backend/api/auth/logout.php

$projectRoot = dirname(__DIR__, 3);
require_once $projectRoot . '/backend/config/session.php';

session_unset();
session_destroy();

// Redirect to login page
header("Location: /cargo-project/");
exit();
