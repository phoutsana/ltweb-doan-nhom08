<?php
/**
 * Tệp: Trang cá nhân của Xayyatiew Phoutsana (3120224184)
 * Chức năng 1 (Sáng/Tối): Dùng cookie để nhớ giao diện.
 * Chức năng 2 (CSDL): Quản lý dự án cá nhân (Thêm, Xóa, Hiển thị) từ bảng tv_du_an và Sổ lưu bút từ tv_luu_but.
 */
require_once __DIR__ . '/../../inc/config.php';
// BT7_PHOUTSANA_CSRF_GUARD
require_once __DIR__ . '/../../inc/csrf.php';

if (isset($_GET['xoa_du_an'])) {
    header('Allow: POST');
    http_response_code(405);
    exit('405 Method Not Allowed');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    csrf_verify();
}
require_once __DIR__ . '/../../inc/db.php';
$goc = '../../';

// BT7: Bật quyền quản lý/xóa cho chủ trang (phục vụ kiểm thử Phần B)
$coQuyenDuAn = true;

// ==========================================
// 1. CHỨC NĂNG 1: GIAO DIỆN SÁNG / TỐI (COOKIE)
// ==========================================
if (isset($_GET['doi_giao_dien'])) {
    header('Allow: POST');
    http_response_code(405);
    exit('Method Not Allowed');
}

