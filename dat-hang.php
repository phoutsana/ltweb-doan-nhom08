<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';

// Bắt buộc phải đăng nhập và gửi yêu cầu bằng POST
if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dang-nhap.php');
    exit;
}

$gioHang = $_SESSION['gio'] ?? [];
if (empty($gioHang)) {
    header('Location: gio-hang.php');
    exit;
}

$pdo = \db();

try {
    // Bắt đầu giao dịch (Transaction)
    $pdo->beginTransaction();

    $userId = $_SESSION['user']['id'];
    $tongTien = 0;
    $chiTietHopLe = [];

    // Duyệt qua giỏ hàng để khóa dòng và kiểm tra tồn kho
    foreach ($gioHang as $idSp => $item) {
        $id = (int)$idSp;
        $soLuongMua = is_array($item) ? (int)($item['so_luong'] ?? 1) : (int)$item;

        if ($id <= 0 || $soLuongMua <= 0) continue;

        // Khóa dòng sản phẩm để kiểm tra tồn kho an toàn tuyệt đối (SELECT ... FOR UPDATE)
        $stmtCheck = $pdo->prepare("SELECT id, ten, gia, ton_kho FROM san_pham WHERE id = ? FOR UPDATE");
        $stmtCheck->execute([$id]);
        $spDb = $stmtCheck->fetch();

        // NẾU KHÔNG ĐỦ TỒN KHO HOẶC HẾT HÀNG -> Hủy giao dịch (Rollback) và thông báo giữ nguyên giỏ
        if (!$spDb || (int)$spDb['ton_kho'] < $soLuongMua) {
            $pdo->rollBack();
            
            $tenSp = $spDb ? $spDb['ten'] : "Sản phẩm #{$id}";
            $tonKhoCon = $spDb ? $spDb['ton_kho'] : 0;

            echo "<div style='max-width: 650px; margin: 50px auto; background: #fee2e2; color: #991b1b; padding: 25px; border: 1px solid #f87171; border-radius: 8px; font-family: sans-serif; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>";
            echo "<h3 style='margin-top:0; color: #b91c1c;'>❌ Đặt hàng không thành công!</h3>";
            echo "<p>Sản phẩm '<strong>" . htmlspecialchars($tenSp) . "</strong>' không đủ số lượng trong kho.</p>";
            echo "<p>Số lượng tồn kho hiện tại: <strong>{$tonKhoCon}</strong> | Số lượng bạn muốn mua: <strong>{$soLuongMua}</strong></p>";
            echo "<hr style='border:0; border-top:1px solid #fca5a5; margin: 15px 0;'>";
            echo "<p><em>Giỏ hàng của bạn vẫn được giữ nguyên để bạn có thể điều chỉnh lại số lượng.</em></p>";
            echo "<br><a href='gio-hang.php' style='display: inline-block; background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;'>Quay lại giỏ hàng</a>";
            echo "</div>";
            exit;
        }

        $thanhTien = (float)$spDb['gia'] * $soLuongMua;
        $tongTien += $thanhTien;

        $chiTietHopLe[] = [
            'id' => $id,
            'so_luong' => $soLuongMua,
            'gia' => (float)$spDb['gia']
        ];
    }

    if (empty($chiTietHopLe)) {
        $pdo->rollBack();
        header('Location: gio-hang.php');
        exit;
    }

    // 1. Tạo đơn hàng mới trong bảng don_hang (Không dùng cột ngay_tao để tránh lỗi SQL)
    $stmtDh = $pdo->prepare("INSERT INTO don_hang (nguoi_dung_id, tong_tien, trang_thai) VALUES (?, ?, 'dang_xu_ly')");
    $stmtDh->execute([$userId, $tongTien]);
    $donHangId = $pdo->lastInsertId();

    // 2. Trừ tồn kho và lưu chi tiết đơn hàng
    foreach ($chiTietHopLe as $ct) {
        // Trừ tồn kho sản phẩm
        $stmtUp = $pdo->prepare("UPDATE san_pham SET ton_kho = ton_kho - ? WHERE id = ?");
        $stmtUp->execute([$ct['so_luong'], $ct['id']]);

        // Lưu chi tiết đơn hàng
        $stmtCt = $pdo->prepare("INSERT INTO chi_tiet_don (don_hang_id, san_pham_id, so_luong, don_gia) VALUES (?, ?, ?, ?)");
        $stmtCt->execute([$donHangId, $ct['id'], $ct['so_luong'], $ct['gia']]);
    }

    // Xác nhận giao dịch thành công (Commit)
    $pdo->commit();

    // Xóa sạch giỏ hàng sau khi đặt thành công
    unset($_SESSION['gio']);

    // Chuyển hướng đến trang lịch sử đơn hàng với thông báo thành công
    header("Location: don-hang.php?id={$donHangId}&dat_hang=thanh_cong");
    exit;

} catch (\Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo "<div style='max-width: 600px; margin: 50px auto; background: #fee2e2; color: #991b1b; padding: 20px; border: 1px solid #f87171; border-radius: 8px; font-family: sans-serif;'>";
    echo "<h3 style='margin-top:0; color: #b91c1c;'>Lỗi hệ thống khi đặt hàng:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<br><a href='gio-hang.php' style='display: inline-block; background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;'>Quay lại giỏ hàng</a>";
    echo "</div>";
    exit;
}