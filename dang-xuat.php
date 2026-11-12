<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

/**
 * BT7 - Bao mat chuc nang dang xuat
 * - Chi chap nhan POST
 * - GET tra ve HTTP 405
 * - POST bat buoc CSRF token hop le
 * - Huy session va cookie dang nhap
 */

// 1. Tu choi GET va cac phuong thuc khac
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('405 Method Not Allowed');
}

// 2. Xac minh CSRF truoc khi dang xuat
csrf_verify();

// 3. Xoa du lieu Session
$_SESSION = [];

// 4. Xoa cookie Session
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'] ?? 'Lax'
    ]);
}

// 5. Huy Session
session_destroy();

// 6. Chuyen ve trang chu
header('Location: index.php', true, 303);
exit;
