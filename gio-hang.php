<?php
declare(strict_types=1);

use App\Services\GioHang;

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

$gioHang = new GioHang();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    // Kiem tra CSRF truoc tat ca thao tac thay doi gio hang.
    // Ham csrf_verify() se tra 403 va dung neu token thieu hoac sai.
    csrf_verify();

    $hanhDong = $_POST['hanh_dong'] ?? null;

    try {
        if (!is_string($hanhDong)) {
            throw new InvalidArgumentException('Thao tác giỏ hàng không hợp lệ.');
        }

        if ($hanhDong === 'xoa_tat_ca') {
            $gioHang->lamTrong();
        } elseif (in_array($hanhDong, ['them', 'xoa', 'cap_nhat'], true)) {
            $id = filter_var(
                $_POST['id'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if ($id === false || $id === null) {
                throw new InvalidArgumentException('Mã sản phẩm không hợp lệ.');
            }

            // Giu kieu chuoi cua ID nhu trong file goc de tuong thich GioHang.
            $maSanPham = (string) $id;

            if ($hanhDong === 'them') {
                $gioHang->them($maSanPham);
            } elseif ($hanhDong === 'xoa') {
                $gioHang->xoa($maSanPham);
            } else {
                $soLuongMoi = filter_var(
                    $_POST['so_luong'] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 0]]
                );

                if ($soLuongMoi === false || $soLuongMoi === null) {
                    throw new InvalidArgumentException('Số lượng không hợp lệ.');
                }

                if ($soLuongMoi > 0) {
                    $gioHang->capNhat($maSanPham, $soLuongMoi);
                } else {
                    $gioHang->xoa($maSanPham);
                }
            }
        } else {
            throw new InvalidArgumentException('Thao tác giỏ hàng không được hỗ trợ.');
        }
    } catch (InvalidArgumentException $e) {
        $_SESSION['flash_loi'] = $e->getMessage();
    }

    // POST -> Redirect -> GET: tranh lap lai thao tac khi tai trang.
    header('Location: gio-hang.php', true, 303);
    exit;
}

$chiTietGio = $gioHang->chiTiet();
$tbLoi = $_SESSION['flash_loi'] ?? '';
unset($_SESSION['flash_loi']);

$tieuDe = 'Giỏ hàng của bạn - Fashion Store';
$trang = 'gio-hang';
$cssRieng = 'css/gio-hang.css';

require_once __DIR__ . '/inc/header.php';

?>

<main class="noi-dung-chinh gio-hang-page">
    <section class="gio-hang-section">
        <h2 class="gio-hang-heading">Giỏ hàng / Yêu thích của bạn</h2>

        <?php if (is_string($tbLoi) && $tbLoi !== ''): ?>
            <div class="gio-hang-error" role="alert">
                <strong>Lỗi:</strong> <?= e($tbLoi) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($chiTietGio)): ?>
            <div class="gio-hang-empty">
                <p>Bạn chưa chọn sản phẩm nào!</p>
                <a href="danh-sach.php" class="nut-bam nut-bam--chinh gio-hang-shop-link">Quay lại mua sắm</a>
            </div>
        <?php else: ?>
            <div class="gio-hang-table-wrap">
                <table class="bang-san-pham gio-hang-table">
                    <thead>
                        <tr>
                            <th scope="col">Hình ảnh</th>
                            <th scope="col">Tên sản phẩm</th>
                            <th scope="col">Đơn giá</th>
                            <th scope="col" class="gio-hang-center">Số lượng</th>
                            <th scope="col">Thành tiền</th>
                            <th scope="col" class="gio-hang-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chiTietGio as $item): ?>
                            <tr>
                                <td>
                                    <img
                                        class="gio-hang-product-img"
                                        src="<?= e((string) $item['sanPham']->hinhAnh) ?>"
                                        alt="<?= e((string) $item['sanPham']->ten) ?>"
                                        width="70"
                                        height="70"
                                    >
                                </td>
                                <td class="gio-hang-product-name">
                                    <a href="chi-tiet.php?id=<?= e((string) $item['sanPham']->id) ?>">
                                        <?= e((string) $item['sanPham']->ten) ?>
                                    </a>
                                </td>
                                <td class="gio-hang-unit-price"><?= vnd($item['sanPham']->gia) ?></td>
                                <td class="gio-hang-center">
                                    <form method="post" action="gio-hang.php" class="gio-hang-form-qty">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $item['sanPham']->id) ?>">
                                        <label class="gio-hang-sr-only" for="so-luong-<?= e((string) $item['sanPham']->id) ?>">Số lượng <?= e((string) $item['sanPham']->ten) ?></label>
                                        <input
                                            id="so-luong-<?= e((string) $item['sanPham']->id) ?>"
                                            type="number"
                                            name="so_luong"
                                            value="<?= e((string) $item['soLuong']) ?>"
                                            min="0"
                                            step="1"
                                            required
                                        >
                                        <button type="submit" name="hanh_dong" value="cap_nhat" class="gio-hang-btn-update">Sửa</button>
                                    </form>
                                </td>
                                <td class="gio-hang-total-price"><?= vnd($item['thanhTien']) ?></td>
                                <td class="gio-hang-center">
                                    <form method="post" action="gio-hang.php" class="gio-hang-form-delete">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $item['sanPham']->id) ?>">
                                        <button type="submit" name="hanh_dong" value="xoa" class="nut-bam nut-bam--phu gio-hang-btn-delete">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="gio-hang-summary-label">Tổng cộng:</td>
                            <td colspan="2" class="gio-hang-summary-value"><?= vnd($gioHang->tongTien()) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="gio-hang-actions">
                <form method="post" action="gio-hang.php" class="gio-hang-form-clear">
                    <?= csrf_field() ?>
                    <button type="submit" name="hanh_dong" value="xoa_tat_ca" class="nut-bam nut-bam--phu gio-hang-btn-clear">Xóa toàn bộ giỏ hàng</button>
                </form>
                <div class="gio-hang-actions-right">
                    <a href="danh-sach.php" class="nut-bam nut-bam--phu gio-hang-btn-shop">Tiếp tục mua sắm</a>
                    <form action="dat-hang.php" method="post" class="gio-hang-form-checkout">
                        <?= csrf_field() ?>
                        <button type="submit" class="nut-bam nut-bam--chinh gio-hang-btn-checkout">Tiến hành thanh toán</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

<script type="module" src="js/main.js"></script>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
