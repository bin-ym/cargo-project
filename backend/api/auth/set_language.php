<?php
// backend/api/auth/set_language.php

session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$lang = $data['lang'] ?? 'en';

// Validate language
$allowed_langs = ['en', 'am'];
if (in_array($lang, $allowed_langs)) {
    $_SESSION['lang'] = $lang;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid language']);
}
