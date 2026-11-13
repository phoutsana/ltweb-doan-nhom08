<?php
declare(strict_types=1);



// BT7 - Bo ham CSRF tap trung
// Ghi log khi yeu cau CSRF khong hop le

require_once __DIR__ . '/security_audit.php';

// 1. Tao CSRF token va luu trong session
function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        throw new LogicException(
            'Session chua khoi tao'
        );
    }

    if (
        !isset($_SESSION['csrf_token']) ||
        !is_string($_SESSION['csrf_token']) ||
        $_SESSION['csrf_token'] === ''
    ) {
        $_SESSION['csrf_token'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['csrf_token'];
}

// 2. Tao input an chua CSRF token
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' .
        htmlspecialchars(
            csrf_token(),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        ) .
        '">';
}

// 3. Xac minh CSRF token truoc khi xu ly POST
function csrf_verify(): void
{
    $submitted = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
    $expected = $_SESSION['csrf_token'] ?? null;

    $valid = (
        ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' &&
        is_string($submitted) &&
        is_string($expected) &&
        $expected !== '' &&
        hash_equals($expected, $submitted)
    );

    if (!$valid) {

        // Lay duong dan, khong ghi query string
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        $path = parse_url(
            $requestUri,
            PHP_URL_PATH
        );

        if (!is_string($path) || $path === '') {
            $path = '/';
        }

        // BT7: Ghi nhat ky CSRF bi tu choi
        bt7_security_log(
            'CSRF_REJECTED',
            $path,
            403
        );

        http_response_code(403);

$loi = 'Trang đã hết hạn, hãy tải lại trang rồi gửi lại';

// Kiem tra request co phai API JSON
$duongDan = (string) ($_SERVER['SCRIPT_NAME'] ?? '');

$contentType = strtolower(
    (string) ($_SERVER['CONTENT_TYPE'] ?? '')
);

$laJson = str_contains($duongDan, '/api/')
    || str_contains($contentType, 'application/json');

if ($laJson) {
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(
        ['loi' => $loi],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

header('Content-Type: text/plain; charset=utf-8');
exit($loi);
    }
}
// Ten ham tuong thich voi de Bai tap 7
function csrfToken(): string
{
    return csrf_token();
}

function csrfField(): string
{
    return csrf_field();
}