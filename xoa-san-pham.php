<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

// BT7: Xoa san pham chi duoc thuc hien bang POST.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('405 Method Not Allowed');
}

// Chi quan tri vien duoc phep xoa san pham.
if (
    !is_array($_SESSION['user'] ?? null) ||
    ($_SESSION['user']['vai_tro'] ?? null) !== 'quan_tri'
) {
    http_response_code(403);
    exit('403 Forbidden');
}

// Token sai hoac thieu: HTTP 403; khong xoa du lieu.
csrf_verify();

$id = filter_var(
    $_POST['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);
if ($id === false || $id === null) {
    http_response_code(400);
    exit('Ma san pham khong hop le.');
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('DELETE FROM san_pham WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() < 1) {
        http_response_code(404);
        exit('San pham khong ton tai.');
    }

    header('Location: quan-tri-san-pham.php?tb=thanh_cong', true, 303);
    exit;
} catch (PDOException $e) {
    // MySQL 1451: San pham dang duoc tham chieu boi don hang.
    $mysqlCode = (int) ($e->errorInfo[1] ?? 0);
    if ($mysqlCode === 1451) {
        header('Location: quan-tri-san-pham.php?loi=dang_dung', true, 303);
        exit;
    }

    error_log('[BT7_XOA_SAN_PHAM] ' . $e->getMessage());
    http_response_code(500);
    exit('Khong the xoa san pham. Vui long thu lai sau.');
}
