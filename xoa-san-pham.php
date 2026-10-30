<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/config.php';

// KIỂM TRA CA 5: Xóa dữ liệu chỉ nhận POST. Nếu là GET thì chặn lại và báo lỗi 405.
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
        // Xóa thành công
        header("Location: quan-tri-san-pham.php?tb=thanh_cong");
        exit;
    } catch (\PDOException $e) {
        // KIỂM TRA CA 5: Bắt lỗi 1451 (Cannot delete or update a parent row)
        if ($e->getCode() == '23000' || strpos($e->getMessage(), '1451') !== false) {
            header("Location: quan-tri-san-pham.php?loi=dang_dung");
            exit;
        } else {
            // Lỗi hệ thống khác
            throw $e;
        }
    }
} else {
    die("ID không hợp lệ.");
}