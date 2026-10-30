<?php
// Đăng ký error handler trả JSON 500 khi có lỗi hệ thống
set_exception_handler(function ($e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["loi" => "Lỗi máy chủ"]);
    exit;
});

require_once __DIR__ . '/../inc/config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = \db();

    // Trường hợp 1: Xem chi tiết theo ID (?id=...)
    if (isset($_GET['id'])) {
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        if ($id === false || $id <= 0) {
            http_response_code(400);
            echo json_encode(["loi" => "Mã sản phẩm không hợp lệ"]);
            exit;
        }

        $stmt = $pdo->prepare("SELECT id, danh_muc_id, ten, gia, ton_kho, anh, mau_sac, kich_thuoc, mo_ta FROM san_pham WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $sp = $stmt->fetch();

        if (!$sp) {
            http_response_code(404);
            echo json_encode(["loi" => "Không tìm thấy sản phẩm"]);
            exit;
        }

        http_response_code(200);
        echo json_encode($sp, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Trường hợp 2: Tìm kiếm theo từ khóa (?q=...)
    $q = trim($_GET['q'] ?? '');
    $tuKhoa = '%' . addcslashes($q, '%_\\') . '%';

    $stmt = $pdo->prepare("SELECT id, danh_muc_id, ten, gia, ton_kho, anh FROM san_pham WHERE ten LIKE ? LIMIT 20");
    $stmt->execute([$tuKhoa]);
    $ketQua = $stmt->fetchAll();

    http_response_code(200);
    echo json_encode($ketQua, JSON_UNESCAPED_UNICODE);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(["loi" => "Lỗi máy chủ"]);
}