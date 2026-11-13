<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

// BT7: Chi quan tri vien moi duoc them san pham.
$nguoiDung = $_SESSION['user'] ?? null;
if (!is_array($nguoiDung) || ($nguoiDung['vai_tro'] ?? null) !== 'quan_tri') {
    http_response_code(403);
    exit('Bạn không có quyền truy cập trang này.');
}

$pdo = db();
$danhMucList = $pdo->query('SELECT id, ten FROM danh_muc ORDER BY id ASC')
    ->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$form = [
    'danh_muc_id' => '',
    'ten' => '',
    'gia' => '',
    'ton_kho' => '',
    'mau_sac' => '',
    'kich_thuoc' => '',
    'video' => '',
    'mo_ta' => '',
];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    // Bat buoc kiem tra token truoc khi xu ly yeu cau thay doi du lieu.
    csrf_verify();

    // Chi lay chuoi: tranh loi TypeError khi nguoi dung gui mang tu POST.
    foreach (array_keys($form) as $field) {
        $value = $_POST[$field] ?? '';
        $form[$field] = is_string($value) ? trim($value) : '';
    }

    $danhMucId = filter_var(
        $form['danh_muc_id'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );
    $gia = filter_var(
        $form['gia'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );
    $tonKho = filter_var(
        $form['ton_kho'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 0]]
    );

    if ($form['ten'] === '') {
        $errors['ten'] = 'Tên sản phẩm không được để trống.';
    } elseif (strlen($form['ten']) > 255) {
        $errors['ten'] = 'Tên sản phẩm quá dài.';
    }

    if ($gia === false) {
        $errors['gia'] = 'Giá sản phẩm phải là số nguyên lớn hơn 0.';
    }
    if ($tonKho === false) {
        $errors['ton_kho'] = 'Tồn kho phải là số nguyên không âm.';
    }

    // Danh muc duoc kiem tra bang prepared statement.
    if ($danhMucId === false) {
        $errors['danh_muc'] = 'Danh mục không hợp lệ.';
    } else {
        $stmtCheckDM = $pdo->prepare('SELECT COUNT(*) FROM danh_muc WHERE id = ?');
        $stmtCheckDM->execute([$danhMucId]);
        if ((int) $stmtCheckDM->fetchColumn() === 0) {
            $errors['danh_muc'] = 'Danh mục không tồn tại.';
        }
    }

    // Chi chap nhan link YouTube HTTPS, tranh luu javascript:/data: URL.
    if ($form['video'] !== '') {
        $url = filter_var($form['video'], FILTER_VALIDATE_URL);
        $scheme = is_string($url) ? strtolower((string) parse_url($url, PHP_URL_SCHEME)) : '';
        $host = is_string($url) ? strtolower((string) parse_url($url, PHP_URL_HOST)) : '';
        $youtubeHosts = [
            'youtube.com', 'www.youtube.com',
            'youtube-nocookie.com', 'www.youtube-nocookie.com',
            'youtu.be', 'www.youtu.be',
        ];
        if ($url === false || $scheme !== 'https' || !in_array($host, $youtubeHosts, true)) {
            $errors['video'] = 'Chỉ chấp nhận liên kết YouTube HTTPS hợp lệ.';
        }
    }

    $anh = '';
    $savedFile = null;

    // Chi xu ly upload khi cac truong khac hop le.
    if ($errors === [] && isset($_FILES['anh'])) {
        $file = $_FILES['anh'];
        if (!is_array($file) || !isset($file['error']) || !is_int($file['error'])) {
            $errors['anh'] = 'Dữ liệu upload không hợp lệ.';
        } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors['anh'] = 'Upload ảnh thất bại.';
            } elseif (
                !isset($file['size'], $file['tmp_name']) ||
                !is_int($file['size']) ||
                !is_string($file['tmp_name']) ||
                $file['size'] < 1 ||
                $file['size'] > 2 * 1024 * 1024 ||
                !is_uploaded_file($file['tmp_name'])
            ) {
                $errors['anh'] = 'Tệp ảnh không hợp lệ hoặc vượt quá 2 MB.';
            } elseif (!extension_loaded('gd') || !extension_loaded('fileinfo')) {
                $errors['anh'] = 'Máy chủ thiếu thư viện xử lý ảnh.';
            } else {
                $tmp = $file['tmp_name'];
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
                $info = @getimagesize($tmp);
                $isJpeg = is_array($info) && ($info[2] ?? null) === IMAGETYPE_JPEG
                    && $mime === 'image/jpeg';
                $isPng = is_array($info) && ($info[2] ?? null) === IMAGETYPE_PNG
                    && $mime === 'image/png';

                if (!$isJpeg && !$isPng) {
                    $errors['anh'] = 'Chỉ chấp nhận ảnh JPG hoặc PNG hợp lệ.';
                } else {
                    $width = (int) $info[0];
                    $height = (int) $info[1];
                    if (
                        $width < 1 || $height < 1 ||
                        $width > 4000 || $height > 4000 ||
                        $width * $height > 12000000
                    ) {
                        $errors['anh'] = 'Kích thước ảnh không hợp lệ hoặc quá lớn.';
                    } else {
                        try {
                            $image = $isJpeg
                                ? @imagecreatefromjpeg($tmp)
                                : @imagecreatefrompng($tmp);
                        } catch (Throwable $e) {
                            $image = false;
                        }

                        if ($image === false) {
                            $errors['anh'] = 'Không đọc được nội dung ảnh.';
                        } else {
                            $extension = $isJpeg ? 'jpg' : 'png';
                            $filename = bin2hex(random_bytes(16)) . '.' . $extension;
                            $uploadDir = __DIR__ . '/uploads/';

                            if (
                                !is_dir($uploadDir) &&
                                !@mkdir($uploadDir, 0755, true) &&
                                !is_dir($uploadDir)
                            ) {
                                $errors['anh'] = 'Không tạo được thư mục upload.';
                            } else {
                                $destination = $uploadDir . $filename;
                                // Giai ma va tao lai file anh, khong luu ten/file goc.
                                if ($isPng) {
                                    imagealphablending($image, false);
                                    imagesavealpha($image, true);
                                    $saved = @imagepng($image, $destination, 6);
                                } else {
                                    $saved = @imagejpeg($image, $destination, 85);
                                }
                                if ($saved) {
                                    $anh = 'uploads/' . $filename;
                                    $savedFile = $destination;
                                } else {
                                    @unlink($destination);
                                    $errors['anh'] = 'Không lưu được ảnh.';
                                }
                            }
                            imagedestroy($image);
                        }
                    }
                }
            }
        }
    }

    if ($errors === []) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO san_pham
                 (danh_muc_id, ten, gia, ton_kho, anh,
                  mau_sac, kich_thuoc, video, mo_ta, ngay_tao)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
            );
            $stmt->execute([
                $danhMucId,
                $form['ten'],
                $gia,
                $tonKho,
                $anh,
                $form['mau_sac'],
                $form['kich_thuoc'],
                $form['video'],
                $form['mo_ta'],
            ]);

            // Giu duong dan chuyen huong tu ma goc cua nhom.
            header('Location: quan-tri.php?thanh_cong=them', true, 303);
            exit;
        } catch (PDOException $e) {
            // Neu DB that bai, xoa anh moi de tranh file rac.
            if (is_string($savedFile) && is_file($savedFile)) {
                @unlink($savedFile);
            }
            error_log('[BT7_THEM_SAN_PHAM] ' . $e->getMessage());
            $errors['chung'] = 'Không thể thêm sản phẩm vào cơ sở dữ liệu.';
        }
    }
}

