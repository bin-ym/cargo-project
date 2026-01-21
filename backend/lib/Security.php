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
        $ivLen = openssl_cipher_iv_length(self::$method);
        $iv = openssl_random_pseudo_bytes($ivLen);
        $encrypted = openssl_encrypt($data, self::$method, $key, 0, $iv);
        // Use JSON and base64 for safe transport
        return base64_encode(json_encode(['data' => $encrypted, 'iv' => base64_encode($iv)]));
    }

    public static function decrypt($data) {
        $key = self::getKey();
        $decoded = base64_decode($data);
        $payload = json_decode($decoded, true);

        if (!is_array($payload) || !isset($payload['data'], $payload['iv'])) {
            // Fallback to old format for backward compatibility temporarily, or just fail
            if (strpos($decoded, '::') !== false) {
                 list($encrypted_data, $iv) = explode('::', $decoded, 2);
                 // Fix IV length if needed
                 $ivLen = openssl_cipher_iv_length(self::$method);
                 if (strlen($iv) !== $ivLen) {
                     // If it's too short, pad it (dangerous but fixes crash)
                     // If too long, cut it
                     $iv = str_pad(substr($iv, 0, $ivLen), $ivLen, "\0");
                 }
                 return openssl_decrypt($encrypted_data, self::$method, $key, 0, $iv);
            }
            return false;
        }
        
        $iv = base64_decode($payload['iv']);
        $encrypted_data = $payload['data'];
        
        // Ensure IV is correct length
        $expectedLen = openssl_cipher_iv_length(self::$method);
        if (strlen($iv) !== $expectedLen) {
            return false;
        }

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
