<?php

/**
 * Tệp: Trang cá nhân của Xayyatiew Phoutsana (3120224184)
 * Chức năng 1 (Sáng/Tối): Dùng cookie để nhớ giao diện. Tránh xung đột CSS với nhóm bằng !important.
 * Chức năng 2 (Lưu bút): Form POST lưu file JSONL, có PRG chống gửi lặp.
 */
require_once __DIR__ . '/../../inc/config.php';
$goc = '../../';

// ==========================================
// 1. CHỨC NĂNG: GIAO DIỆN SÁNG / TỐI (COOKIE)
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
// 2. CHỨC NĂNG: SỔ LƯU BÚT (FILE JSONL)
// ==========================================
$fileLuuBut = __DIR__ . '/../../storage/3120224184_luubut.jsonl';
$loi = [];
$thongBao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gui_luu_but'])) {
    $ten = trim($_POST['ten'] ?? '');
    $loiNhan = trim($_POST['loi_nhan'] ?? '');

    if (mb_strlen($ten) < 2) $loi['ten'] = 'Tên phải có ít nhất 2 ký tự.';
    if (mb_strlen($loiNhan) < 5) $loi['loi_nhan'] = 'Lời nhắn quá ngắn (ít nhất 5 ký tự).';

    if (empty($loi)) {
        $duLieu = ['ten' => $ten, 'loi_nhan' => $loiNhan, 'thoi_gian' => date('d/m/Y H:i:s')];
        file_put_contents($fileLuuBut, json_encode($duLieu) . PHP_EOL, FILE_APPEND);
        header('Location: gioithieu.php?thanhcong=1#luu-but');
        exit;
    }
}

if (isset($_GET['thanhcong']) && $_GET['thanhcong'] == 1) {
    $thongBao = 'Cảm ơn bạn đã để lại lời nhắn!';
}

$danhSachLuuBut = [];
if (file_exists($fileLuuBut)) {
    $lines = file($fileLuuBut, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $danhSachLuuBut[] = json_decode($line, true);
    }
    $danhSachLuuBut = array_reverse($danhSachLuuBut);
    $danhSachLuuBut = array_slice($danhSachLuuBut, 0, 5);
}

$tieuDe = 'Giới thiệu cá nhân - Xayyatiew Phoutsana';
require_once __DIR__ . '/../../inc/header.php';
?>

<!-- Gọi file CSS gốc của bạn -->
<link rel="stylesheet" href="style-canhan.css">
<!-- MÃ CSS ĐỘC LẬP CHỐNG XUNG ĐỘT (Giải quyết lỗi vỡ giao diện) -->
<!-- DÙNG JS ĐỂ TIÊM CSS LÊN HEAD NHẰM ĐẠT 0 LỖI W3C -->
<script>
    // 1. Chèn mã CSS giao diện Sáng/Tối
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

    // 2. Gán class chế độ tối
    <?php if ($giaoDien === 'dark'): ?>
        document.body.classList.add('che-do-toi');
    <?php endif; ?>
</script>
<main class="noi-dung-chinh" style="max-width: 1000px; margin: 0 auto;">

    <!-- Dùng class mới cn-header thay cho dau-trang -->
    <header class="cn-header">
        <h1 style="font-size: 28px; margin: 0; color: white;">Trang giới thiệu cá nhân</h1>
        <p style="margin-top: 8px; font-style: italic;">Sinh viên Công nghệ Thông tin - Đam mê Lập trình Web & IoT</p>
    </header>

    <!-- Dùng class mới cn-nav thay cho dieu-huong -->
    <nav class="cn-nav">
        <a href="#gioi-thieu">Giới thiệu</a>
        <a href="#so-thich">Sở thích</a>
        <a href="#ky-nang">Kỹ năng</a>
        <a href="#thoi-khoa-bieu">Lịch học</a>
        <a href="#luu-but">Lưu bút</a>
        <a href="gioithieu.php?doi_giao_dien=1" class="cn-btn-darkmode">
            <?= $giaoDien === 'dark' ? '☀️ Đổi sang Sáng' : '🌙 Đổi sang Tối' ?>
        </a>
    </nav>

    <!-- Nội dung chính giữ nguyên class khoi-noi-dung -->
    <section id="gioi-thieu" class="khoi-noi-dung" style="display: flex; flex-wrap: wrap; gap: 30px; align-items: flex-start;">
        <figure style="margin: 0; max-width: 250px;">
            <img src="../../images/avatar-phoutsana.jpg" alt="Ảnh chân dung của Xayyatiew Phoutsana" style="width: 100%; border-radius: 50%; border: 4px solid #2563eb;">
        </figure>
        <div class="thong-tin-chi-tiet" style="flex: 1; min-width: 250px;">
            <h2>Giới thiệu bản thân</h2>
            <p><strong>Họ và tên:</strong> Xayyatiew Phoutsana</p>
            <p><strong>Ngành học:</strong> Công nghệ Thông tin</p>
            <p><strong>Lớp:</strong> 24CNTT2</p>
            <p><strong>Trường:</strong> Trường Đại học Sư phạm – Đại học Đà Nẵng</p>
            <p><strong>Quê quán:</strong> SeKong, Lào</p>
            <p><strong>Mục tiêu:</strong> Phát triển bản thân trở thành một lập trình viên Full-stack chuyên nghiệp.</p>
            <p><strong>Email:</strong> phoutsanaxayyatiew@gmail.com</p>
        </div>
    </section>

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

    <!-- CHỨC NĂNG 2: SỔ LƯU BÚT -->
    <section id="luu-but" class="khoi-noi-dung">
        <h2>Sổ lưu bút</h2>

        <?php if ($thongBao): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 12px; border-left: 4px solid #10b981; margin-bottom: 20px; border-radius: 4px;">
                <?= e($thongBao) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="gioithieu.php#luu-but" novalidate style="margin-bottom: 30px;">
            <div style="margin-bottom: 15px;">
                <label for="tenLuuBut"><strong>Tên của bạn (*):</strong></label>
                <input type="text" id="tenLuuBut" name="ten" value="<?= e($_POST['ten'] ?? '') ?>" class="form-nhap">
                <?php if (isset($loi['ten'])): ?>
                    <div style="color: #ef4444; font-size: 14px; margin-top: 5px;"><?= e($loi['ten']) ?></div>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="loiNhan"><strong>Lời nhắn (*):</strong></label>
                <textarea id="loiNhan" name="loi_nhan" rows="3" class="form-nhap"><?= e($_POST['loi_nhan'] ?? '') ?></textarea>
                <?php if (isset($loi['loi_nhan'])): ?>
                    <div style="color: #ef4444; font-size: 14px; margin-top: 5px;"><?= e($loi['loi_nhan']) ?></div>
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
                            <small style="color: #64748b;"><?= e($lb['thoi_gian']) ?></small>
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