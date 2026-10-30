<?php

/**
 * Tệp: Trang cá nhân của Xayyatiew Phoutsana (3120224184)
 * Chức năng 1 (Sáng/Tối): Dùng cookie để nhớ giao diện.
 * Chức năng 2 (CSDL): Quản lý dự án cá nhân (Thêm, Xóa, Hiển thị) từ bảng tv_du_an và Sổ lưu bút từ tv_luu_but.
 */
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/db.php';
$goc = '../../';

// ==========================================
// 1. CHỨC NĂNG 1: GIAO DIỆN SÁNG / TỐI (COOKIE)
// ==========================================
if (isset($_GET['doi_giao_dien'])) {
    $hienTai = $_COOKIE['giao_dien_ca_nhan'] ?? 'light';
    $moi = ($hienTai === 'light') ? 'dark' : 'light';
    setcookie('giao_dien_ca_nhan', $moi, time() + 86400 * 30, '/');
    header('Location: gioithieu.php');
    exit;
}
$giaoDien = $_COOKIE['giao_dien_ca_nhan'] ?? 'light';

// ==========================================
// 2. XỬ LÝ THÊM / XÓA DỰ ÁN CÁ NHÂN (BẢNG tv_du_an)
// ==========================================
$loiDuAn = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_du_an'])) {
    $tenDuAn = trim($_POST['ten_du_an'] ?? '');
    $moTa = trim($_POST['mo_ta'] ?? '');
    $congNghe = trim($_POST['cong_nghe'] ?? '');

    if (mb_strlen($tenDuAn) < 3) $loiDuAn['ten'] = 'Tên dự án phải từ 3 ký tự trở lên.';

    if (empty($loiDuAn)) {
        $stmt = db()->prepare("INSERT INTO tv_du_an (masv, ten_du_an, mo_ta, cong_nghe) VALUES ('3120224184', ?, ?, ?)");
        $stmt->execute([$tenDuAn, $moTa, $congNghe]);
        header('Location: gioithieu.php?thanhcong_duan=1#du-an');
        exit;
    }
}

// Xử lý Xóa dự án (Kiểm tra quyền phía máy chủ)
if (isset($_GET['xoa_du_an'])) {
    $idDuAn = intval($_GET['xoa_du_an']);
    // Kiểm tra xem dự án có đúng của sinh viên này không trước khi xóa để đảm bảo bảo mật
    $stmtCheck = db()->prepare("SELECT * FROM tv_du_an WHERE id = ? AND masv = '3120224184'");
    $stmtCheck->execute([$idDuAn]);
    if ($stmtCheck->fetch()) {
        $stmtDel = db()->prepare("DELETE FROM tv_du_an WHERE id = ?");
        $stmtDel->execute([$idDuAn]);
    }
    header('Location: gioithieu.php#du-an');
    exit;
}

// ==========================================
// 3. XỬ LÝ SỔ LƯU BÚT (BẢNG tv_luu_but)
// ==========================================
$loiLuuBut = [];
$thongBaoLuuBut = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gui_luu_but'])) {
    $ten = trim($_POST['ten'] ?? '');
    $loiNhan = trim($_POST['loi_nhan'] ?? '');

    if (mb_strlen($ten) < 2) $loiLuuBut['ten'] = 'Tên phải có ít nhất 2 ký tự.';
    if (mb_strlen($loiNhan) < 5) $loiLuuBut['loi_nhan'] = 'Lời nhắn quá ngắn (ít nhất 5 ký tự).';

    if (empty($loiLuuBut)) {
        $stmt = db()->prepare("INSERT INTO tv_luu_but (masv, ten, loi_nhan) VALUES ('3120224184', ?, ?)");
        $stmt->execute([$ten, $loiNhan]);
        header('Location: gioithieu.php?thanhcong_luubut=1#luu-but');
        exit;
    }
}

if (isset($_GET['thanhcong_luubut']) && $_GET['thanhcong_luubut'] == 1) {
    $thongBaoLuuBut = 'Cảm ơn bạn đã để lại lời nhắn!';
}

// Lấy danh sách từ CSDL
$danhSachLuuBut = [];
try {
    $stmtLuuBut = db()->query("SELECT * FROM tv_luu_but WHERE masv = '3120224184' ORDER BY id DESC LIMIT 5");
    $danhSachLuuBut = $stmtLuuBut->fetchAll();
} catch (Exception $e) {
    $danhSachLuuBut = [];
}

