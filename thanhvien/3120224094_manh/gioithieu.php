<?php
/**
 * Tệp: thanhvien/3120224094_manh/gioithieu.php
 * Mô tả: Trang giới thiệu cá nhân của Lê Văn Mạnh (MSSV: 3120224094).
 * Chức năng PHP:
 *   1. Sổ lưu bút cá nhân (Lưu tệp storage/3120224094_luubut.jsonl).
 *   2. Máy tính điểm học phần (Công thức 0.2*A1 + 0.3*A2 + 0.5*A3).
 *   3. Hòm thư Góp ý & Đánh giá Sao (CSDL MySQL - Bảng tv_danh_gia dùng hàm db()).
 * Cách thử: Mở trình duyệt truy cập http://localhost:8000/thanhvien/3120224094_manh/gioithieu.php
 */

require __DIR__ . '/../../inc/config.php';

// Đường dẫn tương đối về thư mục gốc của dự án
$goc = '../../';
$tieuDe = 'Trang giới thiệu cá nhân - Lê Văn Mạnh';
$masvManh = '3120224094';

// ==========================================
// CHỨC NĂNG 1: SỔ LƯU BÚT CÁ NHÂN (LƯU TỆP JSONL)
// ==========================================
$loiLuuBut = [];
$duLieuLuuBut = ['ten' => '', 'noidung' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_luubut'])) {
    $duLieuLuuBut['ten'] = trim($_POST['ten'] ?? '');
    $duLieuLuuBut['noidung'] = trim($_POST['noidung'] ?? '');

    if (mb_strlen($duLieuLuuBut['ten']) === 0) {
        $loiLuuBut['ten'] = 'Vui lòng nhập họ tên của bạn.';
    } elseif (mb_strlen($duLieuLuuBut['ten']) > 50) {
        $loiLuuBut['ten'] = 'Họ tên không được vượt quá 50 ký tự.';
    }

    if (mb_strlen($duLieuLuuBut['noidung']) === 0) {
        $loiLuuBut['noidung'] = 'Vui lòng nhập lời nhắn hoặc góp ý.';
    } elseif (mb_strlen($duLieuLuuBut['noidung']) > 500) {
        $loiLuuBut['noidung'] = 'Lời nhắn không được vượt quá 500 ký tự.';
    }

    if (empty($loiLuuBut)) {
        $tepLuuBut = __DIR__ . '/../../storage/3120224094_luubut.jsonl';
        $dongData = json_encode([
            'thoiGian' => date('Y-m-d H:i:s'),
            'ten' => $duLieuLuuBut['ten'],
            'noiDung' => $duLieuLuuBut['noidung']
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        // Đảm bảo thư mục storage tồn tại
        if (!is_dir(dirname($tepLuuBut))) {
            mkdir(dirname($tepLuuBut), 0777, true);
        }

        file_put_contents($tepLuuBut, $dongData . PHP_EOL, FILE_APPEND | LOCK_EX);
        $_SESSION['flash_luubut'] = 'Cảm ơn bạn đã gửi lời nhắn cho Mạnh!';

        header('Location: gioithieu.php#tuong-tac');
        exit;
    }
}

$danhSachLuuBut = [];
$tepLuuButPath = __DIR__ . '/../../storage/3120224094_luubut.jsonl';
if (is_file($tepLuuButPath)) {
    $dongs = file($tepLuuButPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $allMsgs = array_map(fn($d) => json_decode($d, true), $dongs);
    $danhSachLuuBut = array_slice(array_reverse(array_filter($allMsgs, 'is_array')), 0, 5);
}

$thongBaoLuuBut = $_SESSION['flash_luubut'] ?? '';
unset($_SESSION['flash_luubut']);


// ==========================================
// CHỨC NĂNG 2: MÁY TÍNH ĐIỂM HỌC PHẦN
// ==========================================
$loiDiem = [];
$diemA1 = $diemA2 = $diemA3 = '';
$diemTongKet = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_tinhdiem'])) {
    $diemA1 = trim($_POST['a1'] ?? '');
    $diemA2 = trim($_POST['a2'] ?? '');
    $diemA3 = trim($_POST['a3'] ?? '');

    $cots = [
        'a1' => 'Điểm Chuyên cần (A1)',
        'a2' => 'Điểm Giữa kỳ (A2)',
        'a3' => 'Điểm Cuối kỳ (A3)'
    ];

    foreach ($cots as $key => $label) {
        $val = trim($_POST[$key] ?? '');
        if ($val === '' || !is_numeric($val)) {
            $loiDiem[$key] = "$label phải là một số hợp lệ.";
        } else {
            $fVal = (float)$val;
            if ($fVal < 0 || $fVal > 10) {
                $loiDiem[$key] = "$label phải nằm trong khoảng từ 0 đến 10.";
            }
        }
    }

    if (empty($loiDiem)) {
        $diemTongKet = (0.2 * (float)$diemA1) + (0.3 * (float)$diemA2) + (0.5 * (float)$diemA3);
    }
}


// ==========================================
// CHỨC NĂNG CSDL: HÒM THƯ GÓP Ý & ĐÁNH GIÁ SAO (Dùng db())
// ==========================================
$loiDanhGia = [];
$duLieuDanhGia = ['nguoi_gui' => '', 'so_sao' => '5', 'nhan_xet' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_danhgia'])) {
    $duLieuDanhGia['nguoi_gui'] = trim($_POST['nguoi_gui'] ?? '');
    $duLieuDanhGia['so_sao'] = trim($_POST['so_sao'] ?? '5');
    $duLieuDanhGia['nhan_xet'] = trim($_POST['nhan_xet'] ?? '');

    if (mb_strlen($duLieuDanhGia['nguoi_gui']) === 0) {
        $loiDanhGia['nguoi_gui'] = 'Vui lòng nhập tên của bạn.';
    }
    if (mb_strlen($duLieuDanhGia['nhan_xet']) === 0) {
        $loiDanhGia['nhan_xet'] = 'Vui lòng nhập nội dung góp ý.';
    }

    if (empty($loiDanhGia)) {
        try {
            $stmt = db()->prepare("INSERT INTO tv_danh_gia (masv, nguoi_gui, so_sao, nhan_xet, thoi_gian) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$masvManh, $duLieuDanhGia['nguoi_gui'], (int)$duLieuDanhGia['so_sao'], $duLieuDanhGia['nhan_xet']]);

            $_SESSION['flash_danhgia'] = 'Cảm ơn bạn đã gửi đánh giá!';
            header('Location: gioithieu.php#danh-gia-trang');
            exit;
        } catch (PDOException $e) {
            $loiDanhGia['chung'] = 'Lỗi CSDL: ' . $e->getMessage();
        }
    }
}

$danhSachDanhGia = [];
$diemTrungBinh = 0;
$tongLuotDanhGia = 0;
try {
    $stmt = db()->prepare("SELECT nguoi_gui, so_sao, nhan_xet, thoi_gian FROM tv_danh_gia WHERE masv = ? ORDER BY id DESC");
    $stmt->execute([$masvManh]);
    $danhSachDanhGia = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmtAvg = db()->prepare("SELECT AVG(so_sao) as tb, COUNT(*) as tong FROM tv_danh_gia WHERE masv = ?");
    $stmtAvg->execute([$masvManh]);
    $rowStats = $stmtAvg->fetch(PDO::FETCH_ASSOC);
    if ($rowStats && $rowStats['tong'] > 0) {
        $diemTrungBinh = round((float)$rowStats['tb'], 1);
        $tongLuotDanhGia = (int)$rowStats['tong'];
    }
} catch (PDOException $e) {
    $danhSachDanhGia = [];
}
$thongBaoDanhGia = $_SESSION['flash_danhgia'] ?? '';
unset($_SESSION['flash_danhgia']);

// Nhúng Header dùng chung của nhóm
require __DIR__ . '/../../inc/header.php';
?>

<link rel="stylesheet" href="style-canhan.css">

<!-- Header Banner Xanh Dương -->
<header class="header-banner">
    <h1>Trang giới thiệu cá nhân</h1>
    <p>Sinh viên Công nghệ Thông tin - Đam mê Lập trình Web & Thiết kế Thời trang</p>
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
        <li><a href="#danh-gia-trang">Hòm thư Đánh giá (CSDL)</a></li>
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
                    <tr>
                        <td class="label">Họ và tên:</td>
                        <td class="value">Lê Văn Mạnh</td>
                    </tr>
                    <tr>
                        <td class="label">Mã sinh viên:</td>
                        <td class="value">3120224094</td>
                    </tr>
                    <tr>
                        <td class="label">Đồ án nhóm:</td>
                        <td class="value">Website Thương mại Điện tử Thời trang</td>
                    </tr>
                </table>
            </div>
        </div>
    </section>

    <!-- 2. Card Sở thích / Dự án -->
    <article id="so-thich" class="card">
        <h2 class="card-title">Sở thích / Dự án</h2>
        <p>Tôi đặc biệt quan tâm đến lĩnh vực phát triển website và xây dựng các giao diện web thân thiện, tối ưu trải nghiệm người dùng trong Thương mại điện tử thời trang.</p>
    </article>

    <!-- 3. Card Kỹ năng -->
    <section id="ky-nang" class="card">
        <h2 class="card-title">Kỹ năng</h2>
        <ul class="skills-list">
            <li class="skill-item">HTML / CSS / JavaScript</li>
            <li class="skill-item">PHP / MySQL</li>
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
                        <th scope="row">Thứ 7</th>
                        <td>1 - 3</td>
                        <td>31231755 - Thiết kế và lập trình web</td>
                        <td>B3-303</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- 5. MÁY TÍNH ĐIỂM HỌC PHẦN -->
    <section id="tinh-diem" class="card">
        <h2 class="card-title">🧮 Máy tính điểm học phần (PHP)</h2>
        <form method="POST" action="gioithieu.php#tinh-diem" novalidate class="feedback-form">
            <input type="hidden" name="action_tinhdiem" value="1">
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label for="a1">Điểm A1 (20%):</label>
                    <input type="number" step="0.1" id="a1" name="a1" value="<?= e($diemA1) ?>" placeholder="Ví dụ: 8.5">
                </div>
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label for="a2">Điểm A2 (30%):</label>
                    <input type="number" step="0.1" id="a2" name="a2" value="<?= e($diemA2) ?>" placeholder="Ví dụ: 7.0">
                </div>
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label for="a3">Điểm A3 (50%):</label>
                    <input type="number" step="0.1" id="a3" name="a3" value="<?= e($diemA3) ?>" placeholder="Ví dụ: 9.0">
                </div>
            </div>
            <button type="submit" class="btn-submit" style="margin-top: 10px;">Tính điểm</button>
        </form>
        <?php if ($diemTongKet !== null): ?>
            <div style="margin-top: 20px; padding: 15px; background-color: #dcfce7; border-radius: 8px; color: #166534; font-weight: 600;">
                🎉 Điểm tổng kết: <?= number_format($diemTongKet, 2) ?> / 10
            </div>
        <?php endif; ?>
    </section>

    <!-- 6. SỔ LƯU BÚT CÁ NHÂN -->
    <section id="tuong-tac" class="card">
        <h2 class="card-title">💬 Sổ lưu bút cá nhân (Lưu tệp)</h2>
        <?php if ($thongBaoLuuBut !== ''): ?>
            <div style="padding: 12px; background-color: #e0f2fe; border-radius: 6px; color: #0369a1; margin-bottom: 15px;"><?= e($thongBaoLuuBut) ?></div>
        <?php endif; ?>
        <form method="POST" action="gioithieu.php#tuong-tac" novalidate class="feedback-form">
            <input type="hidden" name="action_luubut" value="1">
            <div class="form-group"><input type="text" name="ten" value="<?= e($duLieuLuuBut['ten']) ?>" placeholder="Tên của bạn..."></div>
            <div class="form-group"><textarea name="noidung" rows="3" placeholder="Nhập lời nhắn..."><?= e($duLieuLuuBut['noidung']) ?></textarea></div>
            <button type="submit" class="btn-submit">Gửi lưu bút</button>
        </form>
    </section>

    <!-- 7. HÒM THƯ GÓP Ý & ĐÁNH GIÁ SAO (BẢNG tv_danh_gia) -->
    <section id="danh-gia-trang" class="card">
        <h2 class="card-title">⭐ Hòm thư Góp ý & Đánh giá Sao (Bảng tv_danh_gia)</h2>
        <div style="background: #fdf2f8; border: 1px solid #fbcfe8; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
            <span style="font-size: 1.5rem; font-weight: bold; color: #db2777;"><?= $diemTrungBinh ?> / 5.0 ⭐</span>
            <p style="margin: 5px 0 0 0; color: #831843;">Tổng hợp từ <strong><?= $tongLuotDanhGia ?></strong> lượt đánh giá qua hàm SQL <code>AVG()</code>.</p>
        </div>

        <?php if ($thongBaoDanhGia !== ''): ?>
            <div style="padding: 12px; background-color: #fce7f3; border-radius: 6px; color: #831843; margin-bottom: 15px;"><?= e($thongBaoDanhGia) ?></div>
        <?php endif; ?>

        <form method="POST" action="gioithieu.php#danh-gia-trang" novalidate class="feedback-form">
            <input type="hidden" name="action_danhgia" value="1">
            <div class="form-group">
                <input type="text" name="nguoi_gui" value="<?= e($duLieuDanhGia['nguoi_gui']) ?>" placeholder="Họ và tên bạn...">
            </div>
            <div class="form-group">
                <select name="so_sao" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <option value="5">⭐⭐⭐⭐⭐ (5 sao - Tuyệt vời)</option>
                    <option value="4">⭐⭐⭐⭐ (4 sao - Rất tốt)</option>
                    <option value="3">⭐⭐⭐ (3 sao - Tạm ổn)</option>
                    <option value="2">⭐⭐ (2 sao - Cần cải thiện)</option>
                    <option value="1">⭐ (1 sao - Kém)</option>
                </select>
            </div>
            <div class="form-group">
                <textarea name="nhan_xet" rows="3" placeholder="Nội dung góp ý..."><?= e($duLieuDanhGia['nhan_xet']) ?></textarea>
            </div>
            <button type="submit" class="btn-submit">Gửi đánh giá</button>
        </form>

        <h4 style="margin-top: 25px; margin-bottom: 12px;">💬 Góp ý từ CSDL:</h4>
        <?php if (empty($danhSachDanhGia)): ?>
            <p style="color: #64748b; font-style: italic;">Chưa có góp ý nào.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($danhSachDanhGia as $dg): ?>
                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px;">
                        <strong><?= e($dg['nguoi_gui']) ?> (<?= (int)$dg['so_sao'] ?> ⭐)</strong>
                        <p style="margin: 4px 0 0 0; color: #334155;"><?= e($dg['nhan_xet']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <div class="back-buttons" style="margin-top: 20px;">
        <a href="<?= $goc ?>index.php" class="btn-back" style="text-decoration: none; color: #1e42a0; font-weight: 500;">&larr; Quay về trang chủ nhóm</a>
    </div>

</main>

<script src="js/canhan.js"></script>

<?php require __DIR__ . '/../../inc/footer.php'; ?>
<?php
// Xử lý khi người dùng gửi lưu bút mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gui_luu_but'])) {
    $ten_nguoi_gui = trim($_POST['ten_nguoi_gui']);
    $noi_dung = trim($_POST['noi_dung']);
    if (!empty($ten_nguoi_gui) && !empty($noi_dung)) {
        $stmt = db()->prepare("INSERT INTO tv_luu_but (masv, ten_nguoi_gui, noi_dung) VALUES (?, ?, ?)");
        $stmt->execute(['3120224094', $ten_nguoi_gui, $noi_dung]);
        header("Location: gioithieu.php");
        exit;
    }
}

// Lấy danh sách lưu bút từ CSDL
$stmt_lb = db()->prepare("SELECT * FROM tv_luu_but WHERE masv = ? ORDER BY thoi_gian DESC");
$stmt_lb->execute(['3120224094']);
$danh_sach_luu_but = $stmt_lb->fetchAll();
?>

<!-- HTML Giao diện Sổ lưu bút -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">📖 Sổ lưu bút (CSDL MySQL)</div>
    <div class="card-body">
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <input type="text" name="ten_nguoi_gui" class="form-control" placeholder="Tên của bạn..." required>
            </div>
            <div class="mb-3">
                <textarea name="noi_dung" class="form-control" rows="3" placeholder="Lời nhắn gửi..." required></textarea>
            </div>
            <button type="submit" name="gui_luu_but" class="btn btn-primary">Gửi lời nhắn</button>
        </form>

        <hr>
        <div class="list-group">
            <?php if (empty($danh_sach_luu_but)): ?>
                <p class="text-muted">Chưa có lời nhắn nào.</p>
            <?php else: ?>
                <?php foreach ($danh_sach_luu_but as $lb): ?>
                    <div class="list-group-item mb-2">
                        <h6 class="mb-1 text-bold"><?= htmlspecialchars($lb['ten_nguoi_gui']) ?> <small class="text-muted float-end"><?= $lb['thoi_gian'] ?></small></h6>
                        <p class="mb-1"><?= nl2br(htmlspecialchars($lb['noi_dung'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
// Xử lý tìm kiếm dự án
$tu_khoa = isset($_GET['tu_khoa']) ? trim($_GET['tu_khoa']) : '';
if ($tu_khoa !== '') {
    $stmt_da = db()->prepare("SELECT * FROM tv_du_an WHERE masv = ? AND (ten_du_an LIKE ? OR cong_nghe LIKE ?)");
    $stmt_da->execute(['3120224094', "%$tu_khoa%", "%$tu_khoa%"]);
} else {
    $stmt_da = db()->prepare("SELECT * FROM tv_du_an WHERE masv = ?");
    $stmt_da->execute(['3120224094']);
}
$danh_sach_du_an = $stmt_da->fetchAll();
?>

<!-- HTML Giao diện Dự án cá nhân -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">🚀 Dự án cá nhân (Tìm kiếm & Lọc CSDL)</div>
    <div class="card-body">
        <!-- Form Tìm kiếm -->
        <form method="GET" class="input-group mb-3">
            <input type="text" name="tu_khoa" class="form-control" placeholder="Tìm theo tên dự án hoặc công nghệ..." value="<?= htmlspecialchars($tu_khoa) ?>">
            <button class="btn btn-outline-success" type="submit">Tìm kiếm</button>
            <?php if ($tu_khoa !== ''): ?>
                <a href="gioithieu.php" class="btn btn-outline-secondary">Đặt lại</a>
            <?php endif; ?>
        </form>

        <!-- Danh sách dự án -->
        <div class="row">
            <?php if (empty($danh_sach_du_an)): ?>
                <p class="text-muted">Không tìm thấy dự án phù hợp.</p>
            <?php else: ?>
                <?php foreach ($danh_sach_du_an as $da): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><?= htmlspecialchars($da['ten_du_an']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($da['mo_ta']) ?></p>
                                <span class="badge bg-info text-dark">Công nghệ: <?= htmlspecialchars($da['cong_nghe']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>