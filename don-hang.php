<?php
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

// Bắt buộc phải đăng nhập mới được xem đơn hàng
if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap.php');
    exit;
}

$currentUser = $_SESSION['user'];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
}
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

        // Danh sách trạng thái cho phép theo thiết kế
        $trangThaiChoPhieu = ['dang_xu_ly', 'dang_giao', 'hoan_thanh', 'da_huy'];

        if (in_array($trangThaiMoi, $trangThaiChoPhieu)) {
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
$cssRieng = 'css/don-hang-bt7-csp.css';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" data-bt7-dh="dh001">
    <?php if ($loi): ?>
        <div data-bt7-dh="dh002"><?= e($loi) ?></div>
    <?php endif; ?>

    <?php if ($thanhCong): ?>
        <div data-bt7-dh="dh003">
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
                // Lấy chi tiết sản phẩm trong đơn
                $stmtCt = $pdo->prepare("SELECT ct.*, sp.ten as ten_san_pham, sp.anh FROM chi_tiet_don ct JOIN san_pham sp ON ct.san_pham_id = sp.id WHERE ct.don_hang_id = ?");
                $stmtCt->execute([$chiTietDonId]);
                $danhSachChiTiet = $stmtCt->fetchAll();
        ?>
                <h2 data-bt7-dh="dh004">Chi Tiết Đơn Hàng #<?= $donHang['id'] ?></h2>
                <p><strong>Ngày đặt:</strong> <?= e($donHang['ngay_tao'] ?? $donHang['ngay_dat'] ?? 'N/A') ?></p>
                <p><strong>Trạng thái:</strong> <span data-bt7-dh="dh005"><?= e($donHang['trang_thai']) ?></span></p>
                <p><strong>Tổng tiền:</strong> <span data-bt7-dh="dh006"><?= number_format($donHang['tong_tien'], 0, ',', '.') ?> đ</span></p>

                <h3 data-bt7-dh="dh007">Danh sách sản phẩm</h3>
                
                <!-- Bọc bảng chi tiết bằng div cuộn ngang chống vỡ khung -->
                <div data-bt7-dh="dh008">
                    <table data-bt7-dh="dh009">
                        <thead>
                            <tr data-bt7-dh="dh010">
                                <th data-bt7-dh="dh011">Sản phẩm</th>
                                <th data-bt7-dh="dh012">Đơn giá lúc đặt</th>
                                <th data-bt7-dh="dh013">Số lượng</th>
                                <th data-bt7-dh="dh014">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($danhSachChiTiet as $item): ?>
                                <tr>
                                    <td data-bt7-dh="dh015">
                                        <img src="<?= e($item['anh']) ?>" alt="" data-bt7-dh="dh016">
                                        <?= e($item['ten_san_pham']) ?>
                                    </td>
                                    <td data-bt7-dh="dh017"><?= number_format($item['don_gia'], 0, ',', '.') ?> đ</td>
                                    <td data-bt7-dh="dh018"><?= e($item['so_luong']) ?></td>
                                    <td data-bt7-dh="dh019"><?= number_format($item['don_gia'] * $item['so_luong'], 0, ',', '.') ?> đ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <a href="don-hang.php" data-bt7-dh="dh020">&larr Quay lại danh sách đơn hàng</a>
        <?php
            }
        } catch (\Exception $e) {
            echo "<p>Lỗi tải chi tiết đơn hàng.</p>";
        }
        ?>

    <?php else: ?>
        <!-- HIỂN THỊ DANH SÁCH ĐƠN HÀNG -->
        <h2 data-bt7-dh="dh021"><?= $currentUser['vai_tro'] === 'quan_tri' ? 'Quản Lý Tất Cả Đơn Hàng (Admin)' : 'Đơn Hàng Của Tôi' ?></h2>

        <?php
        try {
            if ($currentUser['vai_tro'] === 'quan_tri') {
                $stmt = $pdo->query("SELECT dh.*, nd.ho_ten, nd.email FROM don_hang dh JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id ORDER BY dh.id ASC");
            } else {
                $stmt = $pdo->prepare("SELECT * FROM don_hang WHERE nguoi_dung_id = ? ORDER BY id ASC");
                $stmt->execute([$currentUser['id']]);
            }
            $danhSachDon = $stmt->fetchAll();

            if (empty($danhSachDon)) {
                echo '<p class="don-hang-trong">Chưa có đơn hàng nào.</p>';
            } else {
        ?>
                <!-- Bọc bảng danh sách đơn hàng bằng div cuộn ngang chống vỡ khung -->
                <div data-bt7-dh="dh022">
                    <table data-bt7-dh="dh023">
                        <thead>
                            <tr data-bt7-dh="dh024">
                                <th data-bt7-dh="dh025">ID</th>
                                <?php if ($currentUser['vai_tro'] === 'quan_tri'): ?><th data-bt7-dh="dh026">Khách hàng</th><?php endif; ?>
                                <th data-bt7-dh="dh027">Ngày đặt</th>
                                <th data-bt7-dh="dh028">Tổng tiền</th>
                                <th data-bt7-dh="dh029">Trạng thái</th>
                                <th data-bt7-dh="dh030">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($danhSachDon as $dh): ?>
                                <tr>
                                    <td data-bt7-dh="dh031">#<?= $dh['id'] ?></td>
                                    <?php if ($currentUser['vai_tro'] === 'quan_tri'): ?>
                                        <td data-bt7-dh="dh032"><?= e($dh['ho_ten'] ?? $dh['email']) ?></td>
                                    <?php endif; ?>
                                    <td data-bt7-dh="dh033"><?= e($dh['ngay_tao'] ?? $dh['ngay_dat'] ?? 'N/A') ?></td>
                                    <td data-bt7-dh="dh034"><?= number_format($dh['tong_tien'], 0, ',', '.') ?> đ</td>
                                    <td data-bt7-dh="dh035">
                                        <?php if ($currentUser['vai_tro'] === 'quan_tri'): ?>
                                            <form method="POST" action="don-hang.php" data-bt7-dh="dh036">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="don_hang_id" value="<?= $dh['id'] ?>">
                                                <select name="trang_thai" data-bt7-dh="dh037">
                                                    <option value="dang_xu_ly" <?= $dh['trang_thai'] === 'dang_xu_ly' ? 'selected' : '' ?>>Đang xử lý</option>
                                                    <option value="dang_giao" <?= $dh['trang_thai'] === 'dang_giao' ? 'selected' : '' ?>>Đang giao</option>
                                                    <option value="hoan_thanh" <?= $dh['trang_thai'] === 'hoan_thanh' ? 'selected' : '' ?>>Hoàn thành</option>
                                                    <option value="da_huy" <?= $dh['trang_thai'] === 'da_huy' ? 'selected' : '' ?>>Đã hủy</option>
                                                </select>
                                                <button type="submit" data-bt7-dh="dh038">Lưu</button>
                                            </form>
                                        <?php else: ?>
                                            <span data-bt7-dh="dh039"><?= e($dh['trang_thai']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-bt7-dh="dh040">
                                        <a href="don-hang.php?id=<?= $dh['id'] ?>" data-bt7-dh="dh041">Xem chi tiết</a>
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