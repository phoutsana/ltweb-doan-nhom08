<?php
/**
 * Tệp: Trang cá nhân của KEOPHOXAY Phaeva (3120224182)
 * Chức năng 1: Quản lý kỹ năng từ CSDL (Hiển thị, Lọc GET, Thêm và Xóa bằng POST/PRG).
 * Chức năng 2: Máy tính điểm học phần tính toán, kiểm tra 0-10 và lưu kết quả trực tiếp vào CSDL bảng tv_diem_hoc_phan.
 */

require_once __DIR__ . '/../../inc/config.php';

$goc = '../../';
$thongBaoKyNang = '';
$loiKyNang = '';
$loiDiem = '';

// ==========================================
// CHỨC NĂNG 1: THÊM & XÓA KỸ NĂNG (TƯƠNG TÁC CSDL tv_ky_nang)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_them_ky_nang'])) {
    $tieuDeKyNang = trim($_POST['tieu_de'] ?? '');
    $moTaKyNang = trim($_POST['mo_ta'] ?? '');
    $mucKyNang = trim($_POST['muc'] ?? 'lap-trinh');

    if ($tieuDeKyNang === '' || $moTaKyNang === '') {
        $loiKyNang = 'Vui lòng nhập đầy đủ tiêu đề và mô tả kỹ năng!';
    } else {
        try {
            $stmt = db()->prepare("INSERT INTO tv_ky_nang (masv, tieu_de, mo_ta, muc) VALUES (?, ?, ?, ?)");
            $stmt->execute(['3120224182', $tieuDeKyNang, $moTaKyNang, $mucKyNang]);
            $_SESSION['thong_bao_kn'] = 'Thêm kỹ năng thành công!';
        } catch (PDOException $e) {
            $_SESSION['loi_kn'] = 'Lỗi cơ sở dữ liệu khi thêm kỹ năng.';
        }
        header('Location: gioithieu.php#ky-nang');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_xoa_ky_nang'])) {
    $idXoa = filter_input(INPUT_POST, 'id_ky_nang', FILTER_VALIDATE_INT);
    if ($idXoa) {
        try {
            $stmt = db()->prepare("DELETE FROM tv_ky_nang WHERE id = ? AND masv = ?");
            $stmt->execute([$idXoa, '3120224182']);
            $_SESSION['thong_bao_kn'] = 'Đã xóa kỹ năng thành công!';
        } catch (PDOException $e) {
            $_SESSION['loi_kn'] = 'Không thể xóa kỹ năng.';
        }
    }
    header('Location: gioithieu.php#ky-nang');
    exit;
}

if (isset($_SESSION['thong_bao_kn'])) {
    $thongBaoKyNang = $_SESSION['thong_bao_kn'];
    unset($_SESSION['thong_bao_kn']);
}
if (isset($_SESSION['loi_kn'])) {
    $loiKyNang = $_SESSION['loi_kn'];
    unset($_SESSION['loi_kn']);
}

// Lọc kỹ năng theo GET
$boLoc = $_GET['loc'] ?? 'tat-ca';
$cacBoLocHopLe = ['tat-ca', 'lap-trinh', 'cong-cu', 'ky-nang-mem'];
if (!in_array($boLoc, $cacBoLocHopLe, true)) {
    $boLoc = 'tat-ca';
}

try {
    if ($boLoc === 'tat-ca') {
        $stmt = db()->prepare("SELECT * FROM tv_ky_nang WHERE masv = ? ORDER BY id DESC");
        $stmt->execute(['3120224182']);
    } else {
        $stmt = db()->prepare("SELECT * FROM tv_ky_nang WHERE masv = ? AND muc = ? ORDER BY id DESC");
        $stmt->execute(['3120224182', $boLoc]);
    }
    $kyNangHienThi = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $kyNangHienThi = [];
}

// ==========================================
// CHỨC NĂNG 2: MÁY TÍNH ĐIỂM (LƯU TRỰC TIẾP VÀO CSDL tv_diem_hoc_phan)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_tinh_diem'])) {
    $a1 = filter_input(INPUT_POST, 'a1', FILTER_VALIDATE_FLOAT);
    $a2 = filter_input(INPUT_POST, 'a2', FILTER_VALIDATE_FLOAT);
    $a3 = filter_input(INPUT_POST, 'a3', FILTER_VALIDATE_FLOAT);

    if (
        $a1 === false || $a1 === null ||
        $a2 === false || $a2 === null ||
        $a3 === false || $a3 === null ||
        $a1 < 0 || $a1 > 10 ||
        $a2 < 0 || $a2 > 10 ||
        $a3 < 0 || $a3 > 10
    ) {
        $loiDiem = 'Lỗi: Vui lòng nhập cả 3 điểm hợp lệ từ 0 đến 10!';
    } else {
        $diemTK = round((0.2 * $a1) + (0.3 * $a2) + (0.5 * $a3), 1);
        
        try {
            $stmtDiem = db()->prepare("INSERT INTO tv_diem_hoc_phan (masv, diem_a1, diem_a2, diem_a3, diem_tong_ket) VALUES (?, ?, ?, ?, ?)");
            $stmtDiem->execute(['3120224182', $a1, $a2, $a3, $diemTK]);
            $_SESSION['thong_bao_diem'] = 'Tính và lưu điểm tổng kết thành công: ' . $diemTK;
        } catch (PDOException $e) {
            $_SESSION['loi_diem_db'] = 'Không thể lưu điểm vào CSDL.';
        }

        header('Location: gioithieu.php?loc=' . urlencode($boLoc) . '#diem-hoc-phan');
        exit;
    }
}

