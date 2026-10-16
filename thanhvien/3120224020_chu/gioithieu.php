<?php
/**
 * Trang ca nhan Nguyen Son Chu (3120224020), dung header/footer chung cua nhom.
 * PHP 1: Quan ly muc tieu hoc tap (them, hoan thanh, xoa) bang Session va PRG.
 * PHP 2: Trac nghiem kien thuc lap trinh, cham dap an tai may chu va PRG.
 * Thu: tao mot muc tieu, danh dau/xoa; lam bai trac nghiem va nhan F5.
 */
require_once __DIR__ . '/../../inc/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$goc = '../../';
$tieuDe = 'Giới thiệu cá nhân - Nguyễn Sơn Chu';
$moTa = 'Trang giới thiệu cá nhân của Nguyễn Sơn Chu.';
$trang = 'gioi-thieu';
$cssRieng = 'style-canhan.css';

// Ten khoa rieng de tranh anh huong den Session cua nhom va thanh vien khac.
$khoaMucTieu = 'chu_3120224020_muc_tieu';
$khoaKetQua = 'chu_3120224020_ket_qua_trac_nghiem';
$khoaThongBao = 'chu_3120224020_thong_bao';
$khoaCsrf = 'chu_3120224020_csrf';

if (empty($_SESSION[$khoaCsrf]) || !is_string($_SESSION[$khoaCsrf])) {
    $_SESSION[$khoaCsrf] = bin2hex(random_bytes(32));
}
if (!isset($_SESSION[$khoaMucTieu]) || !is_array($_SESSION[$khoaMucTieu])) {
    $_SESSION[$khoaMucTieu] = [];
}

// Cau hoi va dap an chi duoc cham tai may chu, khong day dap an xuong form.
$cauHoi = [
    'q1' => [
        'noi_dung' => 'Trong PHP, tên biến bắt đầu bằng ký tự nào?',
        'lua_chon' => ['a' => '$', 'b' => '@', 'c' => '#'],
        'dap_an' => 'a',
        'giai_thich' => 'Biến PHP được viết với dấu $ ở phía trước tên biến.',
    ],
    'q2' => [
        'noi_dung' => 'Phương thức HTTP nào thường dùng để gửi biểu mẫu làm thay đổi dữ liệu?',
        'lua_chon' => ['a' => 'GET', 'b' => 'POST', 'c' => 'HEAD'],
        'dap_an' => 'b',
        'giai_thich' => 'POST thường được dùng cho thao tác tạo hoặc cập nhật dữ liệu.',
    ],
    'q3' => [
        'noi_dung' => 'Hàm PHP nào dùng để mã hóa ký tự đặc biệt khi in ra HTML?',
        'lua_chon' => ['a' => 'strlen()', 'b' => 'json_decode()', 'c' => 'htmlspecialchars()'],
        'dap_an' => 'c',
        'giai_thich' => 'htmlspecialchars() chuyển các ký tự HTML đặc biệt thành thực thể.',
    ],
    'q4' => [
        'noi_dung' => 'Dữ liệu tham số GET trong URL được truy cập qua biến nào?',
        'lua_chon' => ['a' => '$_COOKIE', 'b' => '$_GET', 'c' => '$_FILES'],
        'dap_an' => 'b',
        'giai_thich' => 'Mảng $_GET chứa các tham số truy vấn được gửi qua URL.',
    ],
    'q5' => [
        'noi_dung' => 'Mục đích chính của mẫu POST - Redirect - GET (PRG) là gì?',
        'lua_chon' => [
            'a' => 'Giảm dung lượng ảnh',
            'b' => 'Chống gửi lại biểu mẫu khi tải lại trang',
            'c' => 'Thay thế việc kiểm tra dữ liệu',
        ],
        'dap_an' => 'b',
        'giai_thich' => 'Chuyển hướng sau POST thành công giúp F5 không gửi lại yêu cầu POST.',
    ],
];

$loiMucTieu = '';
$loiTracNghiem = '';
$loiChung = '';
$giaTriMucTieu = '';
$traLoiCu = [];

