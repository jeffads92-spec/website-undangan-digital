<?php
// includes/Security.php

class Security {
    
    // Generate CSRF Token
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Verify CSRF Token
    public static function verifyCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            die('CSRF Validation Failed');
        }
        return true;
    }

    // Membersihkan Input XSS
    public static function cleanInput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::cleanInput($value);
            }
        } else {
            $data = htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }

    // Validasi Upload Gambar
    public static function validateImage($file) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed)) {
            return ['valid' => false, 'msg' => 'Format file harus JPG, PNG, atau WebP'];
        }

        if ($file['size'] > $max_size) {
            return ['valid' => false, 'msg' => 'Ukuran file maksimal 5MB'];
        }

        return ['valid' => true];
    }
}
?>
