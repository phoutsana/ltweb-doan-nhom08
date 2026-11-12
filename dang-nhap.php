<?php
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';
require_once __DIR__ . '/inc/security_audit.php';
require_once __DIR__ . '/inc/login_rate_limit.php';

// BT7 - MC23: Bao mat dang nhap, chong Brute Force
// Gioi han 5 lan dang nhap sai trong 15 phut

// 1. Da dang nhap thi chuyen trang theo vai tro
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['vai_tro'] === 'quan_tri') {
        header('Location: quan-tri.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$loi = '';
$email = '';

// 2. Xu ly khi nguoi dung gui form POST

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiem tra CSRF truoc khi xu ly dang nhap
    csrf_verify();

    try {

        // 3. Kiem tra gioi han dang nhap truoc khi truy van CSDL
        $loginLimit = bt7_login_rate_limit();

        if ($loginLimit['blocked']) {

            // ========================================
            // MC23: CHAN DANG NHAP KHI VUOT GIOI HAN
            // ========================================

            http_response_code(429);

            header(
                'Retry-After: ' .
                $loginLimit['retry_after']
            );

            // Ghi log su kien vuot gioi han
            bt7_security_log(
                'LOGIN_RATE_LIMITED',
                '/dang-nhap.php',
                429
            );

            $loi = 'Bạn đã đăng nhập sai quá nhiều lần. Vui lòng thử lại sau.';

        } else {

            // 4. Lay du lieu dau vao
            $emailInput = $_POST['email'] ?? '';
            $passInput = $_POST['password'] ?? '';

            $email = is_string($emailInput)
                ? trim($emailInput)
                : '';

            $pass = is_string($passInput)
                ? $passInput
                : '';

            // 5. Ket noi CSDL
            $pdo = db();

            // 6. Prepared Statement chong SQL Injection
            $stmt = $pdo->prepare(
                "SELECT id, email, mat_khau, ho_ten, vai_tro
                 FROM nguoi_dung
                 WHERE email = :email
                 LIMIT 1"
            );

            $stmt->execute([
                'email' => $email
            ]);

            $nguoiDung = $stmt->fetch();

            // 7. Kiem tra email va mat khau
            if (
                $nguoiDung &&
                password_verify(
                    $pass,
                    $nguoiDung['mat_khau']
                )
            ) {

                // ========================================
                // DANG NHAP THANH CONG
                // ========================================

                // Chong Session Fixation
                session_regenerate_id(true);

                // Luu thong tin can thiet vao Session
                $_SESSION['user'] = [
                    'id'      => $nguoiDung['id'],
                    'email'   => $nguoiDung['email'],
                    'ho_ten'  => $nguoiDung['ho_ten'],
                    'vai_tro' => $nguoiDung['vai_tro']
                ];

                // Phan quyen theo vai tro
                if ($nguoiDung['vai_tro'] === 'quan_tri') {
                    header('Location: quan-tri.php');
                } else {
                    header('Location: index.php');
                }

                exit;

            } else {

                // ========================================
                // MC22c + MC23: DANG NHAP THAT BAI
                // ========================================

                // Tang bo dem dang nhap sai
                // Day la dong quan trong cua MC23
                bt7_login_rate_limit(true);

                // Tra HTTP 401
                http_response_code(401);

                // Ghi log LOGIN_FAILED
                // Khong ghi email, mat khau, cookie hay token
                bt7_security_log(
                    'LOGIN_FAILED',
                    '/dang-nhap.php',
                    401
                );

                // Thong bao chung
                $loi = 'Sai tên đăng nhập (Email) hoặc mật khẩu';
            }
        }

    } catch (PDOException $e) {

        // Loi ket noi hoac truy van CSDL
        error_log(
            'Lỗi truy vấn CSDL (Đăng nhập): ' .
            $e->getMessage()
        );

        http_response_code(503);

        $loi = 'Hệ thống đang bận, vui lòng thử lại sau.';

    } catch (Throwable $e) {

        // Loi bo dem dang nhap hoac loi he thong khac
        // Khong cho phep bo qua rate limit khi co loi
        error_log(
            'BT7: Loi xu ly dang nhap hoac rate limit'
        );

        http_response_code(503);

        $loi = 'Hệ thống đang bận, vui lòng thử lại sau.';
    }
}

// 8. Thiet lap tieu de trang
$tieuDe = 'Đăng nhập - Fashion Store';
$trang = 'dang-nhap';

require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh"
      style="max-width: 500px;
             margin: 50px auto;
             padding: 30px;
             background: white;
             border-radius: 8px;
             box-shadow: 0 1px 3px rgba(0,0,0,0.1);">

    <h2 style="text-align: center;
               color: #1e3a8a;
               margin-bottom: 20px;">
        Đăng Nhập
    </h2>

    <!-- Thong bao loi -->
    <?php if ($loi): ?>

        <div style="background-color: #fee2e2;
                    color: #991b1b;
                    padding: 10px;
                    border-radius: 4px;
                    margin-bottom: 15px;
                    border: 1px solid #f87171;
                    text-align: center;">

            <?= e($loi) ?>

        </div>

    <?php endif; ?>

    <!-- Form dang nhap -->
    <form method="POST"
          action="dang-nhap.php"
          style="display: flex;
                 flex-direction: column;
                 gap: 15px;">

<?= csrf_field() ?>

        <!-- Email -->
        <div>
            <label for="email"
                   style="font-weight: bold;
                          display: block;
                          margin-bottom: 5px;">
                Tài khoản (Email) *
            </label>

            <input type="email"
                   id="email"
                   name="email"
                   value="<?= e($email) ?>"
                   placeholder="Ví dụ: admin@fashionstore.com"
                   required
                   autocomplete="username"
                   style="width: 100%;
                          padding: 10px;
                          border: 1px solid #d1d5db;
                          border-radius: 4px;
                          box-sizing: border-box;">
        </div>

        <!-- Mat khau -->
        <div>
            <label for="password"
                   style="font-weight: bold;
                          display: block;
                          margin-bottom: 5px;">
                Mật khẩu *
            </label>

            <input type="password"
                   id="password"
                   name="password"
                   required
                   autocomplete="current-password"
                   style="width: 100%;
                          padding: 10px;
                          border: 1px solid #d1d5db;
                          border-radius: 4px;
                          box-sizing: border-box;">
        </div>

        <!-- Nut dang nhap -->
        <button type="submit"
                class="nut-bam nut-bam--chinh"
                style="padding: 12px;
                       font-size: 1.1rem;
                       cursor: pointer;
                       border: none;
                       border-radius: 4px;
                       margin-top: 10px;">
            Đăng nhập
        </button>

    </form>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
