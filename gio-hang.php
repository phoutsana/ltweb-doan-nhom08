<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/config.php';

// Xử lý các hành động cập nhật giỏ hàng trực tiếp trên trang giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hanhDong = $_POST['hanh_dong'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($hanhDong === 'xoa' && $id > 0) {
        unset($_SESSION['gio'][$id]);
    } elseif ($hanhDong === 'cap_nhat' && $id > 0) {
        $soLuongMoi = (int)($_POST['so_luong'] ?? 1);
        if ($soLuongMoi > 0) {
            if (isset($_SESSION['gio'][$id])) {
                if (is_array($_SESSION['gio'][$id])) {
                    $_SESSION['gio'][$id]['so_luong'] = $soLuongMoi;
                } else {
                    $_SESSION['gio'][$id] = $soLuongMoi;
                }
            }
        } else {
            unset($_SESSION['gio'][$id]);
        }
    }
    header('Location: gio-hang.php');
    exit;
}

// Lấy danh sách giỏ hàng từ session 'gio'
$gioHang = $_SESSION['gio'] ?? [];
$tongTien = 0;

$tieuDe = 'Giỏ hàng của bạn - Fashion Store';
$trang = 'gio-hang';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" style="max-width: 1000px; margin: 30px auto; padding: 20px;">
    <div style="border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 25px;">
        <h2 style="color: #1e3a8a; margin: 0;">Giỏ hàng / Yêu thích của bạn</h2>
    </div>

    <?php if (empty($gioHang)): ?>
        <div style="text-align: center; padding: 50px; background: white; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <p style="font-size: 1.2rem; color: #dc2626; font-weight: bold; margin-bottom: 20px;">Bạn chưa chọn sản phẩm nào!</p>
            <a href="danh-sach.php" style="display: inline-block; padding: 10px 25px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">Quay lại mua sắm</a>
        </div>
    <?php else: ?>
        <div style="background: white; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 25px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background: #f8fafc; border-bottom: 1px solid #e5e7eb;">
                    <tr>
                        <th style="padding: 15px;">Sản phẩm</th>
                        <th style="padding: 15px; text-align: center;">Đơn giá</th>
                        <th style="padding: 15px; text-align: center;">Số lượng</th>
                        <th style="padding: 15px; text-align: right;">Thành tiền</th>
                        <th style="padding: 15px; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gioHang as $idSp => $item): ?>
                        <?php 
                            if (is_array($item)) {
                                $ten = $item['ten'] ?? 'Sản phẩm';
                                $gia = (float)($item['gia'] ?? 0);
                                $soLuong = (int)($item['so_luong'] ?? 1);
                                $anh = $item['anh'] ?? '';
                            } else {
                                $stmt = db()->prepare("SELECT ten, gia, anh FROM san_pham WHERE id = ?");
                                $stmt->execute([$idSp]);
                                $spDb = $stmt->fetch();
                                $ten = $spDb['ten'] ?? 'Sản phẩm';
                                $gia = (float)($spDb['gia'] ?? 0);
                                $soLuong = (int)$item;
                                $anh = $spDb['anh'] ?? '';
                            }
                            $thanhTien = $gia * $soLuong;
                            $tongTien += $thanhTien;
                        ?>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                                <?php if (!empty($anh)): ?>
                                    <img src="<?= e($anh) ?>" alt="<?= e($ten) ?>" style="width: 60px; height: 60px; object-fit: contain; background: #f9fafb; border-radius: 4px; border: 1px solid #e5e7eb; padding: 5px;">
                                <?php endif; ?>
                                <span style="font-weight: bold; color: #111827;"><?= e($ten) ?></span>
                            </td>
                            <td style="padding: 15px; text-align: center; color: #dc2626; font-weight: bold;"><?= number_format($gia, 0, ',', '.') ?> đ</td>
                            <td style="padding: 15px; text-align: center;">
                                <form method="POST" action="gio-hang.php" style="display: inline-flex; align-items: center; gap: 5px;">
                                    <input type="hidden" name="hanh_dong" value="cap_nhat">
                                    <input type="hidden" name="id" value="<?= (int)$idSp ?>">
                                    <input type="number" name="so_luong" value="<?= $soLuong ?>" min="1" max="99" style="width: 60px; padding: 5px; text-align: center; border: 1px solid #d1d5db; border-radius: 4px;">
                                    <button type="submit" style="padding: 5px 10px; background: #4b5563; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">Cập nhật</button>
                                </form>
                            </td>
                            <td style="padding: 15px; text-align: right; font-weight: bold; color: #111827;"><?= number_format($thanhTien, 0, ',', '.') ?> đ</td>
                            <td style="padding: 15px; text-align: center;">
                                <form method="POST" action="gio-hang.php" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');" style="margin: 0;">
                                    <input type="hidden" name="hanh_dong" value="xoa">
                                    <input type="hidden" name="id" value="<?= (int)$idSp ?>">
                                    <button type="submit" style="padding: 6px 12px; background: #dc2626; color: white; border: none; border-radius: 4px; cursor: pointer;">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div>
                <span style="font-size: 1.2rem; color: #374151; font-weight: bold;">Tổng tiền thanh toán:</span>
                <span style="font-size: 1.5rem; color: #dc2626; font-weight: bold; margin-left: 10px;"><?= number_format($tongTien, 0, ',', '.') ?> đ</span>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <a href="danh-sach.php" style="padding: 12px 20px; background: #e5e7eb; color: #374151; text-decoration: none; border-radius: 6px; font-weight: bold;">Tiếp tục mua sắm</a>
                
                <!-- Sử dụng form POST chính thức để gửi yêu cầu đặt hàng sang dat-hang.php -->
                <form action="dat-hang.php" method="POST" style="margin: 0;">
                    <button type="submit" style="padding: 12px 25px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: bold; font-size: 1rem; cursor: pointer; transition: background 0.3s;">
                        Tiến hành đặt hàng
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>