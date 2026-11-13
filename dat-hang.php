<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

/*
 * BT7 - ĐẶT HÀNG AN TOÀN
 * - Chỉ cho phép POST
 * - Kiểm tra CSRF
 * - Kiểm tra đăng nhập
 * - Đồng bộ giỏ hàng $_SESSION['gio'] với BT6
 * - Kiểm tra tồn kho và khóa dòng bằng FOR UPDATE
 * - Sử dụng Prepared Statements
 * - Transaction, COMMIT và ROLLBACK
 * - Không hiển thị lỗi SQL cho khách hàng
 */

// 1. Chỉ cho phép phương thức POST.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('405 Method Not Allowed');
}

// 2. Kiểm tra mã CSRF.
csrf_verify();

// 3. Kiểm tra đăng nhập.
if (
    !isset($_SESSION['user']) ||
    !is_array($_SESSION['user'])
) {
    header('Location: dang-nhap.php', true, 303);
    exit;
}

$userId = filter_var(
    $_SESSION['user']['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($userId === false || $userId === null) {
    http_response_code(403);
    exit('403 Forbidden');
}

// 4. Lấy giỏ hàng thống nhất với gio-hang.php.
$gioHang = $_SESSION['gio'] ?? [];

if (!is_array($gioHang) || empty($gioHang)) {
    $_SESSION['gio_hang_loi'] = 'Giỏ hàng đang trống.';
    header('Location: gio-hang.php', true, 303);
    exit;
}

$pdo = null;
$donHangId = null;

try {
    // 5. Bắt đầu giao dịch.
    $pdo = db();
    $pdo->beginTransaction();

    $tongTien = 0.0;
    $chiTietHopLe = [];

    // Chuẩn bị câu SQL khóa dòng sản phẩm.
    $stmtCheck = $pdo->prepare(
        'SELECT id, ten, gia, ton_kho
         FROM san_pham
         WHERE id = ?
         FOR UPDATE'
    );

    // 6. Kiểm tra toàn bộ sản phẩm trong giỏ.
    foreach ($gioHang as $idSp => $item) {
        $id = filter_var(
            $idSp,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($id === false || $id === null) {
            throw new InvalidArgumentException(
                'Mã sản phẩm trong giỏ hàng không hợp lệ.'
            );
        }

        // Hỗ trợ cả hai cấu trúc giỏ hàng BT6.
        $soLuongRaw = is_array($item)
            ? ($item['so_luong'] ?? null)
            : $item;

        $soLuongMua = filter_var(
            $soLuongRaw,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($soLuongMua === false || $soLuongMua === null) {
            throw new InvalidArgumentException(
                'Số lượng sản phẩm không hợp lệ.'
            );
        }

        $stmtCheck->execute([$id]);
        $spDb = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$spDb) {
            throw new DomainException(
                'Một sản phẩm trong giỏ hàng không còn tồn tại.'
            );
        }

        if ((int)$spDb['ton_kho'] < $soLuongMua) {
            throw new DomainException(
                'Sản phẩm "' . $spDb['ten'] .
                '" không đủ tồn kho. Hiện còn ' .
                (int)$spDb['ton_kho'] . ' sản phẩm.'
            );
        }

        // Giá phải lấy từ CSDL, không tin dữ liệu phía client.
        $gia = (float)$spDb['gia'];

        if (!is_finite($gia) || $gia < 0) {
            throw new RuntimeException(
                'Giá sản phẩm không hợp lệ.'
            );
        }

        $tongTien += $gia * $soLuongMua;

        $chiTietHopLe[] = [
            'id' => (int)$spDb['id'],
            'so_luong' => $soLuongMua,
            'don_gia' => $gia
        ];
    }

    if (empty($chiTietHopLe)) {
        throw new InvalidArgumentException(
            'Giỏ hàng không có sản phẩm hợp lệ.'
        );
    }

    // 7. Tạo đơn hàng.
    $stmtDh = $pdo->prepare(
        "INSERT INTO don_hang
         (nguoi_dung_id, tong_tien, trang_thai)
         VALUES (?, ?, 'dang_xu_ly')"
    );

    $stmtDh->execute([$userId, $tongTien]);

    $donHangId = (int)$pdo->lastInsertId();

    if ($donHangId <= 0) {
        throw new RuntimeException(
            'Không thể tạo đơn hàng.'
        );
    }

    // 8. Chuẩn bị câu SQL trừ tồn kho.
    $stmtUp = $pdo->prepare(
        'UPDATE san_pham
         SET ton_kho = ton_kho - ?
         WHERE id = ? AND ton_kho >= ?'
    );

    // 9. Chuẩn bị câu SQL lưu chi tiết đơn hàng.
    $stmtCt = $pdo->prepare(
        'INSERT INTO chi_tiet_don
         (don_hang_id, san_pham_id, so_luong, don_gia)
         VALUES (?, ?, ?, ?)'
    );

    foreach ($chiTietHopLe as $ct) {
        $stmtUp->execute([
            $ct['so_luong'],
            $ct['id'],
            $ct['so_luong']
        ]);

        if ($stmtUp->rowCount() !== 1) {
            throw new RuntimeException(
                'Không thể cập nhật tồn kho sản phẩm.'
            );
        }

        $stmtCt->execute([
            $donHangId,
            $ct['id'],
            $ct['so_luong'],
            $ct['don_gia']
        ]);
    }

    // 10. Hoàn tất giao dịch.
    $pdo->commit();

} catch (Throwable $e) {
    // 11. Rollback nếu xảy ra lỗi.
    if ($pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Ghi lỗi ở phía máy chủ.
    error_log('[BT7_DAT_HANG] ' . $e->getMessage());

    // Chỉ hiển thị lỗi nghiệp vụ an toàn.
    if (
        $e instanceof InvalidArgumentException ||
        $e instanceof DomainException
    ) {
        $_SESSION['gio_hang_loi'] = $e->getMessage();
    } else {
        $_SESSION['gio_hang_loi'] =
            'Đặt hàng thất bại. Vui lòng thử lại.';
    }

    header('Location: gio-hang.php', true, 303);
    exit;
}

// 12. Chỉ xóa giỏ hàng sau khi COMMIT thành công.
unset($_SESSION['gio']);

// 13. Chuyển đến trang chi tiết đơn hàng.
header(
    'Location: don-hang.php?id=' .
    urlencode((string)$donHangId) .
    '&dat_hang=thanh_cong',
    true,
    303
);
exit;
