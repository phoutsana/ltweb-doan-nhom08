<?php
declare(strict_types=1);



// BT7 - Bao ve tai nguyen nhay cam
// Dung voi PHP Development Server tren localhost.

$uri = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
);

$path = rawurldecode(is_string($uri) ? $uri : '/');
$path = str_replace('\\', '/', $path);

$segments = explode('/', trim($path, '/'));
$first = strtolower($segments[0] ?? '');
$fileName = strtolower(basename($path));

$extension = strtolower(
    pathinfo($path, PATHINFO_EXTENSION)
);

// 1. Chan thu muc noi bo
$blockedDirs = [
    'inc',
    'database',
    'logs',
    'storage',
    'vendor',
    'src',
    'kiemtra',
    '.git'
];

// 2. Chan cac loai tep nhay cam
$blockedExtensions = [
    'sql',
    'log',
    'jsonl',
    'ini',
    'bak',
    'lock',
    'env'
];

// 3. Chan tep cau hinh trong thu muc goc
$blockedFiles = [
    'composer.json',
    'composer.lock',
    'router.php',
    'api.http',
    'readme.md'
];

// 4. Chan duong dan an va traversal
$unsafePath = false;

foreach ($segments as $segment) {
    if (
        str_starts_with($segment, '.') ||
        str_contains($segment, '%') ||
        $segment === '..'
    ) {
        $unsafePath = true;
        break;
    }
}

$blocked =
    str_contains($path, "\0") ||
    $unsafePath ||
    in_array($first, $blockedDirs, true) ||
    in_array($extension, $blockedExtensions, true) ||
    in_array($fileName, $blockedFiles, true);

// 5. Thu muc uploads chi phuc vu tep anh
if ($first === 'uploads') {
    if (
        count($segments) !== 2 ||
        !in_array(
            $extension,
            ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            true
        )
    ) {
        $blocked = true;
    }
}

// 6. Tu choi yeu cau khong an toan

if ($blocked) {

    // BT7: Ghi nhat ky truy cap bi tu choi
    require_once __DIR__ . '/inc/security_audit.php';

    bt7_security_log(
        'ACCESS_DENIED',
        $path,
        403
    );

    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');

    echo '403 Forbidden';
    return;
}


// 7. Cho PHP Server phuc vu tai nguyen hop le
return false;
