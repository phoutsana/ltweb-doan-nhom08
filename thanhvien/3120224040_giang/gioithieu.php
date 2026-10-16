<?php
declare(strict_types=1);

/*
 * Tệp: gioithieu.php - Trang cá nhân của Ngô Hương Giang.
 * Sử dụng cấu hình, header và footer dùng chung của nhóm.
 * Giữ nguyên giao diện, CSS và JavaScript từ Bài thực hành 4.
 * Cách thử: Mở trang trên localhost và kiểm tra giao diện, tương tác.
 */

require_once __DIR__ . '/../../inc/config.php';

$goc = '../../';
$tieuDe = 'Giới thiệu Ngô Hương Giang | Fashion Store';
$trang = '';
$cssRieng = 'style-canhan.css';
// Chức năng PHP 1: Tra cứu thời khóa biểu theo thứ.
$lichTheoThu = [
    'thu2' => [
        'ten' => 'Thứ 2',
        'sang' => 'Học tập',
        'chieu' => 'Tự học'
    ],
    'thu3' => [
        'ten' => 'Thứ 3',
        'sang' => 'Học tập',
        'chieu' => 'Lập trình'
    ],
    'thu4' => [
        'ten' => 'Thứ 4',
        'sang' => 'Học tập',
        'chieu' => 'Làm đồ án'
    ],
    'thu5' => [
        'ten' => 'Thứ 5',
        'sang' => 'Học tập',
        'chieu' => 'Lập trình'
    ],
    'thu6' => [
        'ten' => 'Thứ 6',
        'sang' => 'Học tập',
        'chieu' => 'Tự học'
    ]
];

/*
 * Chức năng PHP 2: Xuất thời khóa biểu thành CSV.
 * Sử dụng dữ liệu có sẵn từ chức năng 1.
 * Xử lý trước khi xuất HTML để tránh lỗi header.
 */
if (isset($_GET['xuat']) && is_string($_GET['xuat']) && $_GET['xuat'] === 'csv') {
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
    header('Content-Disposition: attachment; filename="thoi-khoa-bieu-giang.csv"');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');

    // UTF-8 BOM giúp Excel nhận diện tiếng Việt.
    fwrite($output, "\xEF\xBB\xBF");

    foreach ($duLieuCSV as $dong) {
        fputcsv($output, $dong, ';', '"', '');
    }

    fclose($output);
    exit;
}

$thuDuocChon = null;
$loiTraCuu = '';

if (array_key_exists('thu', $_GET)) {
    $thu = $_GET['thu'];

    if (is_string($thu) && isset($lichTheoThu[$thu])) {
        $thuDuocChon = $thu;
    } else {
        $loiTraCuu = 'Ngày không hợp lệ. Vui lòng chọn từ Thứ 2 đến Thứ 6.';
    }
}
require __DIR__ . '/../../inc/header.php';
?>

  <!-- Hero Banner xanh đậm sát mép trên cùng, chữ trắng rõ nét -->
  <header class="hero-banner">
    <h1 class="hero-title">Trang giới thiệu cá nhân</h1>
    <p class="hero-subtitle">Sinh viên Công nghệ Thông tin - Đam mê Lập trình Web & UI/UX Design</p>
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

    <!-- ==========================================================================
         TÍNH NĂNG CÁ NHÂN: LỜI CHÀO THỜI GIAN THỰC & BỘ ĐIỀU CHỈNH CỠ CHỮ
         ========================================================================== -->

    <!-- 1. HỘP LỜI CHÀO THỜI GIAN THỰC (Đã thêm role="region" hợp lệ chuẩn W3C) -->
    <div class="greeting-box" role="region" aria-label="Lời chào thời gian thực">
      <div class="greeting-info">
        <p id="realtime-greeting" class="greeting-text">Đang tải lời chào...</p>
        <p id="realtime-date" class="greeting-date"></p>
      </div>
      <div id="realtime-clock" class="greeting-time">00:00:00</div>
    </div>

    <!-- 2. BỘ ĐIỀU CHỈNH CỠ CHỮ TOÀN TRANG (Đã thêm role="group" hợp lệ chuẩn W3C) -->
    <div class="font-control-group" role="group" aria-label="Bộ điều chỉnh cỡ chữ toàn trang">
      <span class="font-control-title">Cỡ chữ:</span>
      <button type="button" id="decrease-font" class="action-btn-small" title="Thu nhỏ cỡ chữ">A-</button>
      <span id="font-size-label" class="font-size-label">Vừa</span>
      <button type="button" id="increase-font" class="action-btn-small" title="Phóng to cỡ chữ">A+</button>
      <button type="button" id="reset-font" class="action-btn-small btn-secondary" title="Đặt lại cỡ chữ mặc định">Mặc định</button>
    </div>

    <!-- ==========================================================================
         MỤC 1: GIỚI THIỆU BẢN THÂN (FLEXBOX + ẢNH CO GIÃN THEO KHUNG)
