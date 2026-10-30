<?php
require_once __DIR__ . '/inc/config.php';

use App\Services\GioHang;

// Bắt buộc phải đăng nhập mới được đặt hàng
if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap.php');
    exit;
}

$gioService = new GioHang();
$chiTietGio = $gioService->chiTiet();

if (empty($chiTietGio)) {
    header('Location: gio-hang.php');
    exit;
}

$pdo = \db();

try {
    // Bắt đầu giao dịch (Transaction bắt buộc theo Chức năng 5)
    $pdo->beginTransaction();

    $userId = $_SESSION['user']['id'];
    $tongTien = $gioService->tongTien();

    // 1. Thêm vào bảng don_hang (Đã lược bỏ cột ngay_tao để tránh lỗi nếu CSDL không có cột này)
    $stmtDh = $pdo->prepare("INSERT INTO don_hang (nguoi_dung_id, tong_tien, trang_thai) VALUES (?, ?, 'dang_xu_ly')");
    $stmtDh->execute([$userId, $tongTien]);
    $donHangId = $pdo->lastInsertId();

    // 2. Kiểm tra tồn kho và cập nhật từng sản phẩm
    foreach ($chiTietGio as $item) {
        $spObj = $item['sanPham'];
        $id = $spObj->id;
        $soLuongMua = $item['soLuong'];

        // Khóa dòng sản phẩm để kiểm tra tồn kho chính xác (SELECT ... FOR UPDATE)
        $stmtCheck = $pdo->prepare("SELECT ton_kho, gia FROM san_pham WHERE id = ? FOR UPDATE");
        $stmtCheck->execute([$id]);
        $spDb = $stmtCheck->fetch();

        if (!$spDb || $spDb['ton_kho'] < $soLuongMua) {
            // Hết hàng hoặc không đủ tồn kho -> Hủy giao dịch (Rollback)
            $pdo->rollBack();
            $_SESSION['flash_loi'] = "Sản phẩm '{$spObj->ten}' không đủ số lượng tồn kho hoặc đã hết hàng!";
            header('Location: gio-hang.php');
            exit;
        }

        // Trừ tồn kho
        $stmtUp = $pdo->prepare("UPDATE san_pham SET ton_kho = ton_kho - ? WHERE id = ?");
        $stmtUp->execute([$soLuongMua, $id]);

        // Lưu chi tiết đơn (Kèm đơn giá lúc đặt - snapshot giá vào bảng chi_tiet_don)
        $stmtCt = $pdo->prepare("INSERT INTO chi_tiet_don (don_hang_id, san_pham_id, so_luong, don_gia) VALUES (?, ?, ?, ?)");
        $stmtCt->execute([$donHangId, $id, $soLuongMua, $spDb['gia']]);
    }

    // Xác nhận giao dịch thành công (Commit)
    $pdo->commit();

    // Xóa trống giỏ hàng sau khi đặt thành công
    $gioService->lamTrong();

    // Chuyển hướng đến trang xem chi tiết đơn hàng vừa đặt
    header("Location: don-hang.php?id={$donHangId}&dat_hang=thanh_cong");
    exit;

} catch (\Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Hiển thị trực tiếp lỗi ra màn hình để debug nếu có sự cố khác
    echo "<div style='max-width: 600px; margin: 50px auto; background: #fee2e2; color: #991b1b; padding: 20px; border: 1px solid #f87171; border-radius: 8px; font-family: sans-serif;'>";
    echo "<h3 style='margin-top:0; color: #b91c1c;'>Lỗi xử lý đặt hàng (Debug):</h3>";
    echo "<p><strong>Chi tiết:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><small><strong>File:</strong> " . $e->getFile() . " - <strong>Dòng:</strong> " . $e->getLine() . "</small></p>";
    echo "<br><a href='gio-hang.php' style='display: inline-block; background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;'>Quay lại giỏ hàng</a>";
    echo "</div>";
    exit;
}