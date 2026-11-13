<?php
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

// Kiểm tra phân quyền Quản trị viên trực tiếp an toàn
if (!isset($_SESSION['user']) || $_SESSION['user']['vai_tro'] !== 'quan_tri') {
    http_response_code(403);
    if (file_exists(__DIR__ . '/403.php')) {
        require __DIR__ . '/403.php';
    } else {
        echo "Bạn không có quyền truy cập trang này!";
    }
    exit;
}

use App\Data\KhoLienHe;

$kho = new KhoLienHe();
$danhSachLienHe = $kho->tatCa();

$tieuDe = 'Trang Quản Trị - Fashion Store';
$trang = 'quan-tri';
$cssRieng = 'css/quan-tri-bt7-csp.css';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" data-bt7-qt="qt001">
    <div data-bt7-qt="qt002">
        <h2 data-bt7-qt="qt003">Danh sách Liên hệ khách hàng</h2>
        <form method="post" action="dang-xuat.php" class="bt7-qt-logout-form">
    <?= csrf_field() ?>
    <button type="submit" class="nut-bam bt7-qt-logout-button">Đăng xuất</button>
</form>
    </div>

    <?php if (empty($danhSachLienHe)): ?>
        <div data-bt7-qt="qt005">
            <p data-bt7-qt="qt006">Chưa có khách hàng nào gửi liên hệ.</p>
        </div>
    <?php else: ?>
        <!-- Bọc bảng danh sách liên hệ bằng div cuộn ngang chống vỡ khung -->
        <div data-bt7-qt="qt007">
            <table data-bt7-qt="qt008">
                <thead data-bt7-qt="qt009">
                    <tr>
                        <th data-bt7-qt="qt010">Thời gian</th>
                        <th data-bt7-qt="qt011">Khách hàng</th>
                        <th data-bt7-qt="qt012">Nội dung chi tiết</th>
                        <th data-bt7-qt="qt013">Ảnh đính kèm</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($danhSachLienHe as $lh): ?>
                        <tr data-bt7-qt="qt014">
                            <td data-bt7-qt="qt015"><?= e($lh['ngay_gui']) ?></td>

                            <td data-bt7-qt="qt016">
                                <p data-bt7-qt="qt017"><?= e($lh['ho_ten']) ?></p>
                                <p data-bt7-qt="qt018">📧 <?= e($lh['email']) ?></p>
                                <?php if (!empty($lh['sdt'])): ?>
                                    <p data-bt7-qt="qt019">📞 <?= e($lh['sdt']) ?></p>
                                <?php endif; ?>
                            </td>

                            <td data-bt7-qt="qt020">
                                <?php if (!empty($lh['chu_de'])): ?>
                                    <p data-bt7-qt="qt021">📌 Chủ đề: <?= e($lh['chu_de']) ?></p>
                                <?php endif; ?>
                                <?= nl2br(e($lh['noi_dung'])) ?>
                            </td>

                            <td data-bt7-qt="qt022">
                                <?php if (!empty($lh['anh'])): ?>
                                    <a href="uploads/<?= e($lh['anh']) ?>" target="_blank">
                                        <img src="uploads/<?= e($lh['anh']) ?>" alt="Ảnh đính kèm" data-bt7-qt="qt023">
                                    </a>
                                <?php else: ?>
                                    <span data-bt7-qt="qt024">Không có ảnh</span>
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