$danhSachDuAn = [];
try {
    $stmtDuAn = db()->query("SELECT * FROM tv_du_an WHERE masv = '3120224184' ORDER BY id DESC");
    $danhSachDuAn = $stmtDuAn->fetchAll();
} catch (Exception $e) {
    $danhSachDuAn = [];
}

$tieuDe = 'Giới thiệu cá nhân - Xayyatiew Phoutsana';
require_once __DIR__ . '/../../inc/header.php';
?>

<link rel="stylesheet" href="style-canhan.css">
<script>
    const cssBoSung = `
        body.che-do-toi { background-color: #0f172a !important; color: #e2e8f0 !important; transition: all 0.3s ease; }
        body.che-do-toi .khoi-noi-dung { background-color: #1e293b !important; color: #e2e8f0 !important; border: 1px solid #334155 !important; box-shadow: none !important; }
        body.che-do-toi h2 { color: #93c5fd !important; border-left-color: #3b82f6 !important; }
        body.che-do-toi .hop-bang table, body.che-do-toi .hop-bang th, body.che-do-toi .hop-bang td { background-color: #1e293b !important; color: white !important; border-color: #475569 !important; }
        body.che-do-toi th { background-color: #1e40af !important; }
        body.che-do-toi .cn-nav { background-color: #0f172a !important; border: 1px solid #334155 !important; }
        
        .cn-header { background: linear-gradient(135deg, var(--mau-chinh-dam, #1e3a8a), var(--mau-chinh, #2563eb)); color: white; text-align: center; padding: 30px 15px; margin-top: 20px; border-radius: 8px 8px 0 0; }
        .cn-nav { background-color: var(--mau-chinh-dam, #1e40af); padding: 12px; display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; border-radius: 0 0 8px 8px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .cn-nav a { color: var(--mau-trang, white); text-decoration: none; padding: 8px 15px; font-weight: bold; border-radius: 20px; transition: background 0.2s, color 0.2s; font-size: 15px; }
        .cn-nav a:hover { background-color: rgba(255,255,255,0.15); color: #bfdbfe; }
        .cn-btn-darkmode { background-color: rgba(255,255,255,0.12) !important; color: #fff !important; border: 1px solid rgba(255,255,255,0.3) !important; }
        .cn-btn-darkmode:hover { background-color: rgba(255,255,255,0.25) !important; }
        
        .form-nhap { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit; }
        body.che-do-toi .form-nhap { background: #0f172a; border-color: #475569; color: #fff; }
    `;
    const styleTag = document.createElement('style');
    styleTag.innerHTML = cssBoSung;
    document.head.appendChild(styleTag);

    <?php if ($giaoDien === 'dark'): ?>
        document.body.classList.add('che-do-toi');
    <?php endif; ?>
</script>

