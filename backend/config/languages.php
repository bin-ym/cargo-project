<?php
// backend/config/languages.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

$translations = [];

function load_translations($lang) {
    global $translations;
    $file = __DIR__ . "/../languages/{$lang}.json";
    if (file_exists($file)) {
        $translations = json_decode(file_get_contents($file), true);
    } else {
        $translations = [];
    }
}

function __($key, $lang = null) {
    global $translations;
    
    if (!$lang) {
        $lang = $_SESSION['lang'] ?? 'en';
    }

    if (empty($translations)) {
        load_translations($lang);
    }

    return $translations[$key] ?? $key;
}

// Pre-load translations for the current session language
load_translations($_SESSION['lang']);
