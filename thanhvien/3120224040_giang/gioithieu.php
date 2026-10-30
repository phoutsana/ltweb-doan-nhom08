<?php
declare(strict_types=1);

/*
 * Tệp: gioithieu.php - Trang cá nhân Ngô Hương Giang (3120224040).
 * Bài 6 - Phần B: Tra cứu thời khóa biểu bằng MySQL/PDO.
 * Chức năng 2: Xuất thời khóa biểu CSV từ MySQL.
 * Cách thử: gioithieu.php?thu=thu3 và gioithieu.php?xuat=csv.
 * Giữ giao diện, CSS và JavaScript của trang cá nhân Bài 5.
 */

require_once __DIR__ . '/../../inc/config.php';

$goc = '../../';
$tieuDe = 'Giới thiệu Ngô Hương Giang | Fashion Store';
$trang = '';
$cssRieng = 'style-canhan.css';

const MSSV_GIANG = '3120224040';

/*
 * Đọc dữ liệu thời khóa biểu từ MySQL.
 * Bảng: tv_thoi_khoa_bieu.
 * Mọi dữ liệu hiển thị và xuất CSV đều lấy từ CSDL.
 */
$lichTheoThu = [];

$st = db()->prepare(
    'SELECT thu, buoi_sang, buoi_chieu
     FROM tv_thoi_khoa_bieu
     WHERE mssv = ?
     ORDER BY thu ASC'
);

$st->execute([MSSV_GIANG]);

foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $dong) {
    $maThu = 'thu' . (int) $dong['thu'];

    $lichTheoThu[$maThu] = [
        'ten' => 'Thứ ' . (int) $dong['thu'],
        'sang' => (string) $dong['buoi_sang'],
        'chieu' => (string) $dong['buoi_chieu']
    ];
}

/*
 * CHỨC NĂNG PHP 2 - BÀI 6
 * Xuất thời khóa biểu CSV bằng dữ liệu MySQL.
 * Thực hiện trước khi xuất HTML để tránh lỗi HTTP header.
 */
if (
    isset($_GET['xuat']) &&
    is_string($_GET['xuat']) &&
    $_GET['xuat'] === 'csv'
) {
    $duLieuCSV = [
        ['Thời gian'],
        ['Buổi sáng'],
        ['Buổi chiều']
    ];

    foreach ($lichTheoThu as $lich) {
        $duLieuCSV[0][] = $lich['ten'];
        $duLieuCSV[1][] = $lich['sang'];
        $duLieuCSV[2][] = $lich['chieu'];
    }

    $output = fopen('php://output', 'wb');

    if ($output === false) {
        throw new RuntimeException('Không thể tạo tệp CSV.');
    }

    header('Content-Type: text/csv; charset=UTF-8');
    header(
        'Content-Disposition: attachment; filename="thoi-khoa-bieu-giang.csv"'
    );
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');

    // UTF-8 BOM giúp Excel hiển thị đúng tiếng Việt.
    fwrite($output, "\xEF\xBB\xBF");

    foreach ($duLieuCSV as $dong) {
        fputcsv($output, $dong, ';', '"', '');
    }

    fclose($output);
    exit;
}

/*
 * CHỨC NĂNG PHP 1 - BÀI 6
 * Tra cứu thời khóa biểu theo thứ bằng MySQL/PDO.
 * Kiểm tra tham số GET trước khi truy vấn CSDL.
 */
$thuDuocChon = null;
$lichDuocChon = null;
$loiTraCuu = '';

if (array_key_exists('thu', $_GET)) {
    $thuNhap = $_GET['thu'];

    if (
        !is_string($thuNhap) ||
        preg_match('/^thu[2-6]$/D', $thuNhap) !== 1
    ) {
        $loiTraCuu =
            'Ngày không hợp lệ. Vui lòng chọn từ Thứ 2 đến Thứ 6.';
    } else {
        $stChon = db()->prepare(
            'SELECT thu, buoi_sang, buoi_chieu
             FROM tv_thoi_khoa_bieu
             WHERE mssv = ? AND thu = ?
             LIMIT 1'
        );

        $stChon->execute([
            MSSV_GIANG,
            (int) substr($thuNhap, 3)
        ]);

        $dongChon = $stChon->fetch(PDO::FETCH_ASSOC);

        if ($dongChon === false) {
            $loiTraCuu =
                'Chưa có thời khóa biểu cho thứ đã chọn.';
        } else {
            $thuDuocChon = $thuNhap;

            $lichDuocChon = [
                'ten' => 'Thứ ' . (int) $dongChon['thu'],
                'sang' => (string) $dongChon['buoi_sang'],
                'chieu' => (string) $dongChon['buoi_chieu']
            ];
        }
    }
}

require __DIR__ . '/../../inc/header.php';
?>

