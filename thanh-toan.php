<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap.php');
    exit;
}

$gioHang = $_SESSION['gio'] ?? [];
if (empty($gioHang)) {
    header('Location: gio-hang.php');
    exit;
}

$pdo = \db();
$tongTien = 0.0;
$tongSoLuong = 0;
$danhSachThanhToan = [];
$loiHetHang = [];

foreach ($gioHang as $idSp => $item) {
    $id = (int)$idSp;
    $soLuong = is_array($item) ? (int)($item['so_luong'] ?? 1) : (int)$item;

    if ($id <= 0 || $soLuong <= 0) continue;

    // Lấy thông tin mới nhất từ CSDL
    $stmt = $pdo->prepare("SELECT id, ten, gia, ton_kho, anh FROM san_pham WHERE id = ?");
    $stmt->execute([$id]);
    $sp = $stmt->fetch();

    if (!$sp) continue;

    $tonKho = (int)$sp['ton_kho'];
    if ($tonKho < $soLuong) {
        $loiHetHang[] = "Sản phẩm '<strong>" . htmlspecialchars($sp['ten']) . "</strong>' chỉ còn lại {$tonKho} trong kho (bạn đang đặt {$soLuong}).";
    }

    $gia = (float)$sp['gia'];
    $thanhTien = $gia * $soLuong;

    $tongTien += $thanhTien;
    $tongSoLuong += $soLuong;

    $danhSachThanhToan[] = [
        'id' => $sp['id'],
        'ten' => $sp['ten'],
        'gia' => $gia,
        'so_luong' => $soLuong,
        'thanh_tien' => $thanhTien,
        'ton_kho' => $tonKho
    ];
}

$tieuDe = 'Xác nhận thanh toán - Fashion Store';
$trang = 'thanh-toan';
require_once __DIR__ . '/inc/header.php';
?>

<main class="container py-5" style="max-width: 1000px; margin: 0 auto;">
    <h1 class="mb-4" style="color: #1e3a8a;">Xác nhận thanh toán đơn hàng</h1>

    <?php if (!empty($loiHetHang)): ?>
        <div style="background-color: #fee2e2; border: 1px solid #f87171; color: #991b1b; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <h4 style="margin-top: 0; color: #b91c1c;">⚠️ Cảnh báo tồn kho:</h4>
            <ul style="margin-bottom: 0; padding-left: 20px;">
                <?php foreach ($loiHetHang as $loi): ?>
                    <li><?= $loi ?></li>
                <?php endforeach; ?>
            </ul>
            <p style="margin-top: 10px; margin-bottom: 0; font-size: 0.95rem;">Bạn vẫn có thể bấm đặt hàng, hệ thống sẽ tự động kiểm tra lại và từ chối giao dịch nếu vượt quá tồn kho thực tế.</p>
        </div>
    <?php endif; ?>

    <?php if (empty($danhSachThanhToan)): ?>
        <div class="alert alert-info">
            Giỏ hàng của bạn đang trống. <a href="danh-sach.php">Tiếp tục mua sắm</a>.
        </div>
        <a href="gio-hang.php" class="btn btn-secondary">Quay lại giỏ hàng</a>
    <?php else: ?>
        <div class="table-responsive" style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <table class="table table-bordered align-middle">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-center">Số lượng mua</th>
                        <th class="text-center">Tồn kho hiện tại</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($danhSachThanhToan as $monHang): ?>
                        <tr>
                            <td><strong><?= e($monHang['ten']) ?></strong></td>
                            <td class="text-end"><?= number_format($monHang['gia'], 0, ',', '.') ?> đ</td>
                            <td class="text-center"><?= e($monHang['so_luong']) ?></td>
                            <td class="text-center">
                                <span style="color: <?= $monHang['ton_kho'] < $monHang['so_luong'] ? '#dc2626' : '#16a34a' ?>; font-weight: bold;">
                                    <?= $monHang['ton_kho'] ?>
                                </span>
                            </td>
                            <td class="text-end" style="color: #dc2626; font-weight: bold;"><?= number_format($monHang['thanh_tien'], 0, ',', '.') ?> đ</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Tổng số lượng</th>
                        <th class="text-center"><?= $tongSoLuong ?></th>
                        <th colspan="2"></th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Tổng thanh toán:</th>
                        <th class="text-end" style="color: #dc2626; font-size: 1.2rem;"><?= number_format($tongTien, 0, ',', '.') ?> đ</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="gio-hang.php" class="btn btn-secondary" style="padding: 10px 20px; font-weight: bold;">⬅️ Quay lại giỏ hàng</a>
            
            <!-- Nút bấm gửi sang dat-hang.php để xử lý transaction -->
            <form action="dat-hang.php" method="POST" style="margin: 0;">
                <button type="submit" class="btn btn-primary" style="background-color: #2563eb; color: white; padding: 10px 25px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                    Xác nhận đặt hàng ngay ➔
                </button>
            </form>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>