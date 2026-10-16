<?php
/**
 * Tệp: Trang cá nhân của KEOPHOXAY Phaeva (3120224182)
 * Chức năng 1: Lọc kỹ năng bằng tham số GET.
 * Chức năng 2: Tính điểm học phần bằng POST, kiểm tra điểm 0-10 và dùng PRG/Session.
 */

require_once __DIR__ . '/../../inc/config.php';

$goc = '../../';

// CHỨC NĂNG 1: DANH SÁCH KỸ NĂNG VÀ LỌC BẰNG GET
$danhSachKyNang = [
    [
        'muc' => 'lap-trinh',
        'tieu_de' => 'Lập trình & Đánh dấu',
        'mo_ta' => 'HTML5, CSS3, căn bản về Lập trình web.'
    ],
    [
        'muc' => 'cong-cu',
        'tieu_de' => 'Công cụ & Quản lý mã nguồn',
        'mo_ta' => 'Sử dụng Git và GitHub để làm việc nhóm.'
    ],
    [
        'muc' => 'ky-nang-mem',
        'tieu_de' => 'Kỹ năng mềm',
        'mo_ta' => 'Kỹ năng làm việc nhóm, giao tiếp và lên kế hoạch dự án.'
    ]
];

$boLoc = $_GET['loc'] ?? 'tat-ca';
$cacBoLocHopLe = ['tat-ca', 'lap-trinh', 'cong-cu', 'ky-nang-mem'];
if (!in_array($boLoc, $cacBoLocHopLe, true)) {
    $boLoc = 'tat-ca';
}

if ($boLoc === 'tat-ca') {
    $kyNangHienThi = $danhSachKyNang;
} else {
    $kyNangHienThi = array_values(array_filter(
        $danhSachKyNang,
        fn($kn) => $kn['muc'] === $boLoc
    ));
}

// CHỨC NĂNG 2: MÁY TÍNH ĐIỂM HỌC PHẦN
$loiDiem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_tinh_diem'])) {
    $a1 = filter_input(INPUT_POST, 'a1', FILTER_VALIDATE_FLOAT);
    $a2 = filter_input(INPUT_POST, 'a2', FILTER_VALIDATE_FLOAT);
    $a3 = filter_input(INPUT_POST, 'a3', FILTER_VALIDATE_FLOAT);

    // Kiểm tra điểm phải là số hợp lệ trong khoảng từ 0 đến 10.
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
        // Công thức: 0.2 * A1 + 0.3 * A2 + 0.5 * A3
        $diemTK = (0.2 * $a1) + (0.3 * $a2) + (0.5 * $a3);
        $_SESSION['ket_qua_diem'] = round($diemTK, 1);

        // PRG: chuyển hướng để tránh gửi lại biểu mẫu khi nhấn F5.
        header('Location: gioithieu.php?loc=' . urlencode($boLoc) . '#diem-hoc-phan');
        exit;
    }
}

// Lấy kết quả từ Session rồi xóa để chỉ hiển thị một lần.
$ketQua = $_SESSION['ket_qua_diem'] ?? null;
unset($_SESSION['ket_qua_diem']);

$tieuDe = 'Giới thiệu cá nhân - KEOPHOXAY Phaeva';
require_once __DIR__ . '/../../inc/header.php';
?>

<script>
// CSS của riêng trang cá nhân được thêm vào <head> để không tạo thẻ <style> trong <body>.
(function () {
    const cssTrangCaNhan = `
        .ph-header { background: linear-gradient(135deg, #1d3383, #112059); color: #fff; text-align: center; padding: 30px 15px; margin-top: 20px; border-radius: 8px 8px 0 0; }
        .ph-nav { background: #112059; padding: 12px; display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; border-radius: 0 0 8px 8px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,.1); }
        .ph-nav a { color: #fff; text-decoration: none; padding: 8px 15px; font-weight: bold; border-radius: 20px; transition: background .2s; font-size: 15px; }
        .ph-nav a:hover { background: rgba(255,255,255,.2); }
        .ph-btn-loc { background: #e2e8f0; border: 0; padding: 8px 15px; border-radius: 20px; cursor: pointer; text-decoration: none; color: #334155; font-weight: bold; margin: 5px; display: inline-block; transition: all .2s; }
        .ph-btn-loc.active { background: #1d3383; color: #fff; }
        .ph-btn-loc:hover { background: #cbd5e1; }
        .ph-btn-loc.active:hover { background: #112059; }
        .form-diem { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; max-width: 500px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #1d3383; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { background: #1d3383; color: #fff; padding: 10px 20px; border: 0; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; }
        .btn-submit:hover { background: #112059; }
        .alert-error { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-weight: bold; text-align: center; }
        .alert-success { color: #15803d; background: #dcfce7; padding: 15px; border-radius: 4px; margin-bottom: 15px; font-weight: bold; text-align: center; font-size: 18px; border: 1px solid #bbf7d0; }
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

    <section id="ky-nang" class="khoi-noi-dung">
        <h2 style="color: #1d3383;">Kỹ năng chuyên môn</h2>
        <div style="margin-bottom: 20px; text-align: center;">
            <a href="gioithieu.php?loc=tat-ca#ky-nang" class="ph-btn-loc <?= $boLoc === 'tat-ca' ? 'active' : '' ?>">Tất cả</a>
            <a href="gioithieu.php?loc=lap-trinh#ky-nang" class="ph-btn-loc <?= $boLoc === 'lap-trinh' ? 'active' : '' ?>">Lập trình</a>
            <a href="gioithieu.php?loc=cong-cu#ky-nang" class="ph-btn-loc <?= $boLoc === 'cong-cu' ? 'active' : '' ?>">Công cụ</a>
            <a href="gioithieu.php?loc=ky-nang-mem#ky-nang" class="ph-btn-loc <?= $boLoc === 'ky-nang-mem' ? 'active' : '' ?>">Kỹ năng mềm</a>
        </div>

        <?php if (empty($kyNangHienThi)): ?>
            <p style="text-align: center; color: #ef4444; font-style: italic;">Không tìm thấy kỹ năng nào trong nhóm này.</p>
        <?php else: ?>
            <ul style="line-height: 1.8; padding-left: 20px;">
                <?php foreach ($kyNangHienThi as $kn): ?>
                    <li style="margin-bottom: 10px; padding: 12px; background: #f8fafc; border-left: 4px solid #1d3383; border-radius: 4px; list-style-type: none;">
                        <strong style="color: #1d3383;"><?= e($kn['tieu_de']) ?>:</strong>
                        <?= e($kn['mo_ta']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section id="diem-hoc-phan" class="khoi-noi-dung">
        <h2 style="color: #1d3383; text-align: center;">Máy tính điểm học phần</h2>
        <div class="form-diem">
            <p style="text-align: center; margin-bottom: 20px; font-style: italic;">Công thức: 0.2 × A1 + 0.3 × A2 + 0.5 × A3</p>

            <?php if ($loiDiem !== ''): ?>
                <div class="alert-error"><?= e($loiDiem) ?></div>
            <?php endif; ?>

            <?php if ($ketQua !== null): ?>
                <div class="alert-success">🎉 Điểm tổng kết của bạn là: <?= e((string) $ketQua) ?></div>
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
                <button type="submit" name="btn_tinh_diem" class="btn-submit">TÍNH ĐIỂM TỔNG KẾT</button>
            </form>
        </div>
    </section>

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
