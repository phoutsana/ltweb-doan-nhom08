<?php

declare(strict_types=1);
require_once __DIR__ . '/inc/config.php';

// Bắt buộc phải đăng nhập mới được xem đơn hàng
if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap.php');
    exit;
}

$currentUser = $_SESSION['user'];
$pdo = \db();
$loi = '';
$thanhCong = '';

// Bắt thông báo thành công khi vừa đặt hàng xong từ dat-hang.php
if (isset($_GET['dat_hang']) && $_GET['dat_hang'] === 'thanh_cong') {
    $thanhCong = 'Đặt hàng thành công! Đơn hàng của bạn đã được ghi nhận và đang được xử lý.';
}

// Xử lý đổi trạng thái đơn hàng (Dành cho Quản trị viên)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['don_hang_id'], $_POST['trang_thai'])) {
    if ($currentUser['vai_tro'] === 'quan_tri') {
        $dhId = (int)$_POST['don_hang_id'];
        $trangThaiMoi = trim($_POST['trang_thai']);

        $trangThaiChoPhieu = ['dang_xu_ly', 'dang_giao', 'hoan_thanh', 'da_huy'];

        if (in_array($trangThaiMoi, $trangThaiChoPhieu, true)) {
            try {
                $stmtUp = $pdo->prepare("UPDATE don_hang SET trang_thai = ? WHERE id = ?");
                $stmtUp->execute([$trangThaiMoi, $dhId]);
                $thanhCong = 'Cập nhật trạng thái đơn hàng thành công!';
            } catch (\Exception $e) {
                error_log("Lỗi cập nhật trạng thái đơn hàng: " . $e->getMessage());
                $loi = 'Không thể cập nhật trạng thái đơn hàng.';
            }
        } else {
            $loi = 'Trạng thái đơn hàng không hợp lệ.';
        }
    } else {
        http_response_code(403);
        $loi = 'Bạn không có quyền thực hiện hành động này.';
    }
}

