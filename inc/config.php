<?php

declare(strict_types=1);

// inc/config.php - Cấu hình chung, được require đầu tiên ở mọi trang.
const MOI_TRUONG = 'prod'; // máy thật: 'prod'

error_reporting(E_ALL);
ini_set('display_errors', MOI_TRUONG === 'dev' ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php-error.log');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

set_exception_handler(function (Throwable $e): void {
    error_log((string) $e);
    http_response_code(500);
    if (MOI_TRUONG === 'dev') {
        echo '<pre>' . htmlspecialchars((string) $e) . '</pre>';
    } else {
        if (file_exists(__DIR__ . '/../500.php')) {
            require __DIR__ . '/../500.php';
        } else {
            echo "Đã xảy ra lỗi hệ thống.";
        }
    }
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