========================================================================== -->
    <section id="gioi-thieu" class="info-card">
      <h2 class="card-title">Giới thiệu bản thân</h2>
      
      <div class="intro-flex">
        <div class="avatar-container">
          <img src="../../images/Huong_Giang.jpg" 
               alt="Ảnh chân dung Ngô Hương Giang" 
               class="profile-avatar"
               width="200" 
               height="200">
        </div>

        <div class="details-list">
          <p><span class="label">Họ và tên:</span> <span class="value">Ngô Hương Giang</span></p>
          <p><span class="label">Ngành học:</span> <span class="value">Công nghệ thông tin</span></p>
          <p><span class="label">Lớp:</span> <span class="value">24CNTT2</span></p>
          <p><span class="label">Trường:</span> <span class="value">Trường Đại học Sư phạm – Đại học Đà Nẵng</span></p>
          <p><span class="label">Mã sinh viên:</span> <span class="value">3120224040</span></p>
          <p><span class="label">Quê quán:</span> <span class="value">Quảng Nam</span></p>
          <p><span class="label">Mục tiêu:</span> <span class="value">Nâng cao kiến thức lập trình và phát triển các sản phẩm công nghệ phục vụ học tập và công việc.</span></p>
        </div>
      </div>
    </section>

    <!-- ==========================================================================
         MỤC 2: DỰ ÁN VÀ SỞ THÍCH
         ========================================================================== -->
    <section id="so-thich" class="info-card">
      <h2 class="card-title">Dự án và sở thích</h2>
      <div class="card-body-text">
        <p>
          Trong dự án <strong>Fashion Store</strong>, mình tham gia xây dựng website giới thiệu và cung cấp thông tin về các sản phẩm thời trang, phối hợp phát triển giao diện chuẩn responsive trên đa thiết bị.
        </p>
        <p class="text-spacing">
          <strong>Sở thích:</strong> Nghe nhạc, đọc sách, nghiên cứu thiết kế trải nghiệm người dùng và lập trình web.
        </p>
      </div>
    </section>

    <!-- ==========================================================================
         MỤC 3: KỸ NĂNG (BỐ CỤC BẰNG CSS GRID)
         ========================================================================== -->
    <section id="ky-nang" class="info-card">
      <h2 class="card-title">Kỹ năng</h2>
      <div class="skills-grid">
        <div class="skill-item">
          <div class="skill-header">
            <span>HTML5</span>
            <span>85%</span>
          </div>
          <div class="skill-bar"><div class="skill-progress" style="width: 85%;"></div></div>
        </div>

        <div class="skill-item">
          <div class="skill-header">
            <span>Lập trình web</span>
