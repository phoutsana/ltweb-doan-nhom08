<?php
/**
 * Trang ca nhan Nguyen Son Chu (3120224020), giu giao dien Bai 5.
 * Chuc nang 1: Quan ly muc tieu hoc tap bang MySQL/PDO va quyen chu trang.
 * Chuc nang 2: Doc cau hoi, cham va luu lich su trac nghiem bang MySQL/PDO.
 * Thu: dang nhap, them/hoan thanh/xoa; lam quiz, F5, thu khach va tat JS.
 */
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/db.php';
if (is_file(__DIR__ . '/../../inc/xac-thuc.php')) {
    require_once __DIR__ . '/../../inc/xac-thuc.php';
}
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$goc = '../../';
$tieuDe = 'Giới thiệu cá nhân - Nguyễn Sơn Chu';
$moTa = 'Trang giới thiệu cá nhân của Nguyễn Sơn Chu.';
$trang = 'gioi-thieu';
$cssRieng = 'style-canhan.css';

// Chi su dung Session cho CSRF, thong bao va ket qua PRG; du lieu nam o MySQL.
$mssvChu = '3120224020';
$emailChu = 'chu.bt6@example.com'; // Tai khoan thu duoc khai bao trong seed_phanb_chu.sql.
$khoaKetQua = 'chu_3120224020_ket_qua_trac_nghiem';
$khoaThongBao = 'chu_3120224020_thong_bao';
$khoaCsrf = 'chu_3120224020_csrf';
if (empty($_SESSION[$khoaCsrf]) || !is_string($_SESSION[$khoaCsrf])) {
    $_SESSION[$khoaCsrf] = bin2hex(random_bytes(32));
}

$pdo = db();
// Xac minh tai khoan chu trang trong nguoi_dung, khong can sua bang chung thanh_vien.
$st = $pdo->prepare('SELECT id FROM nguoi_dung WHERE email = ?');
$st->execute([$emailChu]);
$idChu = (int) ($st->fetchColumn() ?: 0);

$nguoiDangNhap = $_SESSION['user'] ?? null;

$idDangNhap = is_array($nguoiDangNhap)
    ? (int) ($nguoiDangNhap['id'] ?? 0)
    : 0;

$laChuTrang = $idChu > 0 && $idDangNhap === $idChu;


// Lay 5 cau hoi goc tu bang rieng thay vi mang PHP co dinh.
$st = $pdo->prepare('SELECT ma_cau, noi_dung, lua_chon_a, lua_chon_b,
                           lua_chon_c, dap_an, giai_thich
                    FROM tv_cau_hoi WHERE mssv = ? ORDER BY ma_cau');
$st->execute([$mssvChu]);
$cauHoi = [];
foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $cauHoi[$row['ma_cau']] = [
        'noi_dung' => $row['noi_dung'],
        'lua_chon' => [
            'a' => $row['lua_chon_a'],
            'b' => $row['lua_chon_b'],
            'c' => $row['lua_chon_c'],
        ],
        'dap_an' => $row['dap_an'],
        'giai_thich' => $row['giai_thich'],
    ];
}

