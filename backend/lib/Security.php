<?php
// backend/lib/Security.php

class Security {
    private static $method = 'AES-256-CBC';
    private static $key = null;

    private static function getKey() {
        if (self::$key === null) {
            // Try to get from environment or use a fallback (not ideal for production)
            self::$key = $_ENV['APP_KEY'] ?? 'cargo-project-secret-key-2026';
        }
        return hash('sha256', self::$key);
    }

    public static function encrypt($data) {
        $key = self::getKey();
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::$method));
        $encrypted = openssl_encrypt($data, self::$method, $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    public static function decrypt($data) {
        $key = self::getKey();
        $decoded = base64_decode($data);
        if (strpos($decoded, '::') === false) return false;
        
        list($encrypted_data, $iv) = explode('::', $decoded, 2);
        return openssl_decrypt($encrypted_data, self::$method, $key, 0, $iv);
    }

    public static function encryptId($id) {
        return self::encrypt((string)$id);
    }

    public static function decryptId($encryptedId) {
        $decrypted = self::decrypt($encryptedId);
        return $decrypted !== false ? (int)$decrypted : false;
    }
}
