<?php
/**
 * Trang cá nhân Mạnh, giữ tương tác Bài 4 và thêm bộ đếm lượt xem, máy tính điểm.
 * Dùng header/footer chung; máy tính điểm kiểm tra phía máy chủ và dùng PRG.
 * Thử tại http://localhost:8000/thanhvien/3120224094_manh/gioithieu.php
 */

require __DIR__ . '/../../inc/config.php';

$goc = '../../';
$tieuDe = 'Trang giới thiệu cá nhân - Lê Văn Mạnh';
$cssRieng = 'style-canhan.css';

$loiDiem = [];
$diemA1 = $diemA2 = $diemA3 = '';
$diemTongKet = $_SESSION['ketqua_diem'] ?? null;
unset($_SESSION['ketqua_diem']);

if (isset($_SESSION['du_lieu_diem']) && is_array($_SESSION['du_lieu_diem'])) {
    $duLieuDiem = $_SESSION['du_lieu_diem'];
    $diemA1 = is_string($duLieuDiem['a1'] ?? null) ? $duLieuDiem['a1'] : '';
    $diemA2 = is_string($duLieuDiem['a2'] ?? null) ? $duLieuDiem['a2'] : '';
    $diemA3 = is_string($duLieuDiem['a3'] ?? null) ? $duLieuDiem['a3'] : '';
    unset($_SESSION['du_lieu_diem']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';

    if ($action === 'tinhdiem') {
        $tenCotDiem = [
            'a1' => 'Điểm Chuyên cần (A1)',
            'a2' => 'Điểm Giữa kỳ (A2)',
            'a3' => 'Điểm Cuối kỳ (A3)',
        ];
        $diemNhap = [];

        foreach ($tenCotDiem as $key => $label) {
            $value = $_POST[$key] ?? '';
            $diemNhap[$key] = is_string($value) ? trim($value) : '';

            if ($diemNhap[$key] === '' || !is_numeric($diemNhap[$key])) {
                $loiDiem[$key] = "$label phải là một số hợp lệ.";
            } elseif ((float) $diemNhap[$key] < 0 || (float) $diemNhap[$key] > 10) {
                $loiDiem[$key] = "$label phải nằm trong khoảng từ 0 đến 10.";
            }
        }

        $diemA1 = $diemNhap['a1'];
        $diemA2 = $diemNhap['a2'];
        $diemA3 = $diemNhap['a3'];

        if ($loiDiem === []) {
            $_SESSION['ketqua_diem'] = 0.2 * (float) $diemA1
                + 0.3 * (float) $diemA2
                + 0.5 * (float) $diemA3;
            $_SESSION['du_lieu_diem'] = ['a1' => $diemA1, 'a2' => $diemA2, 'a3' => $diemA3];
            header('Location: gioithieu.php#tinh-diem', true, 303);
            exit;
        }
    }
}

$tepLuotXem = __DIR__ . '/../../storage/3120224094_luotxem.txt';
$tepDem = @fopen($tepLuotXem, 'c+');
if ($tepDem === false) {
    throw new RuntimeException('Không thể mở tệp lưu lượt xem.');
}

$daKhoaTep = false;
try {
    if (!@flock($tepDem, LOCK_EX)) {
        throw new RuntimeException('Không thể khóa tệp lưu lượt xem.');
    }
    $daKhoaTep = true;

    $duLieuLuotXem = @stream_get_contents($tepDem);
    if ($duLieuLuotXem === false) {
        throw new RuntimeException('Không thể đọc số lượt xem.');
    }

    $duLieuLuotXem = trim($duLieuLuotXem);
    if ($duLieuLuotXem !== '' && !ctype_digit($duLieuLuotXem)) {
        throw new RuntimeException('Dữ liệu lượt xem không hợp lệ.');
    }

    $soLuotXem = $duLieuLuotXem === '' ? 0 : (int) $duLieuLuotXem;
    if (empty($_SESSION['da_tinh_luot_xem_3120224094'])) {
        if ($soLuotXem === PHP_INT_MAX) {
            throw new OverflowException('Số lượt xem đã vượt giới hạn lưu trữ.');
        }

        $soLuotXem++;
        $duLieuMoi = $soLuotXem . PHP_EOL;
        if (!@rewind($tepDem) || !@ftruncate($tepDem, 0)) {
            throw new RuntimeException('Không thể cập nhật số lượt xem.');
        }

        $soByteDaGhi = @fwrite($tepDem, $duLieuMoi);
        if ($soByteDaGhi !== strlen($duLieuMoi) || !@fflush($tepDem)) {
            throw new RuntimeException('Không thể ghi số lượt xem.');
        }

        $_SESSION['da_tinh_luot_xem_3120224094'] = true;
    }
} finally {
    if ($daKhoaTep && !@flock($tepDem, LOCK_UN)) {
        fclose($tepDem);
        throw new RuntimeException('Không thể mở khóa tệp lưu lượt xem.');
    }
    fclose($tepDem);
}

require __DIR__ . '/../../inc/header.php';
?>

<!-- Header Banner Xanh Dương -->
<header class="header-banner">
    <h1>Trang giới thiệu cá nhân</h1>
    <p>Sinh viên Công nghệ Thông tin - Đam mê Lập trình Web & UI/UX Design</p>
</header>

<!-- Thanh điều hướng chính nội bộ -->
<nav class="main-nav">
    <ul>
        <li><a href="#gioi-thieu" class="active">Giới thiệu</a></li>
        <li><a href="#so-thich">Sở thích</a></li>
        <li><a href="#ky-nang">Kỹ năng</a></li>
        <li><a href="#thoi-khoa-bieu">Thời khóa biểu</a></li>
        <li><a href="#tinh-diem">Tính điểm</a></li>
        <li><a href="#tuong-tac">Góc tương tác</a></li>
    </ul>
</nav>

<main class="profile-container">

    <!-- 1. Card Giới thiệu bản thân -->
    <section id="gioi-thieu" class="card">
        <h2 class="card-title">Giới thiệu bản thân</h2>
        <div class="bio-content">
            <div class="avatar-wrapper">
                <img src="../../images/Mạnh_avata.jpg" alt="Lê Văn Mạnh" class="profile-avatar">
            </div>
            <div class="bio-details">
                <table class="info-table">
                    <tbody>
                        <tr>
                            <th class="label" scope="row">Họ và tên:</th>
                            <td class="value">Lê Văn Mạnh</td>
                        </tr>
                        <tr>
                            <th class="label" scope="row">Ngành học:</th>
                            <td class="value">Công nghệ thông tin</td>
                        </tr>
                        <tr>
                            <th class="label" scope="row">Mã sinh viên:</th>
                            <td class="value">3120224094</td>
                        </tr>
                        <tr>
                            <th class="label" scope="row">Trường:</th>
                            <td class="value">Trường Đại học Sư phạm – Đại học Đà Nẵng</td>
                        </tr>
                        <tr>
                            <th class="label" scope="row">Mục tiêu:</th>
                            <td class="value">Nâng cao kiến thức lập trình và phát triển các sản phẩm công nghệ phục vụ học tập và công việc.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- 2. Card Sở thích / Dự án -->
    <article id="so-thich" class="card">
        <h2 class="card-title">Sở thích / Dự án</h2>
        <p>
            Tôi đặc biệt quan tâm đến lĩnh vực phát triển website và xây dựng các giao diện web thân thiện, tối ưu trải nghiệm người dùng (UI/UX Design).
        </p>
        <p style="margin-top: 10px;">
            Bên cạnh giờ học trên lớp, tôi thường xuyên tìm hiểu các công nghệ mới, thực hành với HTML, CSS, JavaScript, PHP, Python và xây dựng các dự án cá nhân để củng cố kỹ năng lập trình.
        </p>
    </article>

    <!-- 3. Card Kỹ năng -->
    <section id="ky-nang" class="card">
        <h2 class="card-title">Kỹ năng</h2>
        <ul class="skills-list">
            <li class="skill-item">HTML / CSS / JavaScript</li>
            <li class="skill-item">PHP / MySQL</li>
            <li class="skill-item">Java / Python</li>
            <li class="skill-item">Hệ quản trị cơ sở dữ liệu</li>
            <li class="skill-item">Git và GitHub</li>
        </ul>
    </section>

    <!-- 4. Card Thời khóa biểu tuần -->
    <section id="thoi-khoa-bieu" class="card">
        <h2 class="card-title">Thời khóa biểu tuần</h2>
        <div class="table-responsive">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th scope="col">Thứ</th>
                        <th scope="col">Tiết</th>
                        <th scope="col">Mã môn - Tên môn học</th>
                        <th scope="col">Phòng</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row" rowspan="2">Thứ 2</th>
                        <td>1 - 2</td>
                        <td>31221010 - 24-0101 An toàn thông tin</td>
                        <td>B3-206</td>
                    </tr>
                    <tr>
                        <td>3 - 4</td>
                        <td>21221904 - 24-0123 Lịch sử Đảng Cộng sản Việt Nam</td>
                        <td>A5-404C</td>
                    </tr>
                    <tr>
                        <th scope="row">Thứ 4</th>
                        <td>2 - 3</td>
                        <td>31241283 - 24-0102 Hệ quản trị cơ sở dữ liệu</td>
                        <td>A5-404B</td>
                    </tr>
                    <tr>
                        <th scope="row" rowspan="2">Thứ 5</th>
                        <td>4 - 5</td>
                        <td>31231330 - 24-0102 Khai phá dữ liệu</td>
                        <td>A5-404B</td>
                    </tr>
                    <tr>
                        <td>10 - 12</td>
                        <td>31231016 - 24-0103 Công nghệ phần mềm</td>
                        <td>B3-304</td>
                    </tr>
                    <tr>
                        <th scope="row">Thứ 6</th>
                        <td>1 - 3</td>
                        <td>31231398 - 24-0101 Lập trình mạng</td>
                        <td>A5-403</td>
                    </tr>
                    <tr>
                        <th scope="row">Thứ 7</th>
                        <td>1 - 3</td>
                        <td>31231755 - 24-0102 Thiết kế và lập trình web</td>
                        <td>B3-303</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- 5. CHỨC NĂNG PHP 1: Máy tính điểm học phần -->
    <section id="tinh-diem" class="card">
        <h2 class="card-title">🧮 Máy tính điểm học phần (PHP)</h2>
        <p style="margin-bottom: 15px; color: #475569;">Công thức tính: <strong>20% điểm A1 + 30% điểm A2 + 50% điểm A3</strong></p>

        <form method="post" action="gioithieu.php#tinh-diem" class="feedback-form">
            <input type="hidden" name="action" value="tinhdiem">

            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label for="a1">Điểm A1 (Chuyên cần - 20%):</label>
                    <input type="number" min="0" max="10" step="0.1" id="a1" name="a1" value="<?= e($diemA1) ?>" placeholder="Ví dụ: 8.5" required>
                    <?php if (isset($loiDiem['a1'])): ?>
                        <span style="color: #dc2626; font-size: 0.85rem; margin-top: 4px; display: block;"><?= e($loiDiem['a1']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label for="a2">Điểm A2 (Giữa kỳ - 30%):</label>
                    <input type="number" min="0" max="10" step="0.1" id="a2" name="a2" value="<?= e($diemA2) ?>" placeholder="Ví dụ: 7.0" required>
                    <?php if (isset($loiDiem['a2'])): ?>
                        <span style="color: #dc2626; font-size: 0.85rem; margin-top: 4px; display: block;"><?= e($loiDiem['a2']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label for="a3">Điểm A3 (Cuối kỳ - 50%):</label>
                    <input type="number" min="0" max="10" step="0.1" id="a3" name="a3" value="<?= e($diemA3) ?>" placeholder="Ví dụ: 9.0" required>
                    <?php if (isset($loiDiem['a3'])): ?>
                        <span style="color: #dc2626; font-size: 0.85rem; margin-top: 4px; display: block;"><?= e($loiDiem['a3']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn-submit" style="margin-top: 10px;">Tính điểm học phần</button>
        </form>

        <?php if ($diemTongKet !== null): ?>
            <div style="margin-top: 20px; padding: 15px; background-color: #dcfce7; border: 1px solid #86efac; border-radius: 8px; color: #166534; font-weight: 600;">
                🎉 Điểm tổng kết học phần: <?= e(number_format((float) $diemTongKet, 2)) ?> / 10
            </div>
        <?php endif; ?>
    </section>

    <!-- 6. CHỨC NĂNG PHP 2: Góc tương tác và bộ đếm lượt xem -->
    <section id="tuong-tac" class="card">
        <h2 class="card-title">Góc tương tác cá nhân</h2>

        <!-- Đồng hồ đếm ngược JS cũ -->
        <div class="interactive-box">
            <h3>⏳ Đếm ngược ngày thi môn Thiết kế & Lập trình Web</h3>
            <div id="countdown-timer" class="timer-display">
                <span id="days">00</span> ngày <span id="hours">00</span> giờ <span id="minutes">00</span> phút <span id="seconds">00</span> giây
            </div>
        </div>

        <div class="interactive-box">
            <h3>👁️ Lượt xem trang cá nhân</h3>
            <p class="timer-display">
                Tổng lượt xem (mỗi phiên tính một lần):
                <span><?= e(number_format($soLuotXem)) ?></span>
            </p>
        </div>

        <hr style="margin: 20px 0; border: none; border-top: 1px dashed #cbd5e1;">

        <!-- Biểu mẫu góp ý JavaScript được giữ lại từ Bài 4 -->
        <div class="interactive-box">
            <h3>💬 Gửi tin nhắn / Góp ý cho Mạnh</h3>
            <form id="feedback-form" class="feedback-form">
                <div class="form-group">
                    <label for="user-name">Tên của bạn</label>
                    <input type="text" id="user-name" maxlength="50" placeholder="Tên của bạn..." required>
                </div>
                <div class="form-group">
                    <label for="user-msg">Lời nhắn hoặc góp ý</label>
                    <textarea id="user-msg" rows="3" maxlength="500" placeholder="Nhập lời nhắn hoặc góp ý..." required></textarea>
                </div>
                <button type="submit" class="btn-submit">Gửi lời nhắn nhanh</button>
            </form>
            <div id="form-response" class="form-response" role="status" aria-live="polite"></div>
        </div>

        <hr style="margin: 20px 0; border: none; border-top: 1px dashed #cbd5e1;">
        <div class="back-buttons">
            <a href="<?= e($goc) ?>index.html" class="btn-back" style="text-decoration: none; color: #1e42a0; font-weight: 500;">&larr; Quay về trang chủ nhóm</a>
        </div>
    </section>

</main>

<script src="js/canhan.js"></script>

<?php
require __DIR__ . '/../../inc/footer.php';
?>