$loiMucTieu = '';
$loiTracNghiem = '';
$loiChung = '';
$giaTriMucTieu = '';
$traLoiCu = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $tokenGui = $_POST['csrf_token'] ?? null;
    $hanhDong = $_POST['hanh_dong'] ?? null;

    if (!is_string($tokenGui) || !hash_equals($_SESSION[$khoaCsrf], $tokenGui)) {
        $loiChung = 'Yêu cầu không hợp lệ. Hãy tải lại trang và thử lại.';
        http_response_code(400);
    } elseif (!is_string($hanhDong)) {
        $loiChung = 'Thao tác không hợp lệ.';
    } elseif (in_array($hanhDong, ['them_muc_tieu', 'doi_trang_thai', 'xoa_muc_tieu'], true)) {
        if (!$laChuTrang) {
            http_response_code(403);
            $loiChung = 'Chỉ chủ trang đã đăng nhập mới được thay đổi mục tiêu.';
        } elseif ($hanhDong === 'them_muc_tieu') {
            $duLieu = $_POST['noi_dung_muc_tieu'] ?? null;
            $giaTriMucTieu = is_string($duLieu) ? trim($duLieu) : '';
            $giaTriMucTieu = preg_replace('/\s+/u', ' ', $giaTriMucTieu) ?? '';
            if (preg_match('/^.{3,120}$/u', $giaTriMucTieu) !== 1) {
                $loiMucTieu = 'Mục tiêu phải dài từ 3 đến 120 ký tự.';
            } else {
                $dem = $pdo->prepare('SELECT COUNT(*) FROM tv_muc_tieu WHERE mssv = ?');
                $dem->execute([$mssvChu]);
                if ((int) $dem->fetchColumn() >= 20) {
                    $loiMucTieu = 'Chỉ được lưu tối đa 20 mục tiêu.';
                } else {
                    $st = $pdo->prepare('INSERT INTO tv_muc_tieu (mssv, noi_dung) VALUES (?, ?)');
                    $st->execute([$mssvChu, $giaTriMucTieu]);
                    $_SESSION[$khoaThongBao] = 'Đã thêm mục tiêu học tập.';
                    header('Location: gioithieu.php#muc-tieu', true, 303);
                    exit;
                }
            }
        } else {
            $idGui = $_POST['id_muc_tieu'] ?? null;
            $idMucTieu = is_scalar($idGui)
                ? filter_var($idGui, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
                : false;
            if ($idMucTieu === false) {
                $loiMucTieu = 'ID mục tiêu không hợp lệ.';
            } elseif ($hanhDong === 'xoa_muc_tieu') {
                $st = $pdo->prepare('DELETE FROM tv_muc_tieu WHERE id = ? AND mssv = ?');
                $st->execute([$idMucTieu, $mssvChu]);
                if ($st->rowCount() === 0) {
                    $loiMucTieu = 'Không tìm thấy mục tiêu được phép xóa.';
                } else {
                    $_SESSION[$khoaThongBao] = 'Đã xóa mục tiêu.';
                    header('Location: gioithieu.php#muc-tieu', true, 303);
                    exit;
                }
            } else {
                $st = $pdo->prepare('UPDATE tv_muc_tieu
                    SET hoan_thanh = CASE WHEN hoan_thanh = 1 THEN 0 ELSE 1 END
                    WHERE id = ? AND mssv = ?');
                $st->execute([$idMucTieu, $mssvChu]);
                if ($st->rowCount() === 0) {
                    $loiMucTieu = 'Không tìm thấy mục tiêu được phép cập nhật.';
                } else {
                    $_SESSION[$khoaThongBao] = 'Đã cập nhật trạng thái mục tiêu.';
                    header('Location: gioithieu.php#muc-tieu', true, 303);
                    exit;
                }
            }
        }
    } elseif ($hanhDong === 'nop_trac_nghiem') {
        $duLieu = $_POST['tra_loi'] ?? null;
        $traLoiCu = is_array($duLieu) ? $duLieu : [];
        $hopLe = is_array($duLieu) && count($cauHoi) === 5;
        foreach ($cauHoi as $maCau => $cau) {
            $chon = $traLoiCu[$maCau] ?? null;
            if (!is_string($chon) || !array_key_exists($chon, $cau['lua_chon'])) {
                $hopLe = false;
            }
        }
        if (!$hopLe) {
            $loiTracNghiem = 'Vui lòng chọn một đáp án hợp lệ cho cả 5 câu.';
        } else {
            $soCauDung = 0;
            foreach ($cauHoi as $maCau => $cau) {
                if ($traLoiCu[$maCau] === $cau['dap_an']) {
                    $soCauDung++;
                }
            }
            $st = $pdo->prepare('INSERT INTO tv_ket_qua_trac_nghiem
                (mssv, so_cau_dung, tong_cau) VALUES (?, ?, ?)');
            $st->execute([$mssvChu, $soCauDung, count($cauHoi)]);
            $_SESSION[$khoaKetQua] = [
                'so_cau_dung' => $soCauDung,
                'dap_an_da_chon' => $traLoiCu,
            ];
            header('Location: gioithieu.php?ketqua=1#trac-nghiem', true, 303);
            exit;
        }
    } else {
        $loiChung = 'Thao tác không được hỗ trợ.';
    }
}

$thongBao = $_SESSION[$khoaThongBao] ?? '';
unset($_SESSION[$khoaThongBao]);
$ketQuaTracNghiem = (($_GET['ketqua'] ?? null) === '1')
    ? ($_SESSION[$khoaKetQua] ?? null)
    : null;
$st = $pdo->prepare("SELECT id, noi_dung, hoan_thanh,
    DATE_FORMAT(ngay_tao, '%d/%m/%Y %H:%i') AS ngay_tao
    FROM tv_muc_tieu WHERE mssv = ? ORDER BY id DESC");
$st->execute([$mssvChu]);
$danhSachMucTieu = $st->fetchAll(PDO::FETCH_ASSOC);
$soMucTieuHoanThanh = count(array_filter(
    $danhSachMucTieu,
    static fn(array $mucTieu): bool => (int) $mucTieu['hoan_thanh'] === 1
));
$st = $pdo->prepare('SELECT so_cau_dung, tong_cau, ngay_lam
    FROM tv_ket_qua_trac_nghiem WHERE mssv = ? ORDER BY id DESC LIMIT 5');
$st->execute([$mssvChu]);
$lichSuTracNghiem = $st->fetchAll(PDO::FETCH_ASSOC);

// Nap giao dien chung; neu header chua ho tro CSS rieng thi them link vao <head>.
ob_start();
require_once __DIR__ . '/../../inc/header.php';
$htmlHeader = ob_get_clean();
if (strpos($htmlHeader, 'style-canhan.css') === false) {
    $htmlHeader = preg_replace(
        '~</head>~i',
        '    <link rel="stylesheet" href="style-canhan.css">' . "\n</head>",
        $htmlHeader,
        1
    ) ?? $htmlHeader;
}
echo $htmlHeader;
?>

<main class="noi-dung-chinh trang-ca-nhan">
    <div class="thanh-cong-cu-dau">
        <a class="back-home" href="<?= e($goc) ?>index.php">&larr; Về trang chủ nhóm</a>
        <button id="btn-toggle-theme" class="btn-theme-toggle" type="button">🌙 Chế độ tối</button>
    </div>

    <section class="dau-trang-ca-nhan" aria-labelledby="tieu-de-ca-nhan">
        <h1 id="tieu-de-ca-nhan">Trang giới thiệu cá nhân</h1>
        <p>Xin chào! Rất vui được giới thiệu về bản thân tôi.</p>
    </section>

    <nav class="nav-ca-nhan" aria-label="Điều hướng trong trang cá nhân">
        <a href="#gioi-thieu">Giới thiệu</a>
        <a href="#so-thich">Sở thích</a>
        <a href="#ky-nang">Kỹ năng</a>
        <a href="#thoi-khoa-bieu">Thời khóa biểu</a>
        <a href="#muc-tieu">Mục tiêu học tập</a>
        <a href="#trac-nghiem">Trắc nghiệm PHP</a>
    </nav>

    <section id="gioi-thieu">
        <h2>Giới thiệu bản thân</h2>
        <div class="profile">
            <img src="<?= e($goc) ?>images/avatar-SonChu.jpg" alt="Ảnh chân dung của Nguyễn Sơn Chu">
            <div class="info">
                <p><strong>Họ và tên:</strong> Nguyễn Sơn Chu</p>
                <p><strong>Ngành học:</strong> Công nghệ thông tin</p>
                <p><strong>Lớp:</strong> 24CNTT2</p>
                <p><strong>Trường:</strong> Trường Đại học Sư phạm - Đại học Đà Nẵng</p>
                <p><strong>Quê quán:</strong> Nghệ An</p>
                <p>
                    <strong>Email:</strong> nchu3808@gmail.com
                    <button id="btn-copy-email" class="btn-copy-email" type="button">Sao chép</button>
                </p>
                <p>
                    <strong>Mục tiêu:</strong> Nâng cao kiến thức lập trình và phát triển các sản phẩm công nghệ
                    phục vụ học tập và cuộc sống.
                </p>
                <div class="hop-luot-xem">
                    <span>
                        👀 <strong>Số lần mở trang trên trình duyệt này (JS):</strong>
                        <span id="so-luot-xem" class="so-luot-xem">1</span> lần
                    </span>
                    <button id="btn-reset-counter" class="btn-reset-counter" type="button">Đặt lại</button>
                </div>
            </div>
        </div>
    </section>

    <article id="so-thich">
        <h2>Sở thích</h2>
        <p>Tôi thích lập trình, tìm hiểu công nghệ, chơi game và nghe nhạc.</p>
        <p>Ngoài ra, tôi cũng thích tìm hiểu những công nghệ mới và áp dụng kiến thức đã học vào các dự án thực tế.</p>
    </article>

    <section id="ky-nang">
        <h2>Kỹ năng</h2>
        <ul>
            <li>Lập trình C/C++</li>
            <li>HTML và CSS</li>
            <li>SQL</li>
            <li>Làm việc nhóm</li>
            <li>Tìm hiểu và sử dụng công nghệ mới</li>
        </ul>
    </section>

    <section id="thoi-khoa-bieu">
        <h2>Thời khóa biểu</h2>
        <div class="bang-cuon-ngang">
            <table>
                <caption>Thời khóa biểu trong tuần</caption>
                <thead>
                    <tr>
                        <th scope="col">Thứ</th>
                        <th scope="col">Buổi sáng</th>
                        <th scope="col">Buổi chiều</th>
                        <th scope="col">Buổi tối</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><th scope="row">Thứ 2</th><td>Cơ sở dữ liệu</td><td>Lập trình C++</td><td>Tự học</td></tr>
                    <tr><th scope="row">Thứ 3</th><td>Khai phá dữ liệu</td><td>Web/Phát triển Web</td><td>Tự học</td></tr>
                    <tr><th scope="row">Thứ 4</th><td>Trống</td><td>Cơ sở dữ liệu</td><td>Làm bài tập</td></tr>
                    <tr><th scope="row">Thứ 5</th><td>An toàn thông tin</td><td>Lập trình Python</td><td>Tự học</td></tr>
                    <tr><th scope="row">Thứ 6</th><td>Trống</td><td>Trống</td><td>Nghỉ</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Chuc nang PHP 1: Quan ly muc tieu hoc tap bang MySQL/PDO -->
    <section id="muc-tieu" class="chu-chuc-nang">
        <h2>Quản lý mục tiêu học tập (MySQL/PDO)</h2>
        <p>Thêm, đánh dấu hoàn thành hoặc xóa mục tiêu. Dữ liệu được lưu trong MySQL.</p>

        <?php if ($loiChung !== ''): ?>
            <p class="chu-thong-bao-loi" role="alert"><?= e($loiChung) ?></p>
        <?php endif; ?>
        <?php if ($thongBao !== ''): ?>
            <p class="chu-thong-bao-thanh-cong" role="status"><?= e($thongBao) ?></p>
        <?php endif; ?>
        <?php if ($laChuTrang): ?>
            <form method="post" action="gioithieu.php#muc-tieu" class="chu-form-muc-tieu" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION[$khoaCsrf]) ?>">
                <input type="hidden" name="hanh_dong" value="them_muc_tieu">
                <label for="chu-noi-dung-muc-tieu">Mục tiêu mới (3 - 120 ký tự)</label>
                <div class="chu-hang-nhap">
                    <input id="chu-noi-dung-muc-tieu" name="noi_dung_muc_tieu" type="text"
                           maxlength="120" value="<?= e($giaTriMucTieu) ?>"
                           placeholder="Ví dụ: Ôn PHP 30 phút mỗi ngày" aria-describedby="chu-goi-y-muc-tieu">
                    <button class="chu-nut" type="submit">Thêm mục tiêu</button>
                </div>
                <p id="chu-goi-y-muc-tieu" class="chu-goi-y">Tối đa 20 mục tiêu học tập.</p>
                <?php if ($loiMucTieu !== ''): ?>
                    <p class="chu-thong-bao-loi" role="alert"><?= e($loiMucTieu) ?></p>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <p class="chu-goi-y">Chỉ chủ trang đã đăng nhập mới được thêm, cập nhật hoặc xóa mục tiêu.</p>
        <?php endif; ?>

        <p class="chu-tom-tat"><strong>Đã hoàn thành:</strong>
            <?= e((string) $soMucTieuHoanThanh) ?>/<?= e((string) count($danhSachMucTieu)) ?> mục tiêu
        </p>
        <?php if (empty($danhSachMucTieu)): ?>
            <p class="chu-goi-y">Chưa có mục tiêu nào. Hãy thêm mục tiêu đầu tiên.</p>
        <?php else: ?>
            <ul class="chu-danh-sach-muc-tieu">
                <?php foreach ($danhSachMucTieu as $mucTieu): ?>
                    <li class="chu-muc-tieu <?= (int) $mucTieu['hoan_thanh'] === 1 ? 'chu-da-xong' : '' ?>">
                        <div class="chu-noi-dung-muc-tieu">
                            <strong><?= e($mucTieu['noi_dung']) ?></strong>
                            <small>Ngày tạo: <?= e($mucTieu['ngay_tao']) ?></small>
                        </div>
                        <?php if ($laChuTrang): ?>
                            <div class="chu-thao-tac">
                                <form method="post" action="gioithieu.php#muc-tieu">
                                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION[$khoaCsrf]) ?>">
                                    <input type="hidden" name="id_muc_tieu" value="<?= e((string) $mucTieu['id']) ?>">
                                    <button class="chu-nut chu-nut-phu" type="submit" name="hanh_dong" value="doi_trang_thai">
                                        <?= (int) $mucTieu['hoan_thanh'] === 1 ? 'Làm lại' : 'Hoàn thành' ?>
                                    </button>
                                </form>
                                <form method="post" action="gioithieu.php#muc-tieu">
                                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION[$khoaCsrf]) ?>">
                                    <input type="hidden" name="id_muc_tieu" value="<?= e((string) $mucTieu['id']) ?>">
                                    <button class="chu-nut chu-nut-xoa" type="submit" name="hanh_dong" value="xoa_muc_tieu">Xóa</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <!-- Chuc nang PHP 2: Doc cau hoi, cham va luu diem bang MySQL/PDO -->
    <section id="trac-nghiem" class="chu-chuc-nang">
        <h2>Trắc nghiệm kiến thức lập trình (MySQL/PDO)</h2>
        <p>Câu hỏi lấy từ MySQL; máy chủ chấm điểm và lưu kết quả vào MySQL.</p>
        <form method="post" action="gioithieu.php#trac-nghiem" class="chu-form-trac-nghiem" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION[$khoaCsrf]) ?>">
            <input type="hidden" name="hanh_dong" value="nop_trac_nghiem">
            <?php foreach ($cauHoi as $maCau => $cau): ?>
                <fieldset class="chu-cau-hoi">
                    <legend><?= e($cau['noi_dung']) ?></legend>
                    <?php foreach ($cau['lua_chon'] as $maLuaChon => $noiDung): ?>
                        <?php $duocChon = (($traLoiCu[$maCau] ?? null) === $maLuaChon); ?>
                        <label class="chu-lua-chon">
                            <input type="radio" name="tra_loi[<?= e($maCau) ?>]"
                                   value="<?= e($maLuaChon) ?>" <?= $duocChon ? 'checked' : '' ?>>
                            <span><?= e($noiDung) ?></span>
                        </label>
                    <?php endforeach; ?>
                </fieldset>
            <?php endforeach; ?>
            <?php if ($loiTracNghiem !== ''): ?>
                <p class="chu-thong-bao-loi" role="alert"><?= e($loiTracNghiem) ?></p>
            <?php endif; ?>
            <button class="chu-nut" type="submit">Nộp bài và xem kết quả</button>
        </form>

        <?php if (is_array($ketQuaTracNghiem)): ?>
            <div class="chu-ket-qua-trac-nghiem" role="status">
                <h3>Kết quả trắc nghiệm</h3>
                <p>Bạn trả lời đúng <strong><?= e((string) $ketQuaTracNghiem['so_cau_dung']) ?>/<?= e((string) count($cauHoi)) ?></strong> câu.</p>
                <ol>
                    <?php foreach ($cauHoi as $maCau => $cau): ?>
                        <?php
                        $maDaChon = $ketQuaTracNghiem['dap_an_da_chon'][$maCau] ?? '';
                        $dung = $maDaChon === $cau['dap_an'];
                        ?>
                        <li>
                            <strong><?= $dung ? 'Đúng' : 'Chưa đúng' ?>:</strong>
                            <?= e($cau['noi_dung']) ?>
                            <p>Bạn chọn: <?= e($cau['lua_chon'][$maDaChon] ?? '') ?>.</p>
                            <p>Đáp án đúng: <?= e($cau['lua_chon'][$cau['dap_an']]) ?>.</p>
                            <p>Giải thích: <?= e($cau['giai_thich']) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        <?php endif; ?>
        <?php if ($lichSuTracNghiem): ?>
            <h3>Kết quả gần nhất đã lưu trong MySQL</h3>
            <div class="bang-cuon-ngang">
                <table>
                    <thead><tr><th scope="col">Thời gian</th><th scope="col">Điểm</th></tr></thead>
                    <tbody>
                        <?php foreach ($lichSuTracNghiem as $ketQua): ?>
                            <tr>
                                <td><?= e($ketQua['ngay_lam']) ?></td>
                                <td><?= e((string) $ketQua['so_cau_dung']) ?>/<?= e((string) $ketQua['tong_cau']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <p class="chu-thich-ca-nhan">© 2026 Nguyễn Sơn Chu · Trang cá nhân - Fashion Store</p>
    <div id="copy-toast" class="copy-toast" role="status" aria-live="polite"></div>
</main>

<!-- Giữ nguyên JavaScript riêng: Sáng/Tối, sao chép email, lượt xem cục bộ. -->
<script src="js/canhan.js"></script>
<?php require_once __DIR__ . '/../../inc/footer.php'; ?>