// ----------------------------------------------------------
// Hai chuc nang xu ly PHP tren may chu (khong can JavaScript)
// ----------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $tokenGui = $_POST['csrf_token'] ?? null;
    $hanhDong = $_POST['hanh_dong'] ?? null;

    if (!is_string($tokenGui) || !hash_equals($_SESSION[$khoaCsrf], $tokenGui)) {
        $loiChung = 'Yêu cầu không hợp lệ. Hãy tải lại trang và thử lại.';
        http_response_code(400);
    } elseif (!is_string($hanhDong)) {
        $loiChung = 'Thao tác không hợp lệ.';
    } elseif ($hanhDong === 'them_muc_tieu') {
        // Chuc nang 1: Kiem tra dau vao va luu muc tieu tren Session.
        $duLieu = $_POST['noi_dung_muc_tieu'] ?? null;
        $giaTriMucTieu = is_string($duLieu) ? trim($duLieu) : '';
        $giaTriMucTieu = preg_replace('/\s+/u', ' ', $giaTriMucTieu) ?? '';

        if (preg_match('/^.{3,120}$/u', $giaTriMucTieu) !== 1) {
            $loiMucTieu = 'Mục tiêu phải dài từ 3 đến 120 ký tự.';
        } elseif (count($_SESSION[$khoaMucTieu]) >= 20) {
            $loiMucTieu = 'Mỗi phiên chỉ được lưu tối đa 20 mục tiêu.';
        } else {
            $_SESSION[$khoaMucTieu][] = [
                'id' => bin2hex(random_bytes(8)),
                'noi_dung' => $giaTriMucTieu,
                'hoan_thanh' => false,
                'ngay_tao' => date('d/m/Y H:i'),
            ];
            $_SESSION[$khoaThongBao] = 'Đã thêm mục tiêu học tập.';
            header('Location: gioithieu.php#muc-tieu', true, 303);
            exit;
        }
    } elseif ($hanhDong === 'doi_trang_thai' || $hanhDong === 'xoa_muc_tieu') {
        // Moi thao tac voi muc tieu phai co ID hop le va thuc su ton tai.
        $idGui = $_POST['id_muc_tieu'] ?? null;
        $timThay = false;
        if (is_string($idGui) && preg_match('/^[a-f0-9]{16}$/D', $idGui) === 1) {
            foreach ($_SESSION[$khoaMucTieu] as $viTri => $mucTieu) {
                if (($mucTieu['id'] ?? '') === $idGui) {
                    if ($hanhDong === 'xoa_muc_tieu') {
                        array_splice($_SESSION[$khoaMucTieu], $viTri, 1);
                        $_SESSION[$khoaThongBao] = 'Đã xóa mục tiêu.';
                    } else {
                        $_SESSION[$khoaMucTieu][$viTri]['hoan_thanh'] =
                            !($mucTieu['hoan_thanh'] ?? false);
                        $_SESSION[$khoaThongBao] = 'Đã cập nhật trạng thái mục tiêu.';
                    }
                    $timThay = true;
                    break;
                }
            }
        }
        if ($timThay) {
            header('Location: gioithieu.php#muc-tieu', true, 303);
            exit;
        }
        $loiMucTieu = 'Không tìm thấy mục tiêu cần cập nhật.';
    } elseif ($hanhDong === 'nop_trac_nghiem') {
        // Chuc nang 2: Cham toan bo bai thi bang PHP.
        $duLieu = $_POST['tra_loi'] ?? null;
        $traLoiCu = is_array($duLieu) ? $duLieu : [];
        $hopLe = is_array($duLieu);

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
            $dapAnDaChon = [];
            foreach ($cauHoi as $maCau => $cau) {
                $dapAnDaChon[$maCau] = $traLoiCu[$maCau];
                if ($traLoiCu[$maCau] === $cau['dap_an']) {
                    $soCauDung++;
                }
            }
            $_SESSION[$khoaKetQua] = [
                'so_cau_dung' => $soCauDung,
                'dap_an_da_chon' => $dapAnDaChon,
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
$soMucTieuHoanThanh = count(array_filter(
    $_SESSION[$khoaMucTieu],
    static fn(array $mucTieu): bool => !empty($mucTieu['hoan_thanh'])
));

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
        <h2 id="tieu-de-ca-nhan">Trang giới thiệu cá nhân</h2>
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

    <!-- Chuc nang PHP 1: Quan ly muc tieu hoc tap tren Session -->
    <section id="muc-tieu" class="chu-chuc-nang">
        <h2>Quản lý mục tiêu học tập (PHP)</h2>
        <p>Thêm việc cần làm, đánh dấu hoàn thành hoặc xóa. Danh sách được lưu riêng trong phiên của bạn.</p>

        <?php if ($loiChung !== ''): ?>
            <p class="chu-thong-bao-loi" role="alert"><?= e($loiChung) ?></p>
        <?php endif; ?>
        <?php if ($thongBao !== ''): ?>
            <p class="chu-thong-bao-thanh-cong" role="status"><?= e($thongBao) ?></p>
        <?php endif; ?>
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
            <p id="chu-goi-y-muc-tieu" class="chu-goi-y">Tối đa 20 mục tiêu trong mỗi phiên trình duyệt.</p>
            <?php if ($loiMucTieu !== ''): ?>
                <p class="chu-thong-bao-loi" role="alert"><?= e($loiMucTieu) ?></p>
            <?php endif; ?>
        </form>

        <p class="chu-tom-tat"><strong>Đã hoàn thành:</strong>
            <?= e((string) $soMucTieuHoanThanh) ?>/<?= e((string) count($_SESSION[$khoaMucTieu])) ?> mục tiêu
        </p>

        <?php if (empty($_SESSION[$khoaMucTieu])): ?>
            <p class="chu-goi-y">Chưa có mục tiêu nào. Hãy thêm mục tiêu đầu tiên.</p>
        <?php else: ?>
            <ul class="chu-danh-sach-muc-tieu">
                <?php foreach ($_SESSION[$khoaMucTieu] as $mucTieu): ?>
                    <li class="chu-muc-tieu <?= !empty($mucTieu['hoan_thanh']) ? 'chu-da-xong' : '' ?>">
                        <div class="chu-noi-dung-muc-tieu">
                            <strong><?= e($mucTieu['noi_dung']) ?></strong>
                            <small>Ngày tạo: <?= e($mucTieu['ngay_tao']) ?></small>
                        </div>
                        <div class="chu-thao-tac">
                            <form method="post" action="gioithieu.php#muc-tieu">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION[$khoaCsrf]) ?>">
                                <input type="hidden" name="id_muc_tieu" value="<?= e($mucTieu['id']) ?>">
                                <button class="chu-nut chu-nut-phu" type="submit" name="hanh_dong" value="doi_trang_thai">
                                    <?= !empty($mucTieu['hoan_thanh']) ? 'Làm lại' : 'Hoàn thành' ?>
                                </button>
                            </form>
                            <form method="post" action="gioithieu.php#muc-tieu">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION[$khoaCsrf]) ?>">
                                <input type="hidden" name="id_muc_tieu" value="<?= e($mucTieu['id']) ?>">
                                <button class="chu-nut chu-nut-xoa" type="submit" name="hanh_dong" value="xoa_muc_tieu">
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <!-- Chuc nang PHP 2: Trac nghiem kiem tra kien thuc tren may chu -->
    <section id="trac-nghiem" class="chu-chuc-nang">
        <h2>Trắc nghiệm kiến thức lập trình (PHP)</h2>
        <p>Chọn một đáp án cho mỗi câu. Hệ thống chấm bài ở máy chủ và giải thích từng câu.</p>
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
    </section>

    <p class="chu-thich-ca-nhan">© 2026 Nguyễn Sơn Chu · Trang cá nhân - Fashion Store</p>
    <div id="copy-toast" class="copy-toast" role="status" aria-live="polite"></div>
</main>

<!-- Giữ nguyên JavaScript riêng: Sáng/Tối, sao chép email, lượt xem cục bộ. -->
<script src="js/canhan.js"></script>
<?php require_once __DIR__ . '/../../inc/footer.php'; ?>