<span>80%</span>
          </div>
          <div class="skill-bar"><div class="skill-progress" style="width: 80%;"></div></div>
        </div>

        <div class="skill-item">
          <div class="skill-header">
            <span>Làm việc nhóm</span>
            <span>85%</span>
          </div>
          <div class="skill-bar"><div class="skill-progress" style="width: 85%;"></div></div>
        </div>

        <div class="skill-item">
          <div class="skill-header">
            <span>Sử dụng Git và GitHub</span>
            <span>80%</span>
          </div>
          <div class="skill-bar"><div class="skill-progress" style="width: 80%;"></div></div>
        </div>
      </div>
    </section>

    <!-- ==========================================================================
         MỤC 4: THỜI KHÓA BIỂU (CUỘN NGANG ĐỘC LẬP TRÊN MOBILE) VÀ NÚT QUAY LẠI
         ========================================================================== -->
    <section id="thoi-khoa-bieu" class="info-card">
      <h2 class="card-title">Thời khóa biểu tuần</h2>
      <p class="scroll-note">*(Trên điện thoại: cuộn hoặc vuốt ngang bảng để xem đầy đủ thông tin)*</p>

      <!-- Khung cuộn ngang độc lập không làm vỡ màn hình 360px -->
     <div class="table-responsive-wrapper" role="region" tabindex="0" aria-label="Bảng thời khóa biểu có thể cuộn ngang">
        <table class="schedule-table">
          <caption class="sr-only">Thời khóa biểu học tập trong tuần của Ngô Hương Giang</caption>
          <thead>
            <tr>
              <th scope="col">Thời gian</th>
              <th scope="col">Thứ 2</th>
              <th scope="col">Thứ 3</th>
              <th scope="col">Thứ 4</th>
              <th scope="col">Thứ 5</th>
              <th scope="col">Thứ 6</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row">Buổi sáng</th>
              <td>Học tập</td>
              <td>Học tập</td>
              <td>Học tập</td>
              <td>Học tập</td>
              <td>Học tập</td>
            </tr>
            <tr>
              <th scope="row">Buổi chiều</th>
              <td>Tự học</td>
              <td>Lập trình</td>
              <td>Làm đồ án</td>
              <td>Lập trình</td>
              <td>Tự học</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 2 nút quay lại nhỏ gọn có mũi tên đặt ngay dưới bảng thời khóa biểu -->
      
<div class="bottom-actions">
    <a href="../../index.php" class="action-btn-small">
        ← Quay về trang chủ nhóm
    </a>

    <a href="../../gioi-thieu.php" class="action-btn-small">
        ← Quay về trang giới thiệu nhóm
    </a>

    <a href="gioithieu.php?xuat=csv" class="action-btn-small">
        ↓ Tải thời khóa biểu CSV
    </a>
</div>
    </section>
    <!-- Chức năng PHP 1: Xem thời khóa biểu theo thứ -->
<section id="tra-cuu-lich" class="info-card">
    <h2 class="card-title">Xem thời khóa biểu theo thứ</h2>

    <form method="get" action="gioithieu.php#tra-cuu-lich">
        <label for="thu-hoc">Chọn thứ trong tuần:</label>

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

    <?php if ($thuDuocChon !== null): ?>
        <div class="card-body-text">
            <h3>
                Lịch học <?= e($lichTheoThu[$thuDuocChon]['ten']) ?>
            </h3>

            <p>
                <strong>Buổi sáng:</strong>
                <?= e($lichTheoThu[$thuDuocChon]['sang']) ?>
            </p>

            <p>
                <strong>Buổi chiều:</strong>
                <?= e($lichTheoThu[$thuDuocChon]['chieu']) ?>
            </p>
        </div>
    <?php endif; ?>
</section>
  </main>

  <!-- Chân trang -->
  <p class="page-footer">
    &copy; 2026 Nhóm 08 - Dự án Fashion Store.
    Thành viên: <strong>Ngô Hương Giang</strong> (3120224040)
</p>

<script src="js/canhan.js"></script>

<?php require __DIR__ . '/../../inc/footer.php'; ?>