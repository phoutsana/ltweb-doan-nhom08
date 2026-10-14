<?php
require_once __DIR__ . '/inc/config.php';

use App\Services\GioHang;

// Khởi tạo dịch vụ giỏ hàng
$gioHang = new GioHang();

// --- XỬ LÝ CÁC HÀNH ĐỘNG CỦA NGƯỜI DÙNG (THÊM, XÓA, XÓA TẤT CẢ) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hanhDong = $_POST['hanh_dong'] ?? '';
    $id = $_POST['id'] ?? '';

    try {
        if ($hanhDong === 'them' && $id !== '') {
            $gioHang->them($id);
        } elseif ($hanhDong === 'xoa' && $id !== '') {
            $gioHang->xoa($id);
        } elseif ($hanhDong === 'xoa_tat_ca') {
            $gioHang->lamTrong();
        }
    } catch (InvalidArgumentException $e) {
        // Bỏ qua lỗi hoặc lưu thông báo lỗi tùy ý
    }

    // Tải lại trang để cập nhật giao diện và tránh lỗi F5
    header('Location: gio-hang.php');
    exit;
}

// Lấy danh sách chi tiết sản phẩm để in ra bảng
$chiTietGio = $gioHang->chiTiet();

$tieuDe = 'Giỏ hàng của bạn - Fashion Store';
$trang = 'gio-hang';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh">
    <!-- ĐOẠN CSS XỬ LÝ ĐỘC LẬP CHO TRANG GIỎ HÀNG CHỐNG VỠ KHUNG TRÊN MOBILE -->
    <style>
        @scope {
            .khung-nut-gio-hang {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 25px;
                gap: 15px;
            }

            .nhom-nut-phai {
                display: flex;
                gap: 10px;
            }

            /* Chế độ điện thoại */
            @media (max-width: 768px) {
                .hop-bang {
                    margin: 0 -20px;
                    /* Trải rộng bảng ra sát mép điện thoại */
                    width: calc(100% + 40px);
                    border-radius: 0;
                    border-left: none;
                    border-right: none;
                }

                .bang-san-pham th,
                .bang-san-pham td {
                    padding: 10px 8px;
                    /* Giảm khoảng cách ô cho gọn */
                }

                .khung-nut-gio-hang {
                    flex-direction: column;
                    align-items: stretch;
                }

                .nhom-nut-phai {
                    flex-direction: column;
                    align-items: stretch;
                    width: 100%;
                }

                .khung-nut-gio-hang button,
                .khung-nut-gio-hang a {
                    width: 100%;
                    text-align: center;
                    display: block;
                    margin: 0 !important;
                    padding: 12px;
                    box-sizing: border-box;
                }
            }
        }
    </style>

    <section style="background-color: transparent; border: none; box-shadow: none; padding: 0;">
        <h2 style="color: #1e3a8a; border-left: 5px solid #2563eb; padding-left: 15px; margin-bottom: 20px;">Giỏ hàng / Yêu thích của bạn</h2>

        <?php if (empty($chiTietGio)): ?>
            <div style="background: white; padding: 40px 20px; text-align: center; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <p style="font-size: 1.2em; color: #dc2626; font-weight: bold; margin-bottom: 20px;">
                    Bạn chưa chọn sản phẩm nào!
                </p>
                <a href="danh-sach.php" class="nut-bam nut-bam--chinh" style="padding: 10px 25px;">Quay lại mua sắm</a>
            </div>
        <?php else: ?>
            <div class="hop-bang" style="overflow-x: auto; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <table class="bang-san-pham" style="width: 100%; min-width: 600px; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8fafc; border-bottom: 2px solid #e5e7eb;">
                            <th scope="col" style="padding: 15px; text-align: left; color: #1e3a8a;">Hình ảnh</th>
                            <th scope="col" style="padding: 15px; text-align: left; color: #1e3a8a;">Tên sản phẩm</th>
                            <th scope="col" style="padding: 15px; text-align: left; color: #1e3a8a;">Đơn giá</th>
                            <th scope="col" style="padding: 15px; text-align: center; color: #1e3a8a;">Số lượng</th>
                            <th scope="col" style="padding: 15px; text-align: left; color: #1e3a8a;">Thành tiền</th>
                            <th scope="col" style="padding: 15px; text-align: center; color: #1e3a8a;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chiTietGio as $item): ?>
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;">
                                <td style="padding: 15px;">
                                    <img src="<?= e($item['sanPham']->hinhAnh) ?>" alt="<?= e($item['sanPham']->ten) ?>" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                                </td>
                                <td style="padding: 15px; font-weight: bold;">
                                    <a href="chi-tiet.php?id=<?= e($item['sanPham']->id) ?>" style="text-decoration: none; color: #2563eb;">
                                        <?= e($item['sanPham']->ten) ?>
                                    </a>
                                </td>
                                <td style="padding: 15px; color: #4b5563;"><?= vnd($item['sanPham']->gia) ?></td>
                                <td style="padding: 15px; text-align: center; font-weight: bold; font-size: 1.1em; color: #111827;"><?= $item['soLuong'] ?></td>
                                <td style="padding: 15px; color: #dc2626; font-weight: bold;"><?= vnd($item['thanhTien']) ?></td>
                                <td style="padding: 15px; text-align: center;">
                                    <!-- Nút xóa 1 sản phẩm -->
                                    <form method="POST" action="gio-hang.php" style="margin: 0;">
                                        <input type="hidden" name="id" value="<?= e($item['sanPham']->id) ?>">
                                        <button type="submit" name="hanh_dong" value="xoa" class="nut-bam nut-bam--phu" style="padding: 6px 12px; border: 1px solid #dc2626; color: #dc2626; background: #fee2e2; font-size: 0.9em;">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f8fafc;">
                            <td colspan="4" style="text-align: right; font-weight: bold; font-size: 1.1em; padding: 20px 15px; color: #1f2937;">Tổng cộng:</td>
                            <td colspan="2" style="color: #dc2626; font-weight: bold; font-size: 1.3em; padding: 20px 15px;">
                                <?= vnd($gioHang->tongTien()) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Nút điều hướng -->
            <div class="khung-nut-gio-hang">
                <form method="POST" action="gio-hang.php" style="margin: 0;">
                    <button type="submit" name="hanh_dong" value="xoa_tat_ca" class="nut-bam nut-bam--phu" style="border: 1px solid #64748b; color: #475569; background: white;">Xóa toàn bộ giỏ hàng</button>
                </form>

                <div class="nhom-nut-phai">
                    <a href="danh-sach.php" class="nut-bam nut-bam--phu" style="background: white;">Tiếp tục mua sắm</a>
                    <button class="nut-bam nut-bam--chinh" onclick="alert('Tính năng thanh toán đang được phát triển!')">Tiến hành thanh toán</button>
                </div>
            </div>
        <?php endif; ?>

    </section>
</main>

<script type="module" src="js/main.js"></script>
<?php require_once __DIR__ . '/inc/footer.php'; ?>