<main class="noi-dung-chinh" style="max-width: 1000px; margin: 0 auto;">

    <header class="cn-header">
        <h1 style="font-size: 28px; margin: 0; color: white;">Trang giới thiệu cá nhân</h1>
        <p style="margin-top: 8px; font-style: italic;">Sinh viên Công nghệ Thông tin - Đam mê Lập trình Web & IoT</p>
    </header>

    <nav class="cn-nav">
        <a href="#gioi-thieu">Giới thiệu</a>
        <a href="#so-thich">Sở thích</a>
        <a href="#du-an">Dự án</a>
        <a href="#ky-nang">Kỹ năng</a>
        <a href="#thoi-khoa-bieu">Lịch học</a>
        <a href="#luu-but">Lưu bút</a>
        <a href="gioithieu.php?doi_giao_dien=1" class="cn-btn-darkmode">
            <?= $giaoDien === 'dark' ? '☀️ Đổi sang Sáng' : '🌙 Đổi sang Tối' ?>
        </a>
    </nav>

    <!-- Giới thiệu bản thân -->
    <section id="gioi-thieu" class="khoi-noi-dung" style="display: flex; flex-wrap: wrap; gap: 30px; align-items: flex-start;">
        <figure style="margin: 0; max-width: 250px;">
            <img src="../../images/avatar-phoutsana.jpg" alt="Ảnh chân dung" style="width: 100%; border-radius: 50%; border: 4px solid #2563eb;">
        </figure>
        <div class="thong-tin-chi-tiet" style="flex: 1; min-width: 250px;">
            <h2>Giới thiệu bản thân</h2>
            <p><strong>Họ và tên:</strong> Xayyatiew Phoutsana</p>
            <p><strong>Mã sinh viên:</strong> 3120224184</p>
            <p><strong>Ngành học:</strong> Công nghệ Thông tin (Lớp 24CNTT2)</p>
            <p><strong>Trường:</strong> Trường Đại học Sư phạm – Đại học Đà Nẵng</p>
            <p><strong>Quê quán:</strong> SeKong, Lào</p>
        </div>
    </section>

    <!-- CHỨC NĂNG DỰ ÁN CÁ NHÂN (THÊM & XÓA) -->
    <section id="du-an" class="khoi-noi-dung">
        <h2>Quản lý Dự án cá nhân (Bảng tv_du_an)</h2>

        <?php if (isset($_GET['thanhcong_duan'])): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 12px; border-left: 4px solid #10b981; margin-bottom: 20px; border-radius: 4px;">
                Thêm dự án thành công!
            </div>
        <?php endif; ?>

        <!-- Form Thêm Dự Án Mới -->
        <form method="POST" action="gioithieu.php#du-an" style="background: rgba(0,0,0,0.02); padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <h3 style="font-size: 1.1em; margin-bottom: 10px;">Thêm dự án mới</h3>
            <div style="margin-bottom: 10px;">
                <label><strong>Tên dự án (*):</strong></label>
                <input type="text" name="ten_du_an" value="<?= e($_POST['ten_du_an'] ?? '') ?>" class="form-nhap">
                <?php if (isset($loiDuAn['ten'])): ?>
                    <div style="color: #ef4444; font-size: 13px; margin-top: 3px;"><?= e($loiDuAn['ten']) ?></div>
                <?php endif; ?>
            </div>
            <div style="margin-bottom: 10px;">
                <label><strong>Mô tả:</strong></label>
                <textarea name="mo_ta" rows="2" class="form-nhap"><?= e($_POST['mo_ta'] ?? '') ?></textarea>
            </div>
            <div style="margin-bottom: 10px;">
                <label><strong>Công nghệ sử dụng:</strong></label>
                <input type="text" name="cong_nghe" value="<?= e($_POST['cong_nghe'] ?? '') ?>" class="form-nhap">
            </div>
            <button type="submit" name="them_du_an" style="background: #2563eb; color: white; padding: 8px 16px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Thêm dự án</button>
        </form>

        <!-- Bảng Hiển Thị Danh Sách & Nút Xóa -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #ccc;">
                <thead>
                    <tr style="background: #2563eb; color: white;">
                        <th style="padding: 10px;">STT</th>
                        <th style="padding: 10px;">Tên dự án</th>
                        <th style="padding: 10px;">Mô tả</th>
                        <th style="padding: 10px;">Công nghệ</th>
                        <th style="padding: 10px; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($danhSachDuAn)): ?>
                        <tr>
                            <td colspan="5" style="padding: 15px; text-align: center;">Chưa có dự án nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($danhSachDuAn as $index => $da): ?>
                            <tr>
                                <td style="padding: 10px; text-align: center;"><?= $index + 1 ?></td>
                                <td style="padding: 10px;"><strong><?= e($da['ten_du_an']) ?></strong></td>
                                <td style="padding: 10px;"><?= e($da['mo_ta']) ?></td>
                                <td style="padding: 10px;"><?= e($da['cong_nghe']) ?></td>
                                <td style="padding: 10px; text-align: center;">
                                    <a href="gioithieu.php?xoa_du_an=<?= $da['id'] ?>#du-an" onclick="return confirm('Bạn có chắc chắn muốn xóa dự án này không?');" style="background: #ef4444; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 13px;">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Các phần khác giữ nguyên -->
    <article id="so-thich" class="khoi-noi-dung">
        <h2>Dự án tiêu biểu & Sở thích</h2>
        <p>Mình thích áp dụng công nghệ vào thực tiễn. Mình từng phát triển hệ thống gợi ý phim sử dụng thuật toán TF-IDF và Cosine Similarity, cũng như xây dựng nguyên mẫu khóa cửa tự động bằng RFID và Arduino. Ngoài giờ code, mình có sở thích luyện tập kỹ năng thuyết trình, giao tiếp, xem phim, đọc sách và chạy bộ.</p>
    </article>

    <section id="ky-nang" class="khoi-noi-dung">
        <h2>Kỹ năng & Ngoại ngữ</h2>
        <ul style="line-height: 1.8; padding-left: 20px;">
            <li><strong>Lập trình Web:</strong> HTML, CSS, JavaScript, PHP, MySQL, Node.js.</li>
            <li><strong>Phần mềm & IoT:</strong> C++, Java, Python, Arduino, RFID, Servo và LCD.</li>
            <li><strong>Ngoại ngữ:</strong> Tiếng Việt, Tiếng Lào, Tiếng Thái và đang trau dồi Tiếng Anh chuyên ngành IT.</li>
        </ul>
    </section>

    <section id="thoi-khoa-bieu" class="khoi-noi-dung">
        <h2>Thời khóa biểu học kỳ này</h2>
        <div class="hop-bang">
            <table style="width: 100%; min-width: 600px;">
                <caption style="text-align: left; padding: 10px 0; font-weight: bold;">Lịch học hàng tuần của Phoutsana</caption>
                <thead>
                    <tr>
                        <th scope="col">Buổi</th>
                        <th scope="col">Thứ 2</th>
                        <th scope="col">Thứ 3</th>
                        <th scope="col">Thứ 4</th>
                        <th scope="col">Thứ 5</th>
                        <th scope="col">Thứ 6</th>
                        <th scope="col">Thứ 7</th>
                        <th scope="col">CN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Sáng</th>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>Khai phá dữ liệu<br>Hệ phân tán</td>
                        <td>-</td>
                        <td>Thiết kế & Lập trình Web</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <th scope="row">Chiều</th>
                        <td>An toàn thông tin</td>
                        <td>Hệ quản trị CSDL</td>
                        <td>-</td>
                        <td>Công nghệ phần mềm<br>Lịch sử Đảng</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- SỔ LƯU BÚT -->
    <section id="luu-but" class="khoi-noi-dung">
        <h2>Sổ lưu bút (Dữ liệu từ CSDL tv_luu_but)</h2>

        <?php if ($thongBaoLuuBut): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 12px; border-left: 4px solid #10b981; margin-bottom: 20px; border-radius: 4px;">
                <?= e($thongBaoLuuBut) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="gioithieu.php#luu-but" novalidate style="margin-bottom: 30px;">
            <div style="margin-bottom: 15px;">
                <label for="tenLuuBut"><strong>Tên của bạn (*):</strong></label>
                <input type="text" id="tenLuuBut" name="ten" value="<?= e($_POST['ten'] ?? '') ?>" class="form-nhap">
                <?php if (isset($loiLuuBut['ten'])): ?>
                    <div style="color: #ef4444; font-size: 14px; margin-top: 5px;"><?= e($loiLuuBut['ten']) ?></div>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="loiNhan"><strong>Lời nhắn (*):</strong></label>
                <textarea id="loiNhan" name="loi_nhan" rows="3" class="form-nhap"><?= e($_POST['loi_nhan'] ?? '') ?></textarea>
                <?php if (isset($loiLuuBut['loi_nhan'])): ?>
                    <div style="color: #ef4444; font-size: 14px; margin-top: 5px;"><?= e($loiLuuBut['loi_nhan']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" name="gui_luu_but" style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Gửi lời nhắn</button>
        </form>

        <h3 style="font-size: 1.1em; border-bottom: 1px solid #cbd5e1; padding-bottom: 8px; margin-bottom: 15px;">Lời nhắn gần đây:</h3>
        <?php if (empty($danhSachLuuBut)): ?>
            <p style="color: #64748b; font-style: italic;">Chưa có lời nhắn nào.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php foreach ($danhSachLuuBut as $lb): ?>
                    <div style="padding: 12px 15px; border-left: 4px solid #2563eb; background: rgba(0,0,0,0.02); border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <strong style="color: #2563eb;"><?= e($lb['ten']) ?></strong>
                            <small style="color: #64748b;"><?= e($lb['ngay_tao']) ?></small>
                        </div>
                        <p style="margin: 0; line-height: 1.5;"><?= nl2br(e($lb['loi_nhan'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</main>

<div style="text-align: center; margin: 30px 0;">
    <a href="../../index.php" style="display: inline-block; margin: 5px; padding: 10px 20px; background: #64748b; color: white; text-decoration: none; border-radius: 6px;">Quay về trang chủ nhóm</a>
</div>

<?php require_once __DIR__ . '/../../inc/footer.php'; ?>