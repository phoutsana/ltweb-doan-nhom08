<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

$loi = '';
$thanhCong = false;
$emailNhap = '';
$hoTenNhap = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $hoTenNhap = trim($_POST['ho_ten'] ?? '');
    $emailNhap = trim($_POST['email'] ?? '');
    $matKhauNhap = $_POST['mat_khau'] ?? '';

    // Validate cơ bản
    if (empty($hoTenNhap) || empty($emailNhap) || empty($matKhauNhap)) {
        $loi = 'Vui lòng điền đầy đủ tất cả các trường.';
    } elseif (!filter_var($emailNhap, FILTER_VALIDATE_EMAIL)) {
        $loi = 'Địa chỉ email không hợp lệ.';
    } elseif (mb_strlen($matKhauNhap) < 7) { // Sửa từ 6 thành 7 để khớp Ca kiểm thử 6 (bắt buộc >= 7 ký tự)
        $loi = 'Mật khẩu phải có ít nhất 7 ký tự.';
    } else {
        try {
            $pdo = db();

            // Kiểm tra email đã tồn tại chưa (hoặc bắt lỗi 1062)
            $stmtCheck = $pdo->prepare("SELECT id FROM nguoi_dung WHERE email = ?");
            $stmtCheck->execute([$emailNhap]);
            if ($stmtCheck->fetch()) {
                $loi = 'Email này đã được đăng ký tài khoản khác.';
            } else {
                // Mã hóa mật khẩu bảo mật bằng password_hash
                $matKhauBam = password_hash($matKhauNhap, PASSWORD_DEFAULT);
                
                // Mặc định vai trò là khach_hang
                $vaiTro = 'khach_hang';

                $stmt = $pdo->prepare("INSERT INTO nguoi_dung (ho_ten, email, mat_khau, vai_tro) VALUES (?, ?, ?, ?)");
                $stmt->execute([$hoTenNhap, $emailNhap, $matKhauBam, $vaiTro]);

                $thanhCong = true;
            }
        } catch (\PDOException $e) {
            // Bắt lỗi trùng lặp UNIQUE (1062)
            if ($e->getCode() == 23000 || strpos($e->getMessage(), '1062') !== false) {
                $loi = 'Email này đã được đăng ký trong hệ thống.';
            } else {
                $loi = 'Lỗi hệ thống: ' . $e->getMessage();
            }
        }
    }
}

$tieuDe = 'Đăng ký tài khoản - Fashion Store';
$trang = 'dang-ky';
$cssRieng = 'css/dang-ky-bt7-csp.css';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" data-bt7-dk="dk001">
    <div data-bt7-dk="dk002">
        <div data-bt7-dk="dk003"></div>
        <h2 data-bt7-dk="dk004">Đăng Ký Tài Khoản</h2>
    </div>

    <?php if ($thanhCong): ?>
        <div data-bt7-dk="dk005">
            <strong>Thành công!</strong> Đăng ký tài khoản thành công! Đang chuyển hướng sang trang đăng nhập...<br>
            Hoặc bạn có thể bấm vào đây: <a href="dang-nhap.php" data-bt7-dk="dk006">đăng nhập ngay</a>.
        </div>
        <!-- Tự động chuyển hướng sang trang đăng nhập sau 2 giây -->
        <script src="js/dang-ky-bt7-csp.js" defer></script>
    <?php endif; ?>

    <?php if (!empty($loi)): ?>
        <div data-bt7-dk="dk007">
            <strong>Lỗi!</strong> <?= e($loi) ?>
        </div>
    <?php endif; ?>

    <?php if (!$thanhCong): ?>
    <form method="POST" action="dang-ky.php" data-bt7-dk="dk008">
        <?= csrf_field() ?>
        <div>
            <label for="ho_ten" data-bt7-dk="dk009">Họ và tên *</label>
            <input type="text" id="ho_ten" name="ho_ten" value="<?= e($hoTenNhap) ?>" required data-bt7-dk="dk010">
        </div>

        <div>
            <label for="email" data-bt7-dk="dk011">Email *</label>
            <input type="email" id="email" name="email" value="<?= e($emailNhap) ?>" required data-bt7-dk="dk012">
        </div>

        <div>
            <label for="mat_khau" data-bt7-dk="dk013">Mật khẩu (tối thiểu 7 ký tự) *</label>
            <input type="password" id="mat_khau" name="mat_khau" required minlength="7" data-bt7-dk="dk014">
        </div>

        <button type="submit" class="nut-bam nut-bam--chinh" data-bt7-dk="dk015">Đăng ký</button>
    </form>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>