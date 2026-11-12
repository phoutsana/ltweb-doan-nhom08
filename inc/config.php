<?php

declare(strict_types=1);

// inc/config.php - Cấu hình chung, được require đầu tiên ở mọi trang.
const MOI_TRUONG = 'dev'; // máy thật: 'prod'

error_reporting(E_ALL);
ini_set('display_errors', MOI_TRUONG === 'dev' ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php-error.log');

 // BT7: Gia co bao mat Session va Cookie
 if (session_status() === PHP_SESSION_NONE) {

     // Chan PHP su dung Session ID chua duoc tao
     ini_set('session.use_strict_mode', '1');

     // Chi su dung cookie de luu Session ID
     ini_set('session.use_only_cookies', '1');

     // Kiem tra website co su dung HTTPS hay khong
     $isHttps = (
         !empty($_SERVER['HTTPS']) &&
         $_SERVER['HTTPS'] !== 'off'
     );

     // Cau hinh cookie bao mat
     session_set_cookie_params([
         'lifetime' => 0,
         'path' => '/',
         'secure' => $isHttps,
         'httponly' => true,
         'samesite' => 'Lax'
     ]);

     // Khoi tao session sau khi cau hinh cookie
     session_start();
 }

 // BT7 - HTTP Security Headers
 if (!headers_sent()) {
     header('X-Content-Type-Options: nosniff');

     header('X-Frame-Options: SAMEORIGIN');

     header(
         'Referrer-Policy: strict-origin-when-cross-origin'
     );

     header(
         'Permissions-Policy: camera=(), microphone=(), geolocation=()'
     );

     // CSP co ban duoc ap dung ngay
     header(
         "Content-Security-Policy: " .
         "object-src 'none'; " .
         "base-uri 'self'; " .
         "frame-ancestors 'self'"
     );

     // Chinh sach CSP nghiem ngat de theo doi va
     // chuan bi loai bo script/style inline
     header(
         "Content-Security-Policy-Report-Only: " .
         "default-src 'self'; " .
         "script-src 'self'; " .
         "style-src 'self'; " .
         "img-src 'self' data: https:; " .
         "object-src 'none'; " .
         "base-uri 'self'; " .
         "frame-ancestors 'self'"
     );
 }

/**
 * BT7 - Xu ly loi ngoai le tap trung.
 * Ghi loi vao nhat ky, khong hien chi tiet noi bo.
 */
set_exception_handler(function (Throwable $e): void {
    error_log((string) $e);

    http_response_code(500);

    if (!headers_sent()) {
        header('Content-Type: text/plain; charset=UTF-8');
    }

    echo 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.';
});

// 1. Tự động nạp Class (Thay thế hoàn toàn cho vendor/autoload.php)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// 2. Nạp các file hàm và CSDL dùng chung cho toàn hệ thống
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ham.php';
