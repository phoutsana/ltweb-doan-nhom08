<?php
declare(strict_types=1);

/**
 * Tệp: inc/xac-thuc.php
 * Chức năng: Quản lý thông tin người dùng đang đăng nhập và phân quyền hệ thống.
 */

// Lấy thông tin người dùng hiện tại từ session
function nguoiDung(): ?array {
    return $_SESSION['user'] ?? null;
}

// Kiểm tra bắt buộc phải đăng nhập (chưa đăng nhập thì chuyển hướng về trang đăng nhập)
function yeuCauDangNhap(): void {
    if (!nguoiDung()) {
        header('Location: dang-nhap.php');
        exit;
    }
}

// Kiểm tra phân quyền theo vai trò hoặc chủ sở hữu dữ liệu
function yeuCauQuyen(string $vaiTro): void {
    $user = nguoiDung();
    if (!$user) {
        header('Location: dang-nhap.php');
        exit;
    }
    if ($user['vai_tro'] !== $vaiTro) {
        http_response_code(403);
        require_once __DIR__ . '/../403.php';
        exit;
    }
}
