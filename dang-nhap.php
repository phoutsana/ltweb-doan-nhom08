<?php
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/tai-khoan.php';

// Đã đăng nhập rồi thì đẩy thẳng vào trang quản trị
if (isset($_SESSION['user'])) {
    header('Location: quan-tri.php');
    exit;
}

$loi = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    // Kiểm tra mật khẩu băm
    if ($user === $tkAdmin && password_verify($pass, $mkAdminHash)) {
        session_regenerate_id(true); // Cấp mã phiên mới (Bảo mật bắt buộc)
        $_SESSION['user'] = $user;
        header('Location: quan-tri.php');
        exit;
    } else {
        error_log("Đăng nhập sai tài khoản: $user"); // Ghi log lỗi
        $loi = 'Sai tên đăng nhập hoặc mật khẩu';    // Báo lỗi chung chung
    }
}

$tieuDe = 'Đăng nhập - Fashion Store';
$trang = 'dang-nhap';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" style="max-width: 500px; margin: 50px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; color: #1e3a8a; margin-bottom: 20px;">Đăng Nhập Quản Trị</h2>

    <?php if ($loi): ?>
        <div style="background-color: #fee2e2; color: #991b1b; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f87171; text-align: center;">
            <?= e($loi) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="dang-nhap.php" style="display: flex; flex-direction: column; gap: 15px;">
        <div>
            <label for="username" style="font-weight: bold; display: block; margin-bottom: 5px;">Tài khoản</label>
            <input type="text" id="username" name="username" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
        </div>
        <div>
            <label for="password" style="font-weight: bold; display: block; margin-bottom: 5px;">Mật khẩu</label>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
        </div>
        <button type="submit" class="nut-bam nut-bam--chinh" style="padding: 12px; font-size: 1.1rem; cursor: pointer; border: none; border-radius: 4px; margin-top: 10px;">Đăng nhập</button>
    </form>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>