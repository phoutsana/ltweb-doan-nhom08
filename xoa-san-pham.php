<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/config.php';

// 1. KIỂM TRA QUYỀN QUẢN TRỊ (Bảo mật: khách hàng không được phép xóa)
if (!isset($_SESSION['user']) || $_SESSION['user']['vai_tro'] !== 'quan_tri') {
    http_response_code(403);
    die("<h1>Lỗi 403: Truy cập bị từ chối.</h1><p>Bạn không có quyền thực hiện thao tác xóa sản phẩm này!</p>");
}

// 2. KIỂM TRA CA 5: Xóa dữ liệu chỉ nhận POST. Nếu là GET thì chặn lại và báo lỗi 405.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    die("<h1>Lỗi 405: Phương thức không được phép (Method Not Allowed).</h1><p>Bạn không thể xóa dữ liệu bằng cách truy cập trực tiếp URL này qua phương thức GET.</p>");
}

$pdo = db();
$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM san_pham WHERE id = ?");
        $stmt->execute([$id]);
        // Xóa thành công chuyển hướng về trang quản trị
        header("Location: quan-tri-san-pham.php?tb=thanh_cong");
        exit;
    } catch (\PDOException $e) {
        // KIỂM TRA CA 5: Bắt lỗi khóa ngoại 1451 (sản phẩm đã phát sinh đơn hàng)
        if ($e->getCode() == '23000' || strpos($e->getMessage(), '1451') !== false) {
            header("Location: quan-tri-san-pham.php?loi=dang_dung");
            exit;
        } else {
            throw $e;
        }
    }
} else {
    die("ID không hợp lệ.");
}