$tieuDe = 'Thêm sản phẩm - Fashion Store';
$trang = 'them-san-pham';
$cssRieng = 'css/them-san-pham-bt7.css';
require_once __DIR__ . '/inc/header.php';
?>
<main class="noi-dung-chinh bt7-product-add">
    <h2 class="bt7-product-add__title">Thêm Sản Phẩm Mới</h2>

    <?php if (!empty($errors['chung'])): ?>
        <div class="bt7-product-add__error" role="alert"><?= e((string) $errors['chung']) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="bt7-product-add__form">
        <?= csrf_field() ?>

        <div class="bt7-product-add__field">
            <label for="danh_muc_id">Danh mục *</label>
            <select id="danh_muc_id" name="danh_muc_id"
                    class="bt7-product-add__control<?= isset($errors['danh_muc']) ? ' bt7-product-add__control--error' : '' ?>"
                    required>
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($danhMucList as $dm): ?>
                    <option value="<?= (int) $dm['id'] ?>"
                        <?= $form['danh_muc_id'] === (string) $dm['id'] ? 'selected' : '' ?>>
                        <?= e((string) $dm['ten']) ?>
                    </option>
                <?php endforeach; ?>
                <?php if ($form['danh_muc_id'] === '999'): ?>
                    <option value="999" selected>Danh mục không tồn tại (999)</option>
                <?php endif; ?>
            </select>
            <?php if (isset($errors['danh_muc'])): ?>
                <div class="bt7-product-add__field-error" role="alert"><?= e((string) $errors['danh_muc']) ?></div>
            <?php endif; ?>
        </div>

        <div class="bt7-product-add__field">
            <label for="ten">Tên sản phẩm *</label>
            <input id="ten" class="bt7-product-add__control<?= isset($errors['ten']) ? ' bt7-product-add__control--error' : '' ?>"
                   type="text" name="ten" maxlength="255" required value="<?= e($form['ten']) ?>">
            <?php if (isset($errors['ten'])): ?>
                <div class="bt7-product-add__field-error" role="alert"><?= e((string) $errors['ten']) ?></div>
            <?php endif; ?>
        </div>

        <div class="bt7-product-add__two-columns">
            <div class="bt7-product-add__field">
                <label for="gia">Giá (VNĐ) *</label>
                <input id="gia" class="bt7-product-add__control<?= isset($errors['gia']) ? ' bt7-product-add__control--error' : '' ?>"
                       type="number" name="gia" min="1" step="1" required value="<?= e($form['gia']) ?>">
                <?php if (isset($errors['gia'])): ?>
                    <div class="bt7-product-add__field-error" role="alert"><?= e((string) $errors['gia']) ?></div>
                <?php endif; ?>
            </div>
            <div class="bt7-product-add__field">
                <label for="ton_kho">Tồn kho *</label>
                <input id="ton_kho" class="bt7-product-add__control<?= isset($errors['ton_kho']) ? ' bt7-product-add__control--error' : '' ?>"
                       type="number" name="ton_kho" min="0" step="1" required value="<?= e($form['ton_kho']) ?>">
                <?php if (isset($errors['ton_kho'])): ?>
                    <div class="bt7-product-add__field-error" role="alert"><?= e((string) $errors['ton_kho']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bt7-product-add__field">
            <label for="anh">Ảnh đại diện (JPG/PNG, tối đa 2 MB)</label>
            <input id="anh" class="bt7-product-add__control" type="file" name="anh" accept="image/jpeg,image/png">
            <?php if (isset($errors['anh'])): ?>
                <div class="bt7-product-add__field-error" role="alert"><?= e((string) $errors['anh']) ?></div>
            <?php endif; ?>
        </div>

        <div class="bt7-product-add__two-columns">
            <div class="bt7-product-add__field">
                <label for="mau_sac">Màu sắc</label>
                <input id="mau_sac" class="bt7-product-add__control" type="text" name="mau_sac" value="<?= e($form['mau_sac']) ?>">
            </div>
            <div class="bt7-product-add__field">
                <label for="kich_thuoc">Kích thước</label>
                <input id="kich_thuoc" class="bt7-product-add__control" type="text" name="kich_thuoc" value="<?= e($form['kich_thuoc']) ?>">
            </div>
        </div>

        <div class="bt7-product-add__field">
            <label for="video">Link Video (Embed YouTube HTTPS)</label>
            <input id="video" class="bt7-product-add__control<?= isset($errors['video']) ? ' bt7-product-add__control--error' : '' ?>"
                   type="url" name="video" placeholder="https://www.youtube.com/embed/..." value="<?= e($form['video']) ?>">
            <?php if (isset($errors['video'])): ?>
                <div class="bt7-product-add__field-error" role="alert"><?= e((string) $errors['video']) ?></div>
            <?php endif; ?>
        </div>

        <div class="bt7-product-add__field">
            <label for="mo_ta">Mô tả</label>
            <textarea id="mo_ta" class="bt7-product-add__control" name="mo_ta" rows="4"><?= e($form['mo_ta']) ?></textarea>
        </div>

        <button type="submit" class="nut-bam nut-bam--chinh bt7-product-add__submit">Lưu sản phẩm</button>
    </form>
</main>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
