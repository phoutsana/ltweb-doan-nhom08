<?php

declare(strict_types=1);
require_once __DIR__ . '/inc/config.php';

// Kiểm tra quyền quản trị (Chặn truy cập trái phép)
if (!isset($_SESSION['user']) || $_SESSION['user']['vai_tro'] !== 'quan_tri') {
    http_response_code(403);
    die("<h1>Lỗi 403</h1><p>Bạn không có quyền truy cập trang này.</p>");
}

$pdo = db();

// Lấy danh sách sản phẩm kết hợp với tên danh mục
$sql = "SELECT sp.id, sp.ten, sp.gia, sp.ton_kho, dm.ten as ten_danh_muc, sp.anh 
        FROM san_pham sp 
        LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
        ORDER BY sp.id ASC";
$stmt = $pdo->query($sql);
$sanPhams = $stmt->fetchAll();

$tieuDe = 'Quản lý sản phẩm';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" style="max-width: 1100px; margin: 40px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #1e3a8a;">Quản Lý Sản Phẩm</h2>
        <a href="them-san-pham.php" style="padding: 10px 15px; background: #10b981; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background 0.3s;">
            + Thêm Sản Phẩm Mới
        </a>
    </div>

    <!-- Hiển thị thông báo (Thành công hoặc Lỗi) -->
    <?php if (isset($_GET['tb']) && $_GET['tb'] === 'thanh_cong'): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #10b981;">
            Thao tác thành công!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['loi']) && $_GET['loi'] === 'dang_dung'): ?>
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #ef4444;">
            <strong>Không thể xóa:</strong> Sản phẩm này đã có khách đặt hàng (Vi phạm khóa ngoại 1451).
        </div>
    <?php endif; ?>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 800px;">
            <thead>
                <tr style="background: #f3f4f6; color: #374151;">
                    <th style="padding: 12px; border: 1px solid #e5e7eb;">ID</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">Ảnh</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb;">Tên sản phẩm</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb;">Danh mục</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb;">Giá (VNĐ)</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">Tồn kho</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sanPhams as $sp): ?>
                    <tr>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;"><?= $sp['id'] ?></td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">
                            <?php if (!empty($sp['anh'])): ?>
                                <img src="<?= htmlspecialchars($sp['anh']) ?>" alt="Ảnh SP" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                            <?php else: ?>
                                <span style="color: #9ca3af; font-size: 12px;">Chưa có</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; font-weight: 500;">
                            <?= htmlspecialchars($sp['ten']) ?>
                        </td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">
                            <?= htmlspecialchars($sp['ten_danh_muc'] ?? 'Không có') ?>
                        </td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; color: #e11d48; font-weight: bold;">
                            <?= number_format($sp['gia'], 0, ',', '.') ?> đ
                        </td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">
                            <?php if ($sp['ton_kho'] <= 0): ?>
                                <span style="color: red; font-weight: bold; background: #fee2e2; padding: 2px 6px; border-radius: 4px;">Hết hàng</span>
                            <?php else: ?>
                                <span style="color: #047857; font-weight: bold;"><?= $sp['ton_kho'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="sua-san-pham.php?id=<?= $sp['id'] ?>" style="padding: 6px 12px; background: #f59e0b; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 500;">Sửa</a>

                                <!-- XÓA BẰNG POST: Chống xóa nhầm và chống SQL Injection (Chuẩn kiểm thử Ca 5/8) -->
                                <form method="POST" action="xoa-san-pham.php" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" style="margin: 0;">
                                    <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                                    <button type="submit" style="padding: 6px 12px; background: #ef4444; color: white; border: none; border-radius: 4px; font-size: 14px; font-weight: 500; cursor: pointer;">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($sanPhams)): ?>
                    <tr>
                        <td colspan="7" style="padding: 20px; text-align: center; color: #6b7280; font-style: italic;">
                            Chưa có sản phẩm nào trong hệ thống.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>