<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

// BT7: Quản lý giỏ hàng bằng $_SESSION['gio']
// Đồng bộ với api/gio-hang.php của nhánh main.

if (!isset($_SESSION['gio']) || !is_array($_SESSION['gio'])) {
    $_SESSION['gio'] = [];
}

// Xử lý yêu cầu thay đổi giỏ hàng.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    csrf_verify();

    $hanhDong = $_POST['hanh_dong'] ?? '';
    $id = filter_var(
        $_POST['id'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    try {
        if (!is_string($hanhDong)) {
            throw new InvalidArgumentException('Thao tác không hợp lệ.');
        }

        if (!in_array($hanhDong, ['xoa', 'cap_nhat'], true)) {
            throw new InvalidArgumentException('Thao tác không được hỗ trợ.');
        }

        if ($id === false || $id === null) {
            throw new InvalidArgumentException('Mã sản phẩm không hợp lệ.');
        }

        if ($hanhDong === 'xoa') {
            unset($_SESSION['gio'][$id]);
        }

        if ($hanhDong === 'cap_nhat') {
            $soLuongMoi = filter_var(
                $_POST['so_luong'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if ($soLuongMoi === false || $soLuongMoi === null) {
                throw new InvalidArgumentException('Số lượng phải là số nguyên dương.');
            }

            if (!isset($_SESSION['gio'][$id])) {
                throw new InvalidArgumentException('Sản phẩm không có trong giỏ hàng.');
            }

            $stmt = db()->prepare(
                'SELECT ton_kho FROM san_pham WHERE id = ?'
            );
            $stmt->execute([$id]);
            $sp = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$sp) {
                throw new InvalidArgumentException('Sản phẩm không tồn tại.');
            }

            if ($soLuongMoi > (int)$sp['ton_kho']) {
                throw new InvalidArgumentException('Số lượng vượt quá tồn kho.');
            }

            if (is_array($_SESSION['gio'][$id])) {
                $_SESSION['gio'][$id]['so_luong'] = $soLuongMoi;
            } else {
                $_SESSION['gio'][$id] = $soLuongMoi;
            }
        }
    } catch (InvalidArgumentException $e) {
        $_SESSION['gio_hang_loi'] = $e->getMessage();
    }

    // POST -> Redirect -> GET.
    header('Location: gio-hang.php', true, 303);
    exit;
}

$gioHang = $_SESSION['gio'];
$tongTien = 0.0;

$thongBaoLoi = $_SESSION['gio_hang_loi'] ?? '';
unset($_SESSION['gio_hang_loi']);

$tieuDe = 'Giỏ hàng của bạn - Fashion Store';
$trang = 'gio-hang';
$cssRieng = 'css/gio-hang.css';

require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh gio-hang-page">
    <section class="gio-hang-section">
        <h2 class="gio-hang-heading">
            Giỏ hàng / Yêu thích của bạn
        </h2>

        <?php if (is_string($thongBaoLoi) && $thongBaoLoi !== ''): ?>
            <div class="gio-hang-error" role="alert">
                <strong>Lỗi:</strong>
                <?= e($thongBaoLoi) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($gioHang)): ?>
            <div class="gio-hang-empty">
                <p>Bạn chưa chọn sản phẩm nào!</p>

                <a
                    href="danh-sach.php"
                    class="nut-bam nut-bam--chinh gio-hang-shop-link"
                >
                    Quay lại mua sắm
                </a>
            </div>
        <?php else: ?>
            <div class="gio-hang-table-wrap">
                <table class="bang-san-pham gio-hang-table">
                    <thead>
                        <tr>
                            <th scope="col">Sản phẩm</th>
                            <th scope="col">Đơn giá</th>
                            <th scope="col">Số lượng</th>
                            <th scope="col">Thành tiền</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($gioHang as $idSp => $item): ?>
                            <?php
                            $idSanPham = filter_var(
                                $idSp,
                                FILTER_VALIDATE_INT,
                                ['options' => ['min_range' => 1]]
                            );

                            if ($idSanPham === false || $idSanPham === null) {
                                continue;
                            }

                            if (is_array($item)) {
                                $ten = (string)($item['ten'] ?? 'Sản phẩm');
                                $gia = (float)($item['gia'] ?? 0);
                                $soLuong = (int)($item['so_luong'] ?? 1);
                                $anh = (string)($item['anh'] ?? '');
                            } else {
                                $stmt = db()->prepare(
                                    'SELECT ten, gia, anh FROM san_pham WHERE id = ?'
                                );
                                $stmt->execute([$idSanPham]);
                                $spDb = $stmt->fetch(PDO::FETCH_ASSOC);

                                if (!$spDb) {
                                    continue;
                                }

                                $ten = (string)($spDb['ten'] ?? 'Sản phẩm');
                                $gia = (float)($spDb['gia'] ?? 0);
                                $soLuong = (int)$item;
                                $anh = (string)($spDb['anh'] ?? '');
                            }

                            if ($soLuong <= 0) {
                                continue;
                            }

                            $thanhTien = $gia * $soLuong;
                            $tongTien += $thanhTien;
                            ?>

                            <tr>
                                <td class="gio-hang-product-name">
                                    <?php if ($anh !== ''): ?>
                                        <img
                                            class="gio-hang-product-img"
                                            src="<?= e($anh) ?>"
                                            alt="<?= e($ten) ?>"
                                            width="70"
                                            height="70"
                                        >
                                    <?php endif; ?>

                                    <a href="chi-tiet.php?id=<?= (int)$idSanPham ?>">
                                        <?= e($ten) ?>
                                    </a>
                                </td>

                                <td class="gio-hang-unit-price">
                                    <?= number_format($gia, 0, ',', '.') ?> đ
                                </td>

                                <td class="gio-hang-center">
                                    <form
                                        method="post"
                                        action="gio-hang.php"
                                        class="gio-hang-form-qty"
                                    >
                                        <?= csrf_field() ?>

                                        <input
                                            type="hidden"
                                            name="hanh_dong"
                                            value="cap_nhat"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int)$idSanPham ?>"
                                        >

                                        <label
                                            class="gio-hang-sr-only"
                                            for="so-luong-<?= (int)$idSanPham ?>"
                                        >
                                            Số lượng <?= e($ten) ?>
                                        </label>

                                        <input
                                            id="so-luong-<?= (int)$idSanPham ?>"
                                            type="number"
                                            name="so_luong"
                                            value="<?= (int)$soLuong ?>"
                                            min="1"
                                            step="1"
                                            required
                                        >

                                        <button
                                            type="submit"
                                            class="gio-hang-btn-update"
                                        >
                                            Cập nhật
                                        </button>
                                    </form>
                                </td>

                                <td class="gio-hang-total-price">
                                    <?= number_format($thanhTien, 0, ',', '.') ?> đ
                                </td>

                                <td class="gio-hang-center">
                                    <form
                                        method="post"
                                        action="gio-hang.php"
                                        class="gio-hang-form-delete"
                                    >
                                        <?= csrf_field() ?>

                                        <input
                                            type="hidden"
                                            name="hanh_dong"
                                            value="xoa"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int)$idSanPham ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="nut-bam nut-bam--phu gio-hang-btn-delete"
                                        >
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="3" class="gio-hang-summary-label">
                                Tổng tiền thanh toán:
                            </td>

                            <td colspan="2" class="gio-hang-summary-value">
                                <?= number_format($tongTien, 0, ',', '.') ?> đ
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="gio-hang-actions">
                <div class="gio-hang-actions-right">
                    <a
                        href="danh-sach.php"
                        class="nut-bam nut-bam--phu gio-hang-btn-shop"
                    >
                        Tiếp tục mua sắm
                    </a>

                    <form
                        action="dat-hang.php"
                        method="post"
                        class="gio-hang-form-checkout"
                    >
                        <?= csrf_field() ?>

                        <button
                            type="submit"
                            class="nut-bam nut-bam--chinh gio-hang-btn-checkout"
                        >
                            Tiến hành đặt hàng
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
