<?php
require_once __DIR__ . '/inc/config.php';

use App\Data\KhoLienHe;

$du  = ['hoten' => '', 'email' => '', 'sdt' => '', 'noidung' => ''];
$loi = [];
$tenAnh = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Lấy và dọn dẹp dữ liệu
    $du['hoten']   = trim($_POST['hoten'] ?? '');
    $du['email']   = trim($_POST['email'] ?? '');
    $du['sdt']     = trim($_POST['sdt'] ?? '');
    $du['noidung'] = trim($_POST['noidung'] ?? '');

    // 2. Kiểm tra lỗi từng ô
    if (mb_strlen($du['hoten']) < 3) {
        $loi['hoten'] = 'Họ tên phải có ít nhất 3 ký tự.';
    }
    if (!filter_var($du['email'], FILTER_VALIDATE_EMAIL)) {
        $loi['email'] = 'Địa chỉ email không hợp lệ.';
    }
    if (!preg_match('/^(0|\+84)[3|5|7|8|9][0-9]{8}$/', $du['sdt'])) {
        $loi['sdt'] = 'Số điện thoại không hợp lệ (phải là số VN).';
    }
    if (mb_strlen($du['noidung']) < 10) {
        $loi['noidung'] = 'Nội dung liên hệ quá ngắn (ít nhất 10 ký tự).';
    }

    // 3. Xử lý Upload file ảnh (nếu có)
    if (isset($_FILES['anh']) && $_FILES['anh']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['anh'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $loi['anh'] = 'Có lỗi xảy ra khi tải tệp lên.';
        } elseif ($file['size'] > 2 * 1024 * 1024) { // Tối đa 2MB
            $loi['anh'] = 'Kích thước tệp không được vượt quá 2MB.';
        } else {
            // Kiểm tra kiểu MIME thật bằng finfo
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $choPhep = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/webp' => '.webp'];

            if (!array_key_exists($mime, $choPhep)) {
                $loi['anh'] = 'Chỉ chấp nhận tệp hình ảnh (JPG, PNG, WebP).';
            } else {
                // Tạo tên ngẫu nhiên
                $duoiFile = $choPhep[$mime];
                $tenAnh = uniqid('lienhe_', true) . $duoiFile;
            }
        }
    }

    // 4. Nếu không có lỗi, lưu vào JSONL và chuyển hướng (PRG)
    if (empty($loi)) {
        // Nếu có ảnh hợp lệ, di chuyển ảnh vào thư mục uploads
        if ($tenAnh !== null) {
            move_uploaded_file($_FILES['anh']['tmp_name'], __DIR__ . '/uploads/' . $tenAnh);
        }

        $kho = new KhoLienHe(__DIR__ . '/storage/lien-he.jsonl');
        $kho->them([
            'thoiGian' => date('Y-m-d H:i:s'),
            'hoTen'    => $du['hoten'],
            'email'    => $du['email'],
            'sdt'      => $du['sdt'],
            'noiDung'  => $du['noidung'],
            'anh'      => $tenAnh, // Tên file ngẫu nhiên hoặc null
        ]);

        $_SESSION['flash'] = 'Đã gửi liên hệ thành công, cảm ơn bạn!';
        header('Location: lien-he.php'); // Redirect để tránh F5 gửi lại form
        exit;
    }
}

// Lấy thông báo thành công (chỉ hiện 1 lần)
$tb = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$tieuDe = 'Liên hệ - Fashion Store';
$trang = 'lien-he';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" style="max-width: 800px; margin: 30px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h2 style="color: #1e3a8a; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 20px;">Gửi liên hệ cho chúng tôi</h2>

    <?php if ($tb): ?>
        <div style="background-color: #d1fae5; color: #065f46; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #10b981;">
            <strong>Thành công!</strong> <?= e($tb) ?>
        </div>
    <?php endif; ?>

    <!-- Lưu ý bắt buộc có enctype="multipart/form-data" để gửi được file -->
    <form method="POST" action="lien-he.php" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">

        <div>
            <label for="hoten" style="font-weight: bold; display: block; margin-bottom: 5px;">Họ tên *</label>
            <input type="text" id="hoten" name="hoten" value="<?= e($du['hoten']) ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
            <?php if (isset($loi['hoten'])): ?>
                <span style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"><?= e($loi['hoten']) ?></span>
            <?php endif; ?>
        </div>

        <div>
            <label for="email" style="font-weight: bold; display: block; margin-bottom: 5px;">Email *</label>
            <input type="email" id="email" name="email" value="<?= e($du['email']) ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
            <?php if (isset($loi['email'])): ?>
                <span style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"><?= e($loi['email']) ?></span>
            <?php endif; ?>
        </div>

        <div>
            <label for="sdt" style="font-weight: bold; display: block; margin-bottom: 5px;">Số điện thoại *</label>
            <input type="text" id="sdt" name="sdt" value="<?= e($du['sdt']) ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
            <?php if (isset($loi['sdt'])): ?>
                <span style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"><?= e($loi['sdt']) ?></span>
            <?php endif; ?>
        </div>

        <div>
            <label for="anh" style="font-weight: bold; display: block; margin-bottom: 5px;">Ảnh đính kèm (Tùy chọn, tối đa 2MB)</label>
            <input type="file" id="anh" name="anh" accept="image/jpeg, image/png, image/webp" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
            <?php if (isset($loi['anh'])): ?>
                <span style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"><?= e($loi['anh']) ?></span>
            <?php endif; ?>
        </div>

        <div>
            <label for="noidung" style="font-weight: bold; display: block; margin-bottom: 5px;">Nội dung *</label>
            <textarea id="noidung" name="noidung" rows="5" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box; resize: vertical;"><?= e($du['noidung']) ?></textarea>
            <?php if (isset($loi['noidung'])): ?>
                <span style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"><?= e($loi['noidung']) ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="nut-bam nut-bam--chinh" style="padding: 12px; font-size: 1.1rem; cursor: pointer; border: none; border-radius: 4px;">Gửi liên hệ</button>
    </form>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>