<!-- Hero Banner xanh đậm sát mép trên cùng, chữ trắng rõ nét -->
<header class="hero-banner">
    <h1 class="hero-title">Trang giới thiệu cá nhân</h1>
    <p class="hero-subtitle">
        Sinh viên Công nghệ Thông tin - Đam mê Lập trình Web & UI/UX Design
    </p>
</header>

<!-- Thanh menu điều hướng các mục con -->
<nav class="sub-nav" aria-label="Menu cá nhân">
    <div class="nav-container">
        <a href="#gioi-thieu" class="nav-link active">Giới thiệu</a>
        <a href="#so-thich" class="nav-link">Sở thích</a>
        <a href="#ky-nang" class="nav-link">Kỹ năng</a>
        <a href="#thoi-khoa-bieu" class="nav-link">Thời khóa biểu</a>
    </div>
</nav>

<!-- Khung nội dung chính -->
<main class="content-wrapper">

    <!-- ===========================================================
         TÍNH NĂNG CÁ NHÂN: LỜI CHÀO VÀ ĐIỀU CHỈNH CỠ CHỮ
         =========================================================== -->

    <!-- 1. HỘP LỜI CHÀO THỜI GIAN THỰC -->
    <div class="greeting-box"
         role="region"
         aria-label="Lời chào thời gian thực">

        <div class="greeting-info">
            <p id="realtime-greeting" class="greeting-text">
                Đang tải lời chào...
            </p>
            <p id="realtime-date" class="greeting-date"></p>
        </div>

        <div id="realtime-clock" class="greeting-time">
            00:00:00
        </div>
    </div>

    <!-- 2. BỘ ĐIỀU CHỈNH CỠ CHỮ TOÀN TRANG -->
    <div class="font-control-group"
         role="group"
         aria-label="Bộ điều chỉnh cỡ chữ toàn trang">

        <span class="font-control-title">Cỡ chữ:</span>

        <button type="button"
                id="decrease-font"
                class="action-btn-small"
                title="Thu nhỏ cỡ chữ">
            A-
        </button>

        <span id="font-size-label" class="font-size-label">
            Vừa
        </span>

        <button type="button"
                id="increase-font"
                class="action-btn-small"
                title="Phóng to cỡ chữ">
            A+
        </button>

        <button type="button"
                id="reset-font"
                class="action-btn-small btn-secondary"
                title="Đặt lại cỡ chữ mặc định">
            Mặc định
        </button>
    </div>

    <!-- ===========================================================
         MỤC 1: GIỚI THIỆU BẢN THÂN
         =========================================================== -->
    <section id="gioi-thieu" class="info-card">
        <h2 class="card-title">Giới thiệu bản thân</h2>

        <div class="intro-flex">
            <div class="avatar-container">
                <img
                    src="../../images/Huong_Giang.jpg"
                    alt="Ảnh chân dung Ngô Hương Giang"
                    class="profile-avatar"
                    width="200"
                    height="200">
            </div>

            <div class="details-list">
                <p>
                    <span class="label">Họ và tên:</span>
                    <span class="value">Ngô Hương Giang</span>
                </p>

                <p>
                    <span class="label">Ngành học:</span>
                    <span class="value">Công nghệ thông tin</span>
                </p>

                <p>
                    <span class="label">Lớp:</span>
                    <span class="value">24CNTT2</span>
                </p>

                <p>
                    <span class="label">Trường:</span>
                    <span class="value">
                        Trường Đại học Sư phạm – Đại học Đà Nẵng
                    </span>
                </p>

                <p>
                    <span class="label">Mã sinh viên:</span>
                    <span class="value">3120224040</span>
                </p>

                <p>
                    <span class="label">Quê quán:</span>
                    <span class="value">Quảng Nam</span>
                </p>

                <p>
                    <span class="label">Mục tiêu:</span>
                    <span class="value">
                        Nâng cao kiến thức lập trình và phát triển
                        các sản phẩm công nghệ phục vụ học tập
                        và công việc.
                    </span>
                </p>
            </div>
        </div>
    </section>

    <!-- ===========================================================
         MỤC 2: DỰ ÁN VÀ SỞ THÍCH
         =========================================================== -->
    <section id="so-thich" class="info-card">
        <h2 class="card-title">Dự án và sở thích</h2>

        <div class="card-body-text">
            <p>
                Trong dự án <strong>Fashion Store</strong>, mình tham gia
                xây dựng website giới thiệu và cung cấp thông tin
                về các sản phẩm thời trang, phối hợp phát triển
                giao diện chuẩn responsive trên đa thiết bị.
            </p>

            <p class="text-spacing">
                <strong>Sở thích:</strong>
                Nghe nhạc, đọc sách, nghiên cứu thiết kế trải nghiệm
                người dùng và lập trình web.
            </p>
        </div>
    </section>

    <!-- ===========================================================
         MỤC 3: KỸ NĂNG
         =========================================================== -->
    <section id="ky-nang" class="info-card">
        <h2 class="card-title">Kỹ năng</h2>

        <div class="skills-grid">
            <div class="skill-item">
                <div class="skill-header">
                    <span>HTML5</span>
                    <span>85%</span>
                </div>
                <div class="skill-bar">
                    <div class="skill-progress" style="width: 85%;"></div>
                </div>
            </div>

            <div class="skill-item">
                <div class="skill-header">
                    <span>Lập trình web</span>
                    <span>80%</span>
                </div>
                <div class="skill-bar">
                    <div class="skill-progress" style="width: 80%;"></div>
                </div>
            </div>

            <div class="skill-item">
                <div class="skill-header">
                    <span>Làm việc nhóm</span>
                    <span>85%</span>
                </div>
                <div class="skill-bar">
                    <div class="skill-progress" style="width: 85%;"></div>
                </div>
            </div>

            <div class="skill-item">
                <div class="skill-header">
                    <span>Sử dụng Git và GitHub</span>
                    <span>80%</span>
                </div>
                <div class="skill-bar">
                    <div class="skill-progress" style="width: 80%;"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===========================================================
         MỤC 4: THỜI KHÓA BIỂU TỪ MYSQL
         =========================================================== -->
    <section id="thoi-khoa-bieu" class="info-card">
        <h2 class="card-title">Thời khóa biểu tuần</h2>

        <p class="scroll-note">
            *(Trên điện thoại: cuộn hoặc vuốt ngang bảng
            để xem đầy đủ thông tin)*
        </p>

        <!-- Bảng lấy dữ liệu trực tiếp từ MySQL/PDO -->
        <div class="table-responsive-wrapper"
             role="region"
             tabindex="0"
             aria-label="Bảng thời khóa biểu có thể cuộn ngang">

            <table class="schedule-table">
                <caption class="sr-only">
                    Thời khóa biểu học tập trong tuần của Ngô Hương Giang
                </caption>

                <thead>
                    <tr>
                        <th scope="col">Thời gian</th>

                        <?php foreach ($lichTheoThu as $lich): ?>
                            <th scope="col">
                                <?= e($lich['ten']) ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($lichTheoThu === []): ?>
                        <tr>
                            <td colspan="1">
                                Chưa có dữ liệu thời khóa biểu.
                            </td>
                        </tr>
                    <?php else: ?>

                        <tr>
                            <th scope="row">Buổi sáng</th>

                            <?php foreach ($lichTheoThu as $lich): ?>
                                <td>
                                    <?= e($lich['sang']) ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                        <tr>
                            <th scope="row">Buổi chiều</th>

                            <?php foreach ($lichTheoThu as $lich): ?>
                                <td>
                                    <?= e($lich['chieu']) ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Các nút điều hướng và tải CSV -->
        <div class="bottom-actions">

            <a href="../../index.php" class="action-btn-small">
                ← Quay về trang chủ nhóm
            </a>

            <a href="../../gioi-thieu.php" class="action-btn-small">
                ← Quay về trang giới thiệu nhóm
            </a>

            <a href="gioithieu.php?xuat=csv"
               class="action-btn-small">
                ↓ Tải thời khóa biểu CSV
            </a>
        </div>
    </section>

    <!-- ===========================================================
         CHỨC NĂNG 1: TRA CỨU THEO THỨ BẰNG MYSQL/PDO
         =========================================================== -->
    <section id="tra-cuu-lich" class="info-card">
        <h2 class="card-title">Xem thời khóa biểu theo thứ</h2>

        <form method="get" action="gioithieu.php#tra-cuu-lich">
            <label for="thu-hoc">
                Chọn thứ trong tuần:
            </label>

            <select name="thu" id="thu-hoc" required>
                <option value="">-- Chọn thứ --</option>

                <?php foreach ($lichTheoThu as $maThu => $lich): ?>
                    <option
                        value="<?= e($maThu) ?>"
                        <?= $thuDuocChon === $maThu ? 'selected' : '' ?>
                    >
                        <?= e($lich['ten']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="action-btn-small">
                Xem lịch
            </button>
        </form>

        <?php if ($loiTraCuu !== ''): ?>
            <p role="alert" style="color: red;">
                <?= e($loiTraCuu) ?>
            </p>
        <?php endif; ?>

        <?php if ($lichDuocChon !== null): ?>
            <div class="card-body-text">
                <h3>
                    Lịch học <?= e($lichDuocChon['ten']) ?>
                </h3>

                <p>
                    <strong>Buổi sáng:</strong>
                    <?= e($lichDuocChon['sang']) ?>
                </p>

                <p>
                    <strong>Buổi chiều:</strong>
                    <?= e($lichDuocChon['chieu']) ?>
                </p>
            </div>
        <?php endif; ?>
    </section>

</main>

<!-- Chân trang cá nhân -->
<p class="page-footer">
    &copy; 2026 Nhóm 08 - Dự án Fashion Store.
    Thành viên: <strong>Ngô Hương Giang</strong> (3120224040)
</p>

<script src="js/canhan.js"></script>

<?php require __DIR__ . '/../../inc/footer.php'; ?>
