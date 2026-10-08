<?php
/**
 * Trang giới thiệu cá nhân, sổ lưu bút và máy tính điểm học phần.
 * Dùng chung phần đầu/chân trang trong inc/; giữ CSS và JavaScript riêng.
 * Thử tại http://localhost:8000/thanhvien/3120224094_manh/gioithieu.php
 */

require __DIR__ . '/../../inc/config.php';

$goc = '../../';
$tieuDe = 'Trang giới thiệu cá nhân - Lê Văn Mạnh';
$cssRieng = 'style-canhan.css';

$loiLuuBut = [];
$duLieuLuuBut = ['ten' => '', 'noidung' => ''];

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

$danhSachLuuBut = [];
$thongBaoLuuBut = is_string($_SESSION['flash_luubut'] ?? null)
    ? $_SESSION['flash_luubut']
    : '';
unset($_SESSION['flash_luubut']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';

    if ($action === 'luubut') {
        foreach (['ten', 'noidung'] as $field) {
            $value = $_POST[$field] ?? '';
            $duLieuLuuBut[$field] = is_string($value) ? trim($value) : '';
        }

        if ($duLieuLuuBut['ten'] === '') {
            $loiLuuBut['ten'] = 'Vui lòng nhập họ tên của bạn.';
        } elseif (mb_strlen($duLieuLuuBut['ten'], 'UTF-8') > 50) {
            $loiLuuBut['ten'] = 'Họ tên không được vượt quá 50 ký tự.';
        }

        if ($duLieuLuuBut['noidung'] === '') {
            $loiLuuBut['noidung'] = 'Vui lòng nhập lời nhắn hoặc góp ý.';
        } elseif (mb_strlen($duLieuLuuBut['noidung'], 'UTF-8') > 500) {
            $loiLuuBut['noidung'] = 'Lời nhắn không được vượt quá 500 ký tự.';
        }

        if ($loiLuuBut === []) {
            $tepLuuBut = __DIR__ . '/../../storage/3120224094_luubut.jsonl';
            $dongData = json_encode([
                'thoiGian' => date('Y-m-d H:i:s'),
                'ten' => $duLieuLuuBut['ten'],
                'noiDung' => $duLieuLuuBut['noidung'],
            ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

            if (file_put_contents($tepLuuBut, $dongData . PHP_EOL, FILE_APPEND | LOCK_EX) === false) {
                http_response_code(500);
                $loiLuuBut['noidung'] = 'Không thể lưu lời nhắn. Vui lòng thử lại sau.';
            } else {
                $_SESSION['flash_luubut'] = 'Cảm ơn bạn đã gửi lời nhắn cho Mạnh!';
                header('Location: gioithieu.php#tuong-tac', true, 303);
                exit;
            }
        }
    } elseif ($action === 'tinhdiem') {
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

$tepLuuButPath = __DIR__ . '/../../storage/3120224094_luubut.jsonl';
if (is_file($tepLuuButPath)) {
    if (!is_readable($tepLuuButPath)) {
        throw new RuntimeException('Không thể đọc sổ lưu bút.');
    }

    $dongs = file($tepLuuButPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($dongs === false) {
        throw new RuntimeException('Không thể đọc sổ lưu bút.');
    }

    foreach ($dongs as $dong) {
        $tinNhan = json_decode($dong, true, 512, JSON_THROW_ON_ERROR);
        if (
            is_array($tinNhan)
            && is_string($tinNhan['ten'] ?? null)
            && is_string($tinNhan['noiDung'] ?? null)
            && is_string($tinNhan['thoiGian'] ?? null)
        ) {
            $danhSachLuuBut[] = $tinNhan;
        }
    }

    $danhSachLuuBut = array_slice(array_reverse($danhSachLuuBut), 0, 5);
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

    <!-- 6. CHỨC NĂNG PHP 2: Góc tương tác (Sổ lưu bút) & Đồng hồ đếm ngược -->
    <section id="tuong-tac" class="card">
        <h2 class="card-title">Góc tương tác cá nhân</h2>

        <!-- Đồng hồ đếm ngược JS cũ -->
        <div class="interactive-box">
            <h3>⏳ Đếm ngược ngày thi môn Thiết kế & Lập trình Web</h3>
            <div id="countdown-timer" class="timer-display">
                <span id="days">00</span> ngày <span id="hours">00</span> giờ <span id="minutes">00</span> phút <span id="seconds">00</span> giây
            </div>
        </div>

        <hr style="margin: 20px 0; border: none; border-top: 1px dashed #cbd5e1;">

        <!-- Form gửi tin nhắn / Sổ lưu bút PHP -->
        <div class="interactive-box">
            <h3>💬 Sổ lưu bút / Gửi lời nhắn cho Mạnh (PHP)</h3>

            <?php if ($thongBaoLuuBut !== ''): ?>
                <div style="padding: 12px; background-color: #e0f2fe; border: 1px solid #7dd3fc; border-radius: 6px; color: #0369a1; margin-bottom: 15px; font-weight: 500;">
                    <?= e($thongBaoLuuBut) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="gioithieu.php#tuong-tac" class="feedback-form">
                <input type="hidden" name="action" value="luubut">

                <div class="form-group">
                    <label for="user-name">Tên của bạn</label>
                    <input type="text" id="user-name" name="ten" value="<?= e($duLieuLuuBut['ten']) ?>" maxlength="50" required>
                    <?php if (isset($loiLuuBut['ten'])): ?>
                        <span style="color: #dc2626; font-size: 0.85rem; margin-top: 4px; display: block;"><?= e($loiLuuBut['ten']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="user-msg">Lời nhắn hoặc góp ý</label>
                    <textarea id="user-msg" name="noidung" rows="3" maxlength="500" required><?= e($duLieuLuuBut['noidung']) ?></textarea>
                    <?php if (isset($loiLuuBut['noidung'])): ?>
                        <span style="color: #dc2626; font-size: 0.85rem; margin-top: 4px; display: block;"><?= e($loiLuuBut['noidung']) ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-submit">Gửi lời nhắn</button>
            </form>

            <h4 style="margin-top: 25px; margin-bottom: 12px; color: #1e293b; font-size: 1rem;">📋 5 lời nhắn mới nhất trong sổ lưu bút:</h4>
            <?php if (empty($danhSachLuuBut)): ?>
                <p style="color: #64748b; font-style: italic; font-size: 0.9rem;">Chưa có lời nhắn nào. Hãy là người đầu tiên để lại lưu bút nhé!</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php foreach ($danhSachLuuBut as $msg): ?>
                        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <strong style="color: #1e42a0;"><?= e($msg['ten']) ?></strong>
                                <small style="color: #94a3b8; font-size: 0.8rem;"><?= e($msg['thoiGian']) ?></small>
                            </div>
                            <p style="margin: 0; color: #334155; font-size: 0.95rem; word-break: break-word;"><?= e($msg['noiDung']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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