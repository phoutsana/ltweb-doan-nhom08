<?php
declare(strict_types=1);

use App\Services\GioHang;

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

/*
 * BT7 - BẢO MẬT CHỨC NĂNG ĐẶT HÀNG
 * - Chỉ chấp nhận phương thức POST
 * - Bắt buộc xác minh CSRF token
 * - Kiểm tra đăng nhập
 * - Kiểm tra giỏ hàng và tồn kho
 * - Sử dụng Prepared Statement
 * - Sử dụng Transaction và Rollback
 * - Không tiết lộ lỗi nội bộ
 */

// 1. Từ chối GET, HEAD và các phương thức khác
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('405 Method Not Allowed');
}

// 2. Kiểm tra CSRF trước mọi thao tác thay đổi dữ liệu
// Thiếu hoặc sai token phải bị từ chối với HTTP 403
csrf_verify();

// 3. Kiểm tra đăng nhập
if (
    !isset($_SESSION['user']) ||
    !is_array($_SESSION['user']) ||
    !isset($_SESSION['user']['id'])
) {
    header('Location: dang-nhap.php');
    exit;
}

$userId = filter_var(
    $_SESSION['user']['id'],
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($userId === false) {
    http_response_code(403);
    exit('403 Forbidden');
}

// 4. Kiểm tra giỏ hàng
$gioService = new GioHang();
$chiTietGio = $gioService->chiTiet();

if (empty($chiTietGio)) {
    $_SESSION['flash_loi'] = 'Giỏ hàng đang trống.';
    header('Location: gio-hang.php');
    exit;
}

$pdo = null;

try {
    // 5. Kết nối và bắt đầu giao dịch
    $pdo = db();
    $pdo->beginTransaction();

    $tongTien = $gioService->tongTien();

    // 6. Kiểm tra sản phẩm và khóa dòng tồn kho
    $sanPhamDatHang = [];

    $stmtCheck = $pdo->prepare(
        'SELECT ton_kho, gia
         FROM san_pham
         WHERE id = ?
         FOR UPDATE'
    );

    foreach ($chiTietGio as $item) {
        $spObj = $item['sanPham'];

        $id = filter_var(
            $spObj->id,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $soLuongMua = filter_var(
            $item['soLuong'],
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($id === false || $soLuongMua === false) {
            throw new InvalidArgumentException(
                'Dữ liệu giỏ hàng không hợp lệ.'
            );
        }

        $stmtCheck->execute([$id]);
        $spDb = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (
            !$spDb ||
            (int) $spDb['ton_kho'] < $soLuongMua
        ) {
        throw new DomainException(
                "Sản phẩm '{$spObj->ten}' không đủ tồn kho."
            );
        }

        $sanPhamDatHang[] = [
            'id' => $id,
            'so_luong' => $soLuongMua,
            'don_gia' => $spDb['gia']
        ];
    }

    // 7. Lưu đơn hàng bằng Prepared Statement
    $stmtDh = $pdo->prepare(
        "INSERT INTO don_hang
         (nguoi_dung_id, tong_tien, trang_thai)
         VALUES (?, ?, 'dang_xu_ly')"
    );

    $stmtDh->execute([$userId, $tongTien]);

    $donHangId = $pdo->lastInsertId();

    // 8. Chuẩn bị truy vấn trừ tồn kho
    $stmtUp = $pdo->prepare(
        'UPDATE san_pham
         SET ton_kho = ton_kho - ?
         WHERE id = ? AND ton_kho >= ?'
    );

    // 9. Chuẩn bị truy vấn lưu chi tiết đơn
    $stmtCt = $pdo->prepare(
        'INSERT INTO chi_tiet_don
         (don_hang_id, san_pham_id, so_luong, don_gia)
         VALUES (?, ?, ?, ?)'
    );

    foreach ($sanPhamDatHang as $sp) {
        // Trừ tồn kho có điều kiện
        $stmtUp->execute([
            $sp['so_luong'],
            $sp['id'],
            $sp['so_luong']
        ]);

        if ($stmtUp->rowCount() !== 1) {
            throw new RuntimeException(
                'Không thể cập nhật tồn kho sản phẩm.'
            );
        }

        // Lưu chi tiết đơn hàng
        $stmtCt->execute([
            $donHangId,
            $sp['id'],
            $sp['so_luong'],
            $sp['don_gia']
        ]);
    }

    // 10. Xác nhận giao dịch
    $pdo->commit();

} catch (Throwable $e) {
    // 11. Hoàn tác nếu giao dịch thất bại
    if ($pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Ghi lỗi nội bộ, không hiển thị SQL và đường dẫn file
    error_log('[BT7_DAT_HANG] ' . $e->getMessage());

    if ($e instanceof InvalidArgumentException ||
       $e instanceof DomainException) {
        $_SESSION['flash_loi'] = $e->getMessage();
    } else {
        $_SESSION['flash_loi'] =
            'Đặt hàng thất bại. Vui lòng thử lại.';
    }

    header('Location: gio-hang.php');
    exit;
}

// 12. Xóa giỏ hàng sau khi COMMIT thành công
$gioService->lamTrong();

// 13. Chuyển đến trang chi tiết đơn hàng
header(
    'Location: don-hang.php?id=' .
    urlencode((string) $donHangId) .
    '&dat_hang=thanh_cong'
);
exit;