// Kiểm tra xem có đang xem chi tiết một đơn hàng cụ thể không (?id=...)
$chiTietDonId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$tieuDe = 'Quản lý đơn hàng - Fashion Store';
$trang = 'don-hang';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" style="max-width: 1000px; margin: 30px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: hidden;">
    <?php if ($loi): ?>
        <div style="background-color: #fee2e2; color: #991b1b; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f87171; text-align: center;"><?= e($loi) ?></div>
    <?php endif; ?>

    <?php if ($thanhCong): ?>
        <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #34d399; text-align: center; font-weight: bold; font-size: 1.05rem;">
            <?= e($thanhCong) ?>
        </div>
    <?php endif; ?>

    <?php if ($chiTietDonId > 0): ?>
        <!-- HIỂN THỊ CHI TIẾT MỘT ĐƠN HÀNG -->
        <?php
        try {
            if ($currentUser['vai_tro'] === 'quan_tri') {
                $stmt = $pdo->prepare("SELECT * FROM don_hang WHERE id = ? LIMIT 1");
                $stmt->execute([$chiTietDonId]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM don_hang WHERE id = ? AND nguoi_dung_id = ? LIMIT 1");
                $stmt->execute([$chiTietDonId, $currentUser['id']]);
            }
            $donHang = $stmt->fetch();

            if (!$donHang) {
                http_response_code(404);
                echo "<h2>Không tìm thấy đơn hàng hoặc bạn không có quyền xem đơn hàng này.</h2>";
            } else {
                $stmtCt = $pdo->prepare("SELECT ct.*, sp.ten as ten_san_pham, sp.anh FROM chi_tiet_don ct JOIN san_pham sp ON ct.san_pham_id = sp.id WHERE ct.don_hang_id = ?");
                $stmtCt->execute([$chiTietDonId]);
                $danhSachChiTiet = $stmtCt->fetchAll();
        ?>
                <h2 style="color: #1e3a8a; margin-bottom: 15px;">Chi Tiết Đơn Hàng #<?= $donHang['id'] ?></h2>
                <p><strong>Ngày đặt:</strong> <?= e($donHang['ngay_tao'] ?? $donHang['ngay_dat'] ?? 'N/A') ?></p>
                <p><strong>Trạng thái:</strong> <span style="padding: 4px 8px; background: #e0f2fe; color: #0369a1; border-radius: 4px;"><?= e($donHang['trang_thai']) ?></span></p>
                <p><strong>Tổng tiền:</strong> <span style="color: #dc2626; font-weight: bold;"><?= number_format($donHang['tong_tien'], 0, ',', '.') ?> đ</span></p>

                <h3 style="margin-top: 25px; margin-bottom: 10px;">Danh sách sản phẩm</h3>

                <div style="width: 100%; overflow-x: auto; margin-bottom: 20px;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                        <thead>
                            <tr style="background: #f3f4f6; text-align: left;">
                                <th style="padding: 10px; border: 1px solid #d1d5db;">Sản phẩm</th>
                                <th style="padding: 10px; border: 1px solid #d1d5db;">Đơn giá lúc đặt</th>
                                <th style="padding: 10px; border: 1px solid #d1d5db;">Số lượng</th>
                                <th style="padding: 10px; border: 1px solid #d1d5db;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($danhSachChiTiet as $item): ?>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #d1d5db;">
                                        <?php if (!empty($item['anh'])): ?>
                                            <img src="<?= e($item['anh']) ?>" alt="" style="width: 40px; height: 40px; object-fit: contain; vertical-align: middle; margin-right: 10px; background: #f9fafb; border: 1px solid #e5e7eb; padding: 2px;">
                                        <?php endif; ?>
                                        <?= e($item['ten_san_pham']) ?>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #d1d5db;"><?= number_format($item['don_gia'], 0, ',', '.') ?> đ</td>
                                    <td style="padding: 10px; border: 1px solid #d1d5db;"><?= e($item['so_luong']) ?></td>
                                    <td style="padding: 10px; border: 1px solid #d1d5db; font-weight: bold;"><?= number_format($item['don_gia'] * $item['so_luong'], 0, ',', '.') ?> đ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <a href="don-hang.php" style="display: inline-block; padding: 8px 16px; background: #4b5563; color: white; text-decoration: none; border-radius: 4px;">&larr Quay lại danh sách đơn hàng</a>
        <?php
            }
        } catch (\Exception $e) {
            echo "<p>Lỗi tải chi tiết đơn hàng.</p>";
        }
        ?>

    <?php else: ?>
        <!-- HIỂN THỊ DANH SÁCH ĐƠN HÀNG (Sắp xếp DESC đưa đơn mới nhất lên đầu) -->
        <h2 style="color: #1e3a8a; margin-bottom: 20px;"><?= $currentUser['vai_tro'] === 'quan_tri' ? 'Quản Lý Tất Cả Đơn Hàng (Admin)' : 'Đơn Hàng Của Tôi' ?></h2>

        <?php
        try {
            if ($currentUser['vai_tro'] === 'quan_tri') {
                $stmt = $pdo->query("SELECT dh.*, nd.ho_ten, nd.email FROM don_hang dh JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id ORDER BY dh.id DESC");
            } else {
                $stmt = $pdo->prepare("SELECT * FROM don_hang WHERE nguoi_dung_id = ? ORDER BY id DESC");
                $stmt->execute([$currentUser['id']]);
            }
            $danhSachDon = $stmt->fetchAll();

            if (empty($danhSachDon)) {
                echo "<p style='text-align: center; color: #6b7280; padding: 20px;'>Chưa có đơn hàng nào.</p>";
            } else {
        ?>
                <div style="width: 100%; overflow-x: auto; margin-bottom: 20px;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 650px;">
                        <thead>
                            <tr style="background: #f3f4f6; text-align: left;">
                                <th style="padding: 10px; border: 1px solid #d1d5db;">ID</th>
                                <?php if ($currentUser['vai_tro'] === 'quan_tri'): ?><th style="padding: 10px; border: 1px solid #d1d5db;">Khách hàng</th><?php endif; ?>
                                <th style="padding: 10px; border: 1px solid #d1d5db;">Ngày đặt</th>
                                <th style="padding: 10px; border: 1px solid #d1d5db;">Tổng tiền</th>
                                <th style="padding: 10px; border: 1px solid #d1d5db;">Trạng thái</th>
                                <th style="padding: 10px; border: 1px solid #d1d5db;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($danhSachDon as $dh): ?>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #d1d5db;">#<?= $dh['id'] ?></td>
                                    <?php if ($currentUser['vai_tro'] === 'quan_tri'): ?>
                                        <td style="padding: 10px; border: 1px solid #d1d5db;"><?= e($dh['ho_ten'] ?? $dh['email']) ?></td>
                                    <?php endif; ?>
                                    <td style="padding: 10px; border: 1px solid #d1d5db;"><?= e($dh['ngay_tao'] ?? $dh['ngay_dat'] ?? 'N/A') ?></td>
                                    <td style="padding: 10px; border: 1px solid #d1d5db; font-weight: bold; color: #dc2626;"><?= number_format($dh['tong_tien'], 0, ',', '.') ?> đ</td>
                                    <td style="padding: 10px; border: 1px solid #d1d5db;">
                                        <?php if ($currentUser['vai_tro'] === 'quan_tri'): ?>
                                            <form method="POST" action="don-hang.php" style="display: flex; gap: 5px;">
                                                <input type="hidden" name="don_hang_id" value="<?= $dh['id'] ?>">
                                                <select name="trang_thai" style="padding: 4px; border: 1px solid #d1d5db; border-radius: 4px;">
                                                    <option value="dang_xu_ly" <?= $dh['trang_thai'] === 'dang_xu_ly' ? 'selected' : '' ?>>Đang xử lý</option>
                                                    <option value="dang_giao" <?= $dh['trang_thai'] === 'dang_giao' ? 'selected' : '' ?>>Đang giao</option>
                                                    <option value="hoan_thanh" <?= $dh['trang_thai'] === 'hoan_thanh' ? 'selected' : '' ?>>Hoàn thành</option>
                                                    <option value="da_huy" <?= $dh['trang_thai'] === 'da_huy' ? 'selected' : '' ?>>Đã hủy</option>
                                                </select>
                                                <button type="submit" style="padding: 4px 8px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">Lưu</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="padding: 4px 8px; background: #e0f2fe; color: #0369a1; border-radius: 4px;"><?= e($dh['trang_thai']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #d1d5db;">
                                        <a href="don-hang.php?id=<?= $dh['id'] ?>" style="color: #2563eb; text-decoration: none; font-weight: bold;">Xem chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
        <?php
            }
        } catch (\Exception $e) {
            echo "<p>Lỗi tải danh sách đơn hàng.</p>";
        }
        ?>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>