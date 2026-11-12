<?php
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

use App\Data\KhoLienHe;

$du  = ['hoten' => '', 'email' => '', 'sdt' => '', 'tuoi' => '', 'ngay' => '', 'chude' => '', 'noidung' => ''];
$loi = [];
$tenAnh = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    // 1. Nhận dữ liệu (Hỗ trợ cả name cũ và name mới từ JS)
    $du['hoten']   = trim($_POST['fullname'] ?? $_POST['hoten'] ?? '');
    $du['email']   = trim($_POST['email'] ?? '');
    $du['sdt']     = trim($_POST['sdt'] ?? '');
    $du['noidung'] = trim($_POST['message'] ?? $_POST['noidung'] ?? '');
    $du['tuoi']    = trim($_POST['tuoi'] ?? '');
    $du['ngay']    = trim($_POST['ngay'] ?? date('Y-m-d'));
    $du['chude']   = trim($_POST['chude'] ?? 'Khác');

    // 2. Validate dữ liệu
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

    // Xử lý ảnh đính kèm (nếu có)
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

    // 3. Kiểm tra xem request này có phải từ AJAX (fetch JS) gửi lên không
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
        || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false); // formData qua fetch

    // 4. Trả kết quả về
    if (!empty($loi)) {
        if ($isAjax) {
            http_response_code(400); // Báo lỗi cho fetch
            echo json_encode(['thanh_cong' => false, 'loi' => $loi]);
            exit;
        }
    } else {
        // Lưu ảnh nếu có
        if ($tenAnh !== null) {
            $duongDanUpload = __DIR__ . '/uploads/';
            if (!is_dir($duongDanUpload)) {
                mkdir($duongDanUpload, 0755, true);
            }
            if (!move_uploaded_file($_FILES['anh']['tmp_name'], $duongDanUpload . $tenAnh)) {
                $tenAnh = null;
            }
        }

        // Lưu thông tin liên hệ vào file JSONL
        if (!is_dir(__DIR__ . '/storage/')) {
            mkdir(__DIR__ . '/storage/', 0755, true);
        }

        $kho = new KhoLienHe();
        $kho->them([
            'thoiGian' => date('Y-m-d H:i:s'),
            'hoTen'    => $du['hoten'],
            'email'    => $du['email'],
            'sdt'      => $du['sdt'],
            'tuoi'     => $du['tuoi'],
            'ngay'     => $du['ngay'],
            'chuDe'    => $du['chude'],
            'noiDung'  => $du['noidung'],
            'anh'      => $tenAnh,
        ]);

        if ($isAjax) {
            echo json_encode(['thanh_cong' => true, 'thong_bao' => 'Đã gửi liên hệ thành công!']);
            exit;
        }

        // Chạy phòng hờ nếu JS bị vô hiệu hóa
        $_SESSION['flash'] = 'Đã gửi liên hệ thành công, cảm ơn bạn!';
        header('Location: lien-he.php');
        exit;
    }
}

$tb = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$tieuDe = 'Liên hệ - Fashion Store';
$trang = 'lien-he';
$cssRieng = 'css/lien-he-bt7-csp.css';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" data-bt7-lh="lh001">
    <h2 data-bt7-lh="lh002">Gửi liên hệ cho chúng tôi</h2>

    <?php if ($tb): ?>
        <div data-bt7-lh="lh003">
            <strong>Thành công!</strong> <?= e($tb) ?>
        </div>
    <?php endif; ?>

    <!-- Nơi JS sẽ in thông báo thành công hoặc thất bại -->
    <div id="ket-qua-form" data-bt7-lh="lh004"></div>

    <form id="form-lien-he" method="POST" action="lien-he.php" enctype="multipart/form-data" data-bt7-lh="lh005">
        <?= csrf_field() ?>
        <div>
            <label for="fullname" data-bt7-lh="lh006">Họ tên *</label>
            <input type="text" id="fullname" name="fullname" value="<?= e($du['hoten']) ?>" required minlength="3" data-bt7-lh="lh007">
            <span id="loi-fullname" data-bt7-lh="lh008"><?= isset($loi['hoten']) ? e($loi['hoten']) : '' ?></span>
        </div>

        <div>
            <label for="email" data-bt7-lh="lh009">Email *</label>
            <input type="email" id="email" name="email" value="<?= e($du['email']) ?>" required data-bt7-lh="lh010">
            <span id="loi-email" data-bt7-lh="lh011"><?= isset($loi['email']) ? e($loi['email']) : '' ?></span>
        </div>

        <div>
            <label for="sdt" data-bt7-lh="lh012">Số điện thoại *</label>
            <input type="text" id="sdt" name="sdt" value="<?= e($du['sdt']) ?>" required data-bt7-lh="lh013">
            <?php if (isset($loi['sdt'])): ?><span data-bt7-lh="lh014"><?= e($loi['sdt']) ?></span><?php endif; ?>
        </div>

        <div>
            <label for="tuoi" data-bt7-lh="lh015">Tuổi</label>
            <input type="number" id="tuoi" name="tuoi" value="<?= e($du['tuoi']) ?>" min="10" max="100" data-bt7-lh="lh016">
        </div>

        <div>
            <label for="ngay" data-bt7-lh="lh017">Ngày liên hệ</label>
            <input type="date" id="ngay" name="ngay" value="<?= $du['ngay'] !== '' ? e($du['ngay']) : date('Y-m-d') ?>" data-bt7-lh="lh018">
        </div>

        <div>
            <label for="chude" data-bt7-lh="lh019">Chủ đề</label>
            <select id="chude" name="chude" data-bt7-lh="lh020">
                <option value="Tư vấn mua hàng" <?= $du['chude'] === 'Tư vấn mua hàng' ? 'selected' : '' ?>>Tư vấn mua hàng</option>
                <option value="Đổi trả sản phẩm" <?= $du['chude'] === 'Đổi trả sản phẩm' ? 'selected' : '' ?>>Đổi trả sản phẩm</option>
                <option value="Khiếu nại dịch vụ" <?= $du['chude'] === 'Khiếu nại dịch vụ' ? 'selected' : '' ?>>Khiếu nại dịch vụ</option>
                <option value="Khác" <?= $du['chude'] === 'Khác' ? 'selected' : '' ?>>Khác</option>
            </select>
        </div>

        <div>
            <label for="anh" data-bt7-lh="lh021">Ảnh đính kèm (Tùy chọn, tối đa 2MB)</label>
            <input type="file" id="anh" name="anh" accept="image/jpeg, image/png, image/webp" data-bt7-lh="lh022">
            <?php if (isset($loi['anh'])): ?><span data-bt7-lh="lh023"><?= e($loi['anh']) ?></span><?php endif; ?>
        </div>

        <div>
            <label for="message" data-bt7-lh="lh024">Nội dung *</label>
            <textarea id="message" name="message" rows="5" required minlength="10" data-bt7-lh="lh025"><?= e($du['noidung']) ?></textarea>
            <span id="loi-message" data-bt7-lh="lh026"><?= isset($loi['noidung']) ? e($loi['noidung']) : '' ?></span>
        </div>

        <button type="submit" id="nut-gui-lien-he" class="nut-bam nut-bam--chinh" data-bt7-lh="lh027">Gửi liên hệ</button>
    </form>
</main>

<!-- Bắt buộc nạp file JS để xử lý gửi Form bằng AJAX -->
<script type="module" src="js/trang-lien-he.js"></script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>