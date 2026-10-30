<?php
require_once __DIR__ . '/inc/config.php';
// ĐÃ XÓA: require_once __DIR__ . '/inc/tai-khoan.php';

// Đã đăng nhập rồi thì phân hướng dựa theo vai trò
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['vai_tro'] === 'quan_tri') {
        header('Location: quan-tri.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$loi = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? ''); // Giữ name='email' nhưng thực chất là email
    $pass  = $_POST['password'] ?? '';

    try {
        $pdo = \db();

        // Truy vấn thông tin người dùng theo email (Dùng prepared statement chống SQL Injection)
        $stmt = $pdo->prepare("SELECT id, email, mat_khau, ho_ten, vai_tro FROM nguoi_dung WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $nguoiDung = $stmt->fetch();

        // Kiểm tra tài khoản có tồn tại và khớp mật khẩu băm không
        if ($nguoiDung && password_verify($pass, $nguoiDung['mat_khau'])) {
            session_regenerate_id(true); // Cấp mã phiên mới (Bảo mật bắt buộc)

            // Lưu thông tin thiết yếu vào Session
            $_SESSION['user'] = [
                'id'      => $nguoiDung['id'],
                'email'   => $nguoiDung['email'],
                'ho_ten'  => $nguoiDung['ho_ten'],
                'vai_tro' => $nguoiDung['vai_tro']
            ];

            // Phân quyền: Quản trị viên vào trang quản trị, Khách hàng về trang chủ
            if ($nguoiDung['vai_tro'] === 'quan_tri') {
                header('Location: quan-tri.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            error_log("Đăng nhập sai tài khoản: $email"); // Ghi log lỗi
            $loi = 'Sai tên đăng nhập (Email) hoặc mật khẩu'; // Báo lỗi chung chung
        }
    } catch (\PDOException $e) {
        error_log("Lỗi truy vấn CSDL (Đăng nhập): " . $e->getMessage());
        $loi = 'Hệ thống đang bận, vui lòng thử lại sau.';
    }
}

$tieuDe = 'Đăng nhập - Fashion Store';
$trang = 'dang-nhap';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" style="max-width: 500px; margin: 50px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; color: #1e3a8a; margin-bottom: 20px;">Đăng Nhập</h2>

    <?php if ($loi): ?>
        <div style="background-color: #fee2e2; color: #991b1b; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f87171; text-align: center;">
            <?= e($loi) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="dang-nhap.php" style="display: flex; flex-direction: column; gap: 15px;">
        <div>
            <label for="email" style="font-weight: bold; display: block; margin-bottom: 5px;">Tài khoản (Email) *</label>
            <!-- Đổi name="username" thành name="email" cho khớp logic -->
            <input type="email" id="email" name="email" placeholder="Ví dụ: admin@fashionstore.com" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
        </div>
        <div>
            <label for="password" style="font-weight: bold; display: block; margin-bottom: 5px;">Mật khẩu *</label>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
        </div>
        <button type="submit" class="nut-bam nut-bam--chinh" style="padding: 12px; font-size: 1.1rem; cursor: pointer; border: none; border-radius: 4px; margin-top: 10px;">Đăng nhập</button>
    </form>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>