$thongBaoDiem = $_SESSION['thong_bao_diem'] ?? null;
unset($_SESSION['thong_bao_diem']);

try {
    $stmtLsDiem = db()->prepare("SELECT * FROM tv_diem_hoc_phan WHERE masv = ? ORDER BY id DESC LIMIT 5");
    $stmtLsDiem->execute(['3120224182']);
    $lichSuDiem = $stmtLsDiem->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $lichSuDiem = [];
}

$tieuDe = 'Giới thiệu cá nhân - KEOPHOXAY Phaeva';
require_once __DIR__ . '/../../inc/header.php';
?>

<script>
(function () {
    const cssTrangCaNhan = `
        .ph-header { background: linear-gradient(135deg, #1d3383, #112059); color: #fff; text-align: center; padding: 30px 15px; margin-top: 20px; border-radius: 8px 8px 0 0; }
        .ph-nav { background: #112059; padding: 12px; display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; border-radius: 0 0 8px 8px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,.1); }
        .ph-nav a { color: #fff; text-decoration: none; padding: 8px 15px; font-weight: bold; border-radius: 20px; transition: background .2s; font-size: 15px; }
        .ph-nav a:hover { background: rgba(255,255,255,.2); }
        .ph-btn-loc { background: #e2e8f0; border: 0; padding: 8px 15px; border-radius: 20px; cursor: pointer; text-decoration: none; color: #334155; font-weight: bold; margin: 5px; display: inline-block; transition: all .2s; }
        .ph-btn-loc.active { background: #1d3383; color: #fff; }
        .ph-btn-loc:hover { background: #cbd5e1; }
        .form-diem, .form-them-kn { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; max-width: 500px; margin: 0 auto 20px auto; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #1d3383; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { background: #1d3383; color: #fff; padding: 10px 20px; border: 0; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; }
        .btn-submit:hover { background: #112059; }
        .btn-xoa { background: #dc2626; color: #fff; border: 0; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-xoa:hover { background: #b91c1c; }
        .alert-error { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-weight: bold; text-align: center; }
        .alert-success { color: #15803d; background: #dcfce7; padding: 15px; border-radius: 4px; margin-bottom: 15px; font-weight: bold; text-align: center; font-size: 16px; border: 1px solid #bbf7d0; }
        @media (max-width: 600px) { .ph-nav { gap: 6px; } .ph-nav a { padding: 8px 10px; } }
    `;
    const styleTag = document.createElement('style');
    styleTag.textContent = cssTrangCaNhan;
    document.head.appendChild(styleTag);
}());
</script>

