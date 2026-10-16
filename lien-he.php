<?php
require_once __DIR__ . '/inc/config.php';

use App\Data\KhoLienHe;

// Đã bổ sung 3 trường dữ liệu mới vào mảng
$du  = ['hoten' => '', 'email' => '', 'sdt' => '', 'tuoi' => '', 'ngay' => '', 'chude' => '', 'noidung' => ''];
$loi = [];
$tenAnh = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Lấy và dọn dẹp dữ liệu
    $du['hoten']   = trim($_POST['hoten'] ?? '');
    $du['email']   = trim($_POST['email'] ?? '');
    $du['sdt']     = trim($_POST['sdt'] ?? '');
    $du['noidung'] = trim($_POST['noidung'] ?? '');

    // Lấy dữ liệu 3 trường mới
    $du['tuoi']    = trim($_POST['tuoi'] ?? '');
    $du['ngay']    = trim($_POST['ngay'] ?? '');
    $du['chude']   = trim($_POST['chude'] ?? '');

    // 2. Kiểm tra lỗi
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

    // 3. Xử lý Upload file ảnh
    if (isset($_FILES['anh']) && $_FILES['anh']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['anh'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $loi['anh'] = 'Có lỗi xảy ra khi tải tệp lên.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $loi['anh'] = 'Kích thước tệp không được vượt quá 2MB.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $choPhep = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/webp' => '.webp'];

            if (!array_key_exists($mime, $choPhep)) {
                $loi['anh'] = 'Chỉ chấp nhận tệp hình ảnh (JPG, PNG, WebP).';
            } else {
                $duoiFile = $choPhep[$mime];
                $tenAnh = uniqid('lienhe_', true) . $duoiFile;
            }
        }
    }

    // 4. Lưu vào JSONL
    if (empty($loi)) {
        if ($tenAnh !== null) {
            move_uploaded_file($_FILES['anh']['tmp_name'], __DIR__ . '/uploads/' . $tenAnh);
        }

        $kho = new KhoLienHe(__DIR__ . '/storage/lien-he.jsonl');
        $kho->them([
            'thoiGian' => date('Y-m-d H:i:s'),
            'hoTen'    => $du['hoten'],
            'email'    => $du['email'],
            'sdt'      => $du['sdt'],
            'tuoi'     => $du['tuoi'],     // Ghi dữ liệu mới
            'ngay'     => $du['ngay'],     // Ghi dữ liệu mới
            'chuDe'    => $du['chude'],    // Ghi dữ liệu mới
            'noiDung'  => $du['noidung'],
            'anh'      => $tenAnh,
        ]);

        $_SESSION['flash'] = 'Đã gửi liên hệ thành công, cảm ơn bạn!';
        header('Location: lien-he.php');
        exit;
    }
}

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

    <form method="POST" action="lien-he.php" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">

        <div>
            <label for="hoten" style="font-weight: bold; display: block; margin-bottom: 5px;">Họ tên *</label>
            <input type="text" id="hoten" name="hoten" value="<?= e($du['hoten']) ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
            <?php if (isset($loi['hoten'])): ?><span style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"><?= e($loi['hoten']) ?></span><?php endif; ?>
        </div>

        <div>
            <label for="email" style="font-weight: bold; display: block; margin-bottom: 5px;">Email *</label>
            <input type="email" id="email" name="email" value="<?= e($du['email']) ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
            <?php if (isset($loi['email'])): ?><span style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"><?= e($loi['email']) ?></span><?php endif; ?>
        </div>

        <div>
            <label for="sdt" style="font-weight: bold; display: block; margin-bottom: 5px;">Số điện thoại *</label>
            <input type="text" id="sdt" name="sdt" value="<?= e($du['sdt']) ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
            <?php if (isset($loi['sdt'])): ?><span style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"><?= e($loi['sdt']) ?></span><?php endif; ?>
        </div>

        <!-- BỔ SUNG 3 TRƯỜNG MỚI VÀO FORM HTML -->
        <div>
            <label for="tuoi" style="font-weight: bold; display: block; margin-bottom: 5px;">Tuổi</label>
            <input type="number" id="tuoi" name="tuoi" value="<?= e($du['tuoi']) ?>" min="10" max="100" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div>
            <label for="ngay" style="font-weight: bold; display: block; margin-bottom: 5px;">Ngày liên hệ</label>
            <input type="date" id="ngay" name="ngay" value="<?= $du['ngay'] !== '' ? e($du['ngay']) : date('Y-m-d') ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div>
            <label for="chude" style="font-weight: bold; display: block; margin-bottom: 5px;">Chủ đề</label>
            <select id="chude" name="chude" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
                <option value="Tư vấn mua hàng" <?= $du['chude'] === 'Tư vấn mua hàng' ? 'selected' : '' ?>>Tư vấn mua hàng</option>
                <option value="Đổi trả sản phẩm" <?= $du['chude'] === 'Đổi trả sản phẩm' ? 'selected' : '' ?>>Đổi trả sản phẩm</option>
                <option value="Khiếu nại dịch vụ" <?= $du['chude'] === 'Khiếu nại dịch vụ' ? 'selected' : '' ?>>Khiếu nại dịch vụ</option>
                <option value="Khác" <?= $du['chude'] === 'Khác' ? 'selected' : '' ?>>Khác</option>
            </select>
        </div>
        <!-- KẾT THÚC BỔ SUNG -->

        <div>
            <label for="anh" style="font-weight: bold; display: block; margin-bottom: 5px;">Ảnh đính kèm (Tùy chọn, tối đa 2MB)</label>
            <input type="file" id="anh" name="anh" accept="image/jpeg, image/png, image/webp" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
            <?php if (isset($loi['anh'])): ?><span style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"><?= e($loi['anh']) ?></span><?php endif; ?>
        </div>

        <div>
            <label for="noidung" style="font-weight: bold; display: block; margin-bottom: 5px;">Nội dung *</label>
            <textarea id="noidung" name="noidung" rows="5" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box; resize: vertical;"><?= e($du['noidung']) ?></textarea>
            <?php if (isset($loi['noidung'])): ?><span style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"><?= e($loi['noidung']) ?></span><?php endif; ?>
        </div>

        <button type="submit" class="nut-bam nut-bam--chinh" style="padding: 12px; font-size: 1.1rem; cursor: pointer; border: none; border-radius: 4px;">Gửi liên hệ</button>
    </form>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>