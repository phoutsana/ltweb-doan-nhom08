<?php

declare(strict_types=1);

// inc/config.php - Cấu hình chung, được require đầu tiên ở mọi trang.
const MOI_TRUONG = 'dev'; // máy thật: 'prod'

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
        require __DIR__ . '/../500.php';
    }
});

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (is_file($composerAutoload)) {
    require_once $composerAutoload;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $file = __DIR__ . '/../src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    });
}

require_once __DIR__ . '/ham.php';
