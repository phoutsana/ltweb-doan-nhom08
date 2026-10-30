<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';

$loi = '';
$thanhCong = false;
$emailNhap = '';
$hoTenNhap = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoTenNhap = trim($_POST['ho_ten'] ?? '');
    $emailNhap = trim($_POST['email'] ?? '');
    $matKhauNhap = $_POST['mat_khau'] ?? '';

    // Validate cơ bản
    if (empty($hoTenNhap) || empty($emailNhap) || empty($matKhauNhap)) {
        $loi = 'Vui lòng điền đầy đủ tất cả các trường.';
    } elseif (!filter_var($emailNhap, FILTER_VALIDATE_EMAIL)) {
        $loi = 'Địa chỉ email không hợp lệ.';
    } elseif (mb_strlen($matKhauNhap) < 6) {
        $loi = 'Mật khẩu phải có ít nhất 6 ký tự.';
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
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" style="max-width: 500px; margin: 40px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 10px;">
        <div style="width: 4px; height: 24px; background: #2563eb;"></div>
        <h2 style="color: #1e3a8a; margin: 0;">Đăng Ký Tài Khoản</h2>
    </div>

    <?php if ($thanhCong): ?>
        <div style="background-color: #d1fae5; color: #065f46; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #10b981;">
            <strong>Thành công!</strong> Đăng ký tài khoản thành công! Đang chuyển hướng sang trang đăng nhập...<br>
            Hoặc bạn có thể bấm vào đây: <a href="dang-nhap.php" style="color: #2563eb; font-weight: bold; text-decoration: underline;">đăng nhập ngay</a>.
        </div>
        <!-- Tự động chuyển hướng sang trang đăng nhập sau 2 giây -->
        <script>
            setTimeout(function() {
                window.location.href = 'dang-nhap.php';
            }, 2000);
        </script>
    <?php endif; ?>

    <?php if (!empty($loi)): ?>
        <div style="background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f87171;">
            <strong>Lỗi!</strong> <?= e($loi) ?>
        </div>
    <?php endif; ?>

    <?php if (!$thanhCong): ?>
    <form method="POST" action="dang-ky.php" style="display: flex; flex-direction: column; gap: 15px;">
        <div>
            <label for="ho_ten" style="font-weight: bold; display: block; margin-bottom: 5px;">Họ và tên *</label>
            <input type="text" id="ho_ten" name="ho_ten" value="<?= e($hoTenNhap) ?>" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div>
            <label for="email" style="font-weight: bold; display: block; margin-bottom: 5px;">Email *</label>
            <input type="email" id="email" name="email" value="<?= e($emailNhap) ?>" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div>
            <label for="mat_khau" style="font-weight: bold; display: block; margin-bottom: 5px;">Mật khẩu *</label>
            <input type="password" id="mat_khau" name="mat_khau" required minlength="6" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
        </div>

        <button type="submit" class="nut-bam nut-bam--chinh" style="padding: 12px; font-size: 1.1rem; cursor: pointer; border: none; border-radius: 4px; background-color: #2563eb; color: white; font-weight: bold;">Đăng ký</button>
    </form>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>