if (
    ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' &&
    isset($_POST['doi_giao_dien'])
) {
    $hienTai = $_COOKIE['giao_dien_ca_nhan'] ?? 'light';
    $moi = $hienTai === 'dark' ? 'light' : 'dark';

    setcookie('giao_dien_ca_nhan', $moi, [
        'expires' => time() + 86400 * 30,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    header('Location: gioithieu.php', true, 303);
    exit;
}
$giaoDien = $_COOKIE['giao_dien_ca_nhan'] ?? 'light';

// ==========================================
// 2. XỬ LÝ THÊM / XÓA DỰ ÁN CÁ NHÂN (BẢNG tv_du_an)
// ==========================================
$loiDuAn = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_du_an'])) {
    if (!$coQuyenDuAn) {
        http_response_code(403);
        exit('Khong co quyen them du an.');
    }
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

// Xử lý Xóa Dự Án
if (
    ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' &&
    isset($_POST['xoa_du_an'])
) {
    if (!$coQuyenDuAn) {
        http_response_code(403);
        exit('Khong co quyen xoa du an.');
    }

    $idDuAn = filter_var(
        $_POST['xoa_du_an'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($idDuAn === false) {
        http_response_code(400);
        exit('ID du an khong hop le.');
    }

    $stmt = db()->prepare(
        'DELETE FROM tv_du_an WHERE id = ? AND masv = ?'
    );
    $stmt->execute([$idDuAn, '3120224184']);

    header('Location: gioithieu.php#du-an', true, 303);
    exit;
}

// ==========================================
// 3. XỬ LÝ SỔ LƯU BÚT (BẢNG tv_luu_but & NÚT XÓA LƯU BÚT)
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

// Xử lý Xóa Lưu Bút
if (
    ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' &&
    isset($_POST['xoa_luu_but'])
) {
    if (!$coQuyenDuAn) {
        http_response_code(403);
        exit('Không có quyền xóa lưu bút.');
    }

    $idLuuBut = filter_var(
        $_POST['xoa_luu_but'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($idLuuBut !== false) {
        $stmtDelLB = db()->prepare('DELETE FROM tv_luu_but WHERE id = ? AND masv = ?');
        $stmtDelLB->execute([$idLuuBut, '3120224184']);
    }

    header('Location: gioithieu.php#luu-but', true, 303);
    exit;
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
$cssRieng = 'thanhvien/3120224184_phoutsana/bt7-csp.css';
require_once __DIR__ . '/../../inc/header.php';
?>

<script src="bt7-csp.js" defer></script>

<main class="noi-dung-chinh" data-bt7-theme="<?= e($giaoDien) ?>" data-bt7-pt="pt001">

    <header class="cn-header">
        <h1 data-bt7-pt="pt002">Trang giới thiệu cá nhân</h1>
        <p data-bt7-pt="pt003">Sinh viên Công nghệ Thông tin - Đam mê Lập trình Web & IoT</p>
    </header>

    <nav class="cn-nav">
        <a href="#gioi-thieu">Giới thiệu</a>
        <a href="#so-thich">Sở thích</a>
        <a href="#du-an">Dự án</a>
        <a href="#ky-nang">Kỹ năng</a>
        <a href="#thoi-khoa-bieu">Lịch học</a>
        <a href="#luu-but">Lưu bút</a>
        <form method="post" action="gioithieu.php" class="bt7-theme-form">
            <?= csrf_field() ?>
            <button type="submit" name="doi_giao_dien" value="1" class="cn-btn-darkmode">
                <?= $giaoDien === 'dark' ? '☀️ Đổi sang Sáng' : '🌙 Đổi sang Tối' ?>
            </button>
        </form>
    </nav>

    <!-- Giới thiệu bản thân -->
    <section id="gioi-thieu" class="khoi-noi-dung" data-bt7-pt="pt004">
        <figure data-bt7-pt="pt005">
            <img src="../../images/avatar-phoutsana.jpg" alt="Ảnh chân dung" data-bt7-pt="pt006">
        </figure>
        <div class="thong-tin-chi-tiet" data-bt7-pt="pt007">
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
            <div data-bt7-pt="pt008">
                Thêm dự án thành công!
            </div>
        <?php endif; ?>

        <!-- Form Thêm Dự Án Mới -->
        <form method="POST" action="gioithieu.php#du-an" data-bt7-pt="pt009">
            <?= csrf_field() ?>
            <h3 data-bt7-pt="pt010">Thêm dự án mới</h3>
            <div data-bt7-pt="pt011">
                <label><strong>Tên dự án (*):</strong></label>
                <input type="text" name="ten_du_an" value="<?= e($_POST['ten_du_an'] ?? '') ?>" class="form-nhap">
                <?php if (isset($loiDuAn['ten'])): ?>
                    <div data-bt7-pt="pt012"><?= e($loiDuAn['ten']) ?></div>
                <?php endif; ?>
            </div>
            <div data-bt7-pt="pt013">
                <label><strong>Mô tả:</strong></label>
                <textarea name="mo_ta" rows="2" class="form-nhap"><?= e($_POST['mo_ta'] ?? '') ?></textarea>
            </div>
            <div data-bt7-pt="pt014">
                <label><strong>Công nghệ sử dụng:</strong></label>
                <input type="text" name="cong_nghe" value="<?= e($_POST['cong_nghe'] ?? '') ?>" class="form-nhap">
            </div>
            <button type="submit" name="them_du_an" data-bt7-pt="pt015">Thêm dự án</button>
        </form>

        <!-- Bảng Hiển Thị Danh Sách & Nút Xóa -->
        <div data-bt7-pt="pt016">
            <table data-bt7-pt="pt017">
                <thead>
                    <tr data-bt7-pt="pt018">
                        <th data-bt7-pt="pt019">STT</th>
                        <th data-bt7-pt="pt020">Tên dự án</th>
                        <th data-bt7-pt="pt021">Mô tả</th>
                        <th data-bt7-pt="pt022">Công nghệ</th>
                        <th data-bt7-pt="pt023">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($danhSachDuAn)): ?>
                        <tr>
                            <td colspan="5" data-bt7-pt="pt024">Chưa có dự án nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($danhSachDuAn as $index => $da): ?>
                            <tr>
                                <td data-bt7-pt="pt025"><?= $index + 1 ?></td>
                                <td data-bt7-pt="pt026"><strong><?= e($da['ten_du_an']) ?></strong></td>
                                <td data-bt7-pt="pt027"><?= e($da['mo_ta']) ?></td>
                                <td data-bt7-pt="pt028"><?= e($da['cong_nghe']) ?></td>
                                <td data-bt7-pt="pt029">
                                    <?php if ($coQuyenDuAn): ?>
                                        <form method="post" action="gioithieu.php#du-an" data-bt7-confirm-delete style="display:inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="xoa_du_an" value="<?= (int) $da['id'] ?>">
                                            <button type="submit" class="bt7-delete-project-btn">Xóa</button>
                                        </form>
                                    <?php endif; ?>
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
        <ul data-bt7-pt="pt030">
            <li><strong>Lập trình Web:</strong> HTML, CSS, JavaScript, PHP, MySQL, Node.js.</li>
            <li><strong>Phần mềm & IoT:</strong> C++, Java, Python, Arduino, RFID, Servo và LCD.</li>
            <li><strong>Ngoại ngữ:</strong> Tiếng Việt, Tiếng Lào, Tiếng Thái và đang trau dồi Tiếng Anh chuyên ngành IT.</li>
        </ul>
    </section>

    <section id="thoi-khoa-bieu" class="khoi-noi-dung">
        <h2>Thời khóa biểu học kỳ này</h2>
        <div class="hop-bang">
            <table data-bt7-pt="pt031">
                <caption data-bt7-pt="pt032">Lịch học hàng tuần của Phoutsana</caption>
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
            <div data-bt7-pt="pt033">
                <?= e($thongBaoLuuBut) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="gioithieu.php#luu-but" novalidate data-bt7-pt="pt034">
            <?= csrf_field() ?>
            <div data-bt7-pt="pt035">
                <label for="tenLuuBut"><strong>Tên của bạn (*):</strong></label>
                <input type="text" id="tenLuuBut" name="ten" value="<?= e($_POST['ten'] ?? '') ?>" class="form-nhap">
                <?php if (isset($loiLuuBut['ten'])): ?>
                    <div data-bt7-pt="pt036"><?= e($loiLuuBut['ten']) ?></div>
                <?php endif; ?>
            </div>

            <div data-bt7-pt="pt037">
                <label for="loiNhan"><strong>Lời nhắn (*):</strong></label>
                <textarea id="loiNhan" name="loi_nhan" rows="3" class="form-nhap"><?= e($_POST['loi_nhan'] ?? '') ?></textarea>
                <?php if (isset($loiLuuBut['loi_nhan'])): ?>
                    <div data-bt7-pt="pt038"><?= e($loiLuuBut['loi_nhan']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" name="gui_luu_but" data-bt7-pt="pt039">Gửi lời nhắn</button>
        </form>

        <h3 data-bt7-pt="pt040">Lời nhắn gần đây:</h3>
        <?php if (empty($danhSachLuuBut)): ?>
            <p data-bt7-pt="pt041">Chưa có lời nhắn nào.</p>
        <?php else: ?>
            <div data-bt7-pt="pt042">
                <?php foreach ($danhSachLuuBut as $lb): ?>
                    <div data-bt7-pt="pt043" style="border-bottom: 1px dashed #ccc; padding: 10px 0; margin-bottom: 10px;">
                        <div data-bt7-pt="pt044" style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong data-bt7-pt="pt045"><?= e($lb['ten']) ?></strong>
                                <small data-bt7-pt="pt046" style="margin-left: 10px; color: #666;"><?= e($lb['ngay_tao'] ?? '') ?></small>
                            </div>
                            <!-- Nút Xóa lưu bút -->
                            <?php if ($coQuyenDuAn): ?>
                                <form method="post" action="gioithieu.php#luu-but" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lời nhắn này không?');" style="margin: 0;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="xoa_luu_but" value="<?= (int) $lb['id'] ?>">
                                    <button type="submit" style="background: #dc3545; color: white; border: none; padding: 3px 8px; border-radius: 4px; cursor: pointer; font-size: 12px;">Xóa</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <p data-bt7-pt="pt047" style="margin-top: 5px;"><?= nl2br(e($lb['loi_nhan'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</main>

<div data-bt7-pt="pt048">
    <a href="../../index.php" data-bt7-pt="pt049">Quay về trang chủ nhóm</a>
</div>

<?php require_once __DIR__ . '/../../inc/footer.php'; ?>