<main class="noi-dung-chinh" style="max-width: 1000px; margin: 0 auto;">
    <header class="ph-header">
        <h1 style="font-size: 28px; margin: 0; color: white;">Trang giới thiệu cá nhân</h1>
        <p style="margin-top: 8px; font-style: italic;">Sinh viên Công nghệ Thông tin - Đam mê Thiết kế &amp; Lập trình Web</p>
    </header>

    <nav class="ph-nav" aria-label="Điều hướng trang cá nhân">
        <a href="#gioi-thieu">Giới thiệu</a>
        <a href="#ky-nang">Kỹ năng</a>
        <a href="#diem-hoc-phan">Tính điểm</a>
        <a href="#thoi-khoa-bieu">Lịch học</a>
    </nav>

    <section id="gioi-thieu" class="khoi-noi-dung" style="display: flex; flex-wrap: wrap; gap: 30px; align-items: flex-start;">
        <figure style="margin: 0; max-width: 250px;">
            <img src="../../images/avatar-phaeva.jpg" alt="Ảnh chân dung của KEOPHOXAY Phaeva" style="width: 100%; border-radius: 8px; border: 4px solid #1d3383;">
        </figure>
        <div class="thong-tin-chi-tiet" style="flex: 1; min-width: 250px;">
            <h2 style="color: #1d3383;">Giới thiệu bản thân</h2>
            <p><strong>Họ và tên:</strong> KEOPHOXAY Phaeva</p>
            <p><strong>Ngành học:</strong> Công nghệ Thông tin</p>
            <p><strong>Trường:</strong> Trường Đại học Sư phạm – Đại học Đà Nẵng</p>
            <p><strong>Vai trò trong nhóm:</strong> Thiết kế giao diện &amp; Hỗ trợ code</p>
            <p><strong>Sở thích:</strong> Nghe nhạc, đọc sách và tìm hiểu các công nghệ mới liên quan đến Frontend.</p>
        </div>
    </section>

    <!-- CHỨC NĂNG 1: QUẢN LÝ KỸ NĂNG (LỌC, THÊM, XÓA TỪ CSDL) -->
    <section id="ky-nang" class="khoi-noi-dung">
        <h2 style="color: #1d3383; text-align: center;">Chức năng 1: Quản lý Kỹ năng (CSDL tv_ky_nang)</h2>

        <?php if ($thongBaoKyNang !== ''): ?>
            <div class="alert-success"><?= e($thongBaoKyNang) ?></div>
        <?php endif; ?>
        <?php if ($loiKyNang !== ''): ?>
            <div class="alert-error"><?= e($loiKyNang) ?></div>
        <?php endif; ?>

        <div class="form-them-kn">
            <h3 style="color: #1d3383; font-size: 16px; margin-top: 0;">Thêm kỹ năng mới vào CSDL</h3>
            <form action="gioithieu.php#ky-nang" method="POST">
                <div class="form-group">
                    <label for="tieu_de">Tiêu đề kỹ năng:</label>
                    <input type="text" id="tieu_de" name="tieu_de" required placeholder="Ví dụ: Lập trình PHP nâng cao">
                </div>
                <div class="form-group">
                    <label for="mo_ta">Mô tả chi tiết:</label>
                    <textarea id="mo_ta" name="mo_ta" rows="2" required placeholder="Mô tả kỹ năng..."></textarea>
                </div>
                <div class="form-group">
                    <label for="muc">Danh mục:</label>
                    <select id="muc" name="muc">
                        <option value="lap-trinh">Lập trình</option>
                        <option value="cong-cu">Công cụ</option>
                        <option value="ky-nang-mem">Kỹ năng mềm</option>
                    </select>
                </div>
                <button type="submit" name="btn_them_ky_nang" class="btn-submit">Lưu Kỹ Năng Vào CSDL</button>
            </form>
        </div>

        <div style="margin-bottom: 20px; text-align: center;">
            <a href="gioithieu.php?loc=tat-ca#ky-nang" class="ph-btn-loc <?= $boLoc === 'tat-ca' ? 'active' : '' ?>">Tất cả</a>
            <a href="gioithieu.php?loc=lap-trinh#ky-nang" class="ph-btn-loc <?= $boLoc === 'lap-trinh' ? 'active' : '' ?>">Lập trình</a>
            <a href="gioithieu.php?loc=cong-cu#ky-nang" class="ph-btn-loc <?= $boLoc === 'cong-cu' ? 'active' : '' ?>">Công cụ</a>
            <a href="gioithieu.php?loc=ky-nang-mem#ky-nang" class="ph-btn-loc <?= $boLoc === 'ky-nang-mem' ? 'active' : '' ?>">Kỹ năng mềm</a>
        </div>

        <?php if (empty($kyNangHienThi)): ?>
            <p style="text-align: center; color: #ef4444; font-style: italic;">Không tìm thấy kỹ năng nào trong nhóm này.</p>
        <?php else: ?>
            <ul style="line-height: 1.8; padding-left: 0; max-width: 600px; margin: 0 auto;">
                <?php foreach ($kyNangHienThi as $kn): ?>
                    <li style="margin-bottom: 10px; padding: 12px; background: #f8fafc; border-left: 4px solid #1d3383; border-radius: 4px; list-style-type: none; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="color: #1d3383;"><?= e($kn['tieu_de']) ?>:</strong>
                            <span><?= e($kn['mo_ta']) ?></span>
                        </div>
                        <form action="gioithieu.php#ky-nang" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa kỹ năng này?');" style="margin: 0;">
                            <input type="hidden" name="id_ky_nang" value="<?= (int) $kn['id'] ?>">
                            <button type="submit" name="btn_xoa_ky_nang" class="btn-xoa">Xóa</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <!-- CHỨC NĂNG 2: MÁY TÍNH ĐIỂM HỌC PHẦN (LƯU VÀO CSDL tv_diem_hoc_phan) -->
    <section id="diem-hoc-phan" class="khoi-noi-dung">
        <h2 style="color: #1d3383; text-align: center;">Chức năng 2: Máy tính & Lưu điểm học phần (CSDL)</h2>
        <div class="form-diem">
            <p style="text-align: center; margin-bottom: 20px; font-style: italic;">Công thức: 0.2 × A1 + 0.3 × A2 + 0.5 × A3</p>

            <?php if ($loiDiem !== ''): ?>
                <div class="alert-error"><?= e($loiDiem) ?></div>
            <?php endif; ?>

            <?php if ($thongBaoDiem !== null): ?>
                <div class="alert-success">🎉 <?= e($thongBaoDiem) ?></div>
            <?php endif; ?>

            <form action="gioithieu.php?loc=<?= e(urlencode($boLoc)) ?>#diem-hoc-phan" method="POST">
                <div class="form-group">
                    <label for="a1">Điểm Chuyên cần &amp; Bài tập (A1 - 20%):</label>
                    <input type="number" id="a1" name="a1" step="0.1" min="0" max="10" required placeholder="Nhập điểm từ 0 đến 10">
                </div>
                <div class="form-group">
                    <label for="a2">Điểm Giữa kỳ (A2 - 30%):</label>
                    <input type="number" id="a2" name="a2" step="0.1" min="0" max="10" required placeholder="Nhập điểm từ 0 đến 10">
                </div>
                <div class="form-group">
                    <label for="a3">Điểm Cuối kỳ (A3 - 50%):</label>
                    <input type="number" id="a3" name="a3" step="0.1" min="0" max="10" required placeholder="Nhập điểm từ 0 đến 10">
                </div>
                <button type="submit" name="btn_tinh_diem" class="btn-submit">TÍNH VÀ LƯU ĐIỂM VÀO CSDL</button>
            </form>

            <?php if (!empty($lichSuDiem)): ?>
                <div style="margin-top: 25px;">
                    <h3 style="color: #1d3383; font-size: 14px; margin-bottom: 10px;">Lịch sử tính điểm gần đây (từ CSDL):</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #1d3383; color: white;">
                                <th style="padding: 6px; border: 1px solid #cbd5e1;">A1</th>
                                <th style="padding: 6px; border: 1px solid #cbd5e1;">A2</th>
                                <th style="padding: 6px; border: 1px solid #cbd5e1;">A3</th>
                                <th style="padding: 6px; border: 1px solid #cbd5e1;">Tổng kết</th>
                                <th style="padding: 6px; border: 1px solid #cbd5e1;">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lichSuDiem as $ls): ?>
                                <tr>
                                    <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;"><?= e((string)$ls['diem_a1']) ?></td>
                                    <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;"><?= e((string)$ls['diem_a2']) ?></td>
                                    <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;"><?= e((string)$ls['diem_a3']) ?></td>
                                    <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #15803d;"><?= e((string)$ls['diem_tong_ket']) ?></td>
                                    <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: center; font-size: 11px;"><?= e((string)$ls['ngay_tinh']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- THỜI KHÓA BIỂU -->
    <section id="thoi-khoa-bieu" class="khoi-noi-dung">
        <h2 style="color: #1d3383;">Thời khóa biểu học kỳ này</h2>
        <div class="hop-bang">
            <table style="width: 100%; min-width: 600px;">
                <caption style="text-align: left; padding: 10px 0; font-weight: bold;">Lịch học hàng tuần của Phaeva</caption>
                <thead style="background-color: #1d3383; color: white;">
                    <tr>
                        <th scope="col">Buổi</th><th scope="col">Thứ 2</th><th scope="col">Thứ 3</th>
                        <th scope="col">Thứ 4</th><th scope="col">Thứ 5</th><th scope="col">Thứ 6</th>
                        <th scope="col">Thứ 7</th><th scope="col">CN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><th scope="row">Sáng</th><td>-</td><td>An toàn thông tin</td><td>-</td><td>Khai phá dữ liệu</td><td>Thiết kế &amp; Lập trình Web</td><td>-</td><td>-</td></tr>
                    <tr><th scope="row">Chiều</th><td>-</td><td>Hệ quản trị CSDL<br>Lịch sử Đảng</td><td>-</td><td>Công nghệ phần mềm</td><td>-</td><td>-</td><td>-</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <div style="text-align: center; margin: 30px 0;">
        <a href="../../index.php" style="display: inline-block; margin: 5px; padding: 10px 20px; background: #1d3383; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">Quay về trang chủ nhóm</a>
        <a href="../../gioi-thieu.php" style="display: inline-block; margin: 5px; padding: 10px 20px; background: #64748b; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">Quay về trang giới thiệu nhóm</a>
    </div>
</main>

<script src="js/canhan.js"></script>
<?php require_once __DIR__ . '/../../inc/footer.php'; ?>