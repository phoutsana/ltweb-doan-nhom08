
<?php

use App\Data\KhoSanPham;
use App\Services\GioHang;

require_once __DIR__ . '/inc/config.php';

// Khởi tạo giỏ hàng và kho sản phẩm.
$gioHang = new GioHang();
$khoSanPham = new KhoSanPham();

// Lấy danh sách sản phẩm từ file JSON.
$danhSachSanPham = $khoSanPham->tatCa();

// Tạo danh sách sản phẩm theo ID.
$sanPhamTheoId = [];

foreach ($danhSachSanPham as $sanPham) {
    $sanPhamTheoId[(string) $sanPham->getId()] = $sanPham;
}

// Lấy dữ liệu giỏ hàng.
$cacMonHang = $gioHang->tatCa();

$tongTien = 0.0;
$tongSoLuong = 0;
$danhSachThanhToan = [];

// Tính tổng tiền dựa trên giá trong dữ liệu sản phẩm.
foreach ($cacMonHang as $id => $soLuong) {
    $id = (string) $id;
    $soLuong = (int) $soLuong;

    // Bỏ qua dữ liệu giỏ hàng không hợp lệ.
    if (
        $soLuong <= 0
        || $soLuong > 99
        || !isset($sanPhamTheoId[$id])
    ) {
        continue;
    }

    $sanPham = $sanPhamTheoId[$id];
    $gia = $sanPham->getGia();
    $thanhTien = $gia * $soLuong;

    $tongTien += $thanhTien;
    $tongSoLuong += $soLuong;

    $danhSachThanhToan[] = [
        'san_pham' => $sanPham,
        'so_luong' => $soLuong,
        'thanh_tien' => $thanhTien,
    ];
}

// Xử lý nút quay lại giỏ hàng theo phương thức POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Location: gio-hang.php', true, 303);
    exit;
}

require_once __DIR__ . '/inc/header.php';
?>

<main class="container py-5">
    <h1 class="mb-4">Xác nhận thanh toán</h1>

    <?php if (empty($danhSachThanhToan)): ?>

        <div class="alert alert-info">
            Giỏ hàng của bạn đang trống.
            <a href="danh-sach.php">Tiếp tục mua sắm</a>.
        </div>

        <a href="gio-hang.php" class="btn btn-secondary">
            Quay lại giỏ hàng
        </a>

    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($danhSachThanhToan as $monHang): ?>
                        <?php
                        $sanPham = $monHang['san_pham'];
                        $soLuong = $monHang['so_luong'];
                        $thanhTien = $monHang['thanh_tien'];
                        ?>

                        <tr>
                            <td>
                                <?= e($sanPham->getTen()) ?>
                            </td>

                            <td class="text-end">
                                <?= e(vnd($sanPham->getGia())) ?>
                            </td>

                            <td class="text-center">
                                <?= e($soLuong) ?>
                            </td>

                            <td class="text-end">
                                <?= e(vnd($thanhTien)) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="2">Tổng số lượng</th>
                        <td class="text-center">
                            <?= e($tongSoLuong) ?>
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <th colspan="3" class="text-end">
                            Tổng thanh toán
                        </th>

                        <th class="text-end">
                            <?= e(vnd($tongTien)) ?>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="alert alert-warning">
            Đây là trang xem lại đơn hàng.
            Chức năng lưu đơn hàng và thanh toán thực tế
            chưa được triển khai trong đoạn code này.
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="gio-hang.php" class="btn btn-secondary">
                Quay lại giỏ hàng
            </a>

            <a href="danh-sach.php" class="btn btn-outline-primary">
                Tiếp tục mua sắm
            </a>
        </div>

    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
