<?php
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/bao-ve.php'; // Chặn người lạ chưa đăng nhập
use App\Data\KhoLienHe;

$kho = new KhoLienHe(__DIR__ . '/storage/lien-he.jsonl');
$danhSachLienHe = $kho->tatCa(); // Lấy mảng liên hệ (mới nhất ở đầu)

$tieuDe = 'Trang Quản Trị - Fashion Store';
$trang = 'quan-tri';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" style="max-width: 1000px; margin: 30px auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 10px;">
        <h2 style="color: #1e3a8a; margin: 0;">Danh sách Liên hệ khách hàng</h2>
        <a href="dang-xuat.php" class="nut-bam" style="background-color: #dc2626; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none;">Đăng xuất</a>
    </div>

    <?php if (empty($danhSachLienHe)): ?>
        <div style="text-align: center; padding: 40px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
            <p style="font-size: 1.2rem; color: #4b5563;">Chưa có khách hàng nào gửi liên hệ.</p>
        </div>
    <?php else: ?>
        <div class="hop-bang" style="background: white; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: #f8fafc; text-align: left;">
                    <tr>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb;">Thời gian</th>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb;">Khách hàng</th>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb;">Nội dung</th>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb;">Ảnh đính kèm</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($danhSachLienHe as $lh): ?>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 15px; vertical-align: top; width: 15%; color: #4b5563;"><?= e($lh['thoiGian']) ?></td>
                            <td style="padding: 15px; vertical-align: top; width: 25%;">
                                <p style="font-weight: bold; margin: 0 0 5px 0; color: #111827;"><?= e($lh['hoTen']) ?></p>
                                <p style="margin: 0 0 5px 0; font-size: 0.9em;">📧 <?= e($lh['email']) ?></p>
                                <p style="margin: 0; font-size: 0.9em;">📞 <?= e($lh['sdt']) ?></p>
                            </td>
                            <td style="padding: 15px; vertical-align: top; width: 40%;">
                                <?= nl2br(e($lh['noiDung'])) ?>
                            </td>
                            <td style="padding: 15px; vertical-align: top; text-align: center; width: 20%;">
                                <?php if (!empty($lh['anh'])): ?>
                                    <a href="uploads/<?= e($lh['anh']) ?>" target="_blank">
                                        <img src="uploads/<?= e($lh['anh']) ?>" alt="Ảnh đính kèm" style="max-width: 100px; max-height: 100px; border-radius: 4px; border: 1px solid #d1d5db; object-fit: cover;">
                                    </a>
                                <?php else: ?>
                                    <span style="color: #9ca3af; font-size: 0.9em;">Không có ảnh</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>