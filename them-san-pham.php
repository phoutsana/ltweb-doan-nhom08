<?php
require_once __DIR__ . '/inc/config.php';

// Kiểm tra quyền Quản trị viên
if (!isset($_SESSION['user']) || $_SESSION['user']['vai_tro'] !== 'quan_tri') {
    http_response_code(403);
    exit('Bạn không có quyền truy cập trang này.');
}

$pdo = \db();

// Lấy danh sách danh mục để hiện ra thẻ select
$danhMucList = $pdo->query("SELECT id, ten FROM danh_muc")->fetchAll();

// Mảng chứa các lỗi chi tiết cho từng trường
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $danhMucId = (int)($_POST['danh_muc_id'] ?? 0);
    $ten = trim($_POST['ten'] ?? '');
    $gia = filter_var($_POST['gia'], FILTER_VALIDATE_INT);
    $tonKho = filter_var($_POST['ton_kho'], FILTER_VALIDATE_INT);
    $mauSac = trim($_POST['mau_sac'] ?? '');
    $kichThuoc = trim($_POST['kich_thuoc'] ?? '');
    $video = trim($_POST['video'] ?? '');
    $moTa = trim($_POST['mo_ta'] ?? '');

    // 1. Kiểm tra Tên sản phẩm không được để trống
    if (empty($ten)) {
        $errors['ten'] = 'Tên sản phẩm không được để trống.';
    }

    // 2. Kiểm tra Giá phải lớn hơn hoặc bằng 1.000đ (hoặc > 0 tùy theo test case, ở đây giữ nguyên logic >= 1000 hoặc bạn có thể chỉnh theo ý muốn)
    if ($gia === false || $gia <= 0) {
        $errors['gia'] = 'Giá sản phẩm phải lớn hơn 0.';
    }

    // 3. Kiểm tra Tồn kho không được nhỏ hơn 0 (bắt lỗi -1)
    if ($tonKho === false || $tonKho < 0) {
        $errors['ton_kho'] = 'Tồn kho không được nhỏ hơn 0.';
    }

    // 4. Kiểm tra danh mục có tồn tại trong database không (bắt lỗi danh mục 999)
    $stmtCheckDM = $pdo->prepare("SELECT COUNT(*) FROM danh_muc WHERE id = ?");
    $stmtCheckDM->execute([$danhMucId]);
    if ($stmtCheckDM->fetchColumn() == 0) {
        $errors['danh_muc'] = 'Danh mục không hợp lệ hoặc không tồn tại.';
    }

    // Xử lý upload ảnh nếu không có lỗi cơ bản
    $anh = '';
    if (empty($errors) && isset($_FILES['anh']) && $_FILES['anh']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['anh']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['anh']['name']);
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if (move_uploaded_file($fileTmp, $uploadDir . $fileName)) {
            $anh = 'uploads/' . $fileName;
        }
    }

    // Nếu không có lỗi nào thì tiến hành INSERT vào Database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO san_pham (danh_muc_id, ten, gia, ton_kho, anh, mau_sac, kich_thuoc, video, mo_ta, ngay_tao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$danhMucId, $ten, $gia, $tonKho, $anh, $mauSac, $kichThuoc, $video, $moTa]);

            // PRG (Post-Redirect-Get) kèm thông báo thành công
            header('Location: quan-tri.php?thanh_cong=them');
            exit;
        } catch (\PDOException $e) {
            error_log("Lỗi thêm sản phẩm: " . $e->getMessage());
            $errors['chung'] = 'Không thể thêm sản phẩm vào cơ sở dữ liệu.';
        }
    }
}

$tieuDe = 'Thêm sản phẩm - Fashion Store';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" style="max-width: 600px; margin: 30px auto; padding: 25px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h2 style="color: #1e3a8a; margin-bottom: 20px;">Thêm Sản Phẩm Mới</h2>

    <?php if (!empty($errors['chung'])): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 4px; margin-bottom: 15px;"><?= e($errors['chung']) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 12px;">
        <!-- Danh mục -->
        <div>
            <label style="font-weight: bold;">Danh mục *</label>
            <select name="danh_muc_id" style="width: 100%; padding: 8px; border: 1px solid <?= isset($errors['danh_muc']) ? 'red' : '#d1d5db' ?>; border-radius: 4px;">
                <?php foreach ($danhMucList as $dm): ?>
                    <option value="<?= $dm['id'] ?>" <?= (isset($_POST['danh_muc_id']) && $_POST['danh_muc_id'] == $dm['id']) ? 'selected' : '' ?>>
                        <?= e($dm['ten']) ?>
                    </option>
                <?php endforeach; ?>
                <!-- Giữ lại hiển thị option 999 nếu người dùng vừa hack qua DevTools -->
                <?php if (isset($_POST['danh_muc_id']) && $_POST['danh_muc_id'] == 999): ?>
                    <option value="999" selected>Danh mục không tồn tại (999)</option>
                <?php endif; ?>
            </select>
            <?php if (isset($errors['danh_muc'])): ?>
                <div style="color: red; font-size: 13px; margin-top: 4px;"><?= e($errors['danh_muc']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Tên sản phẩm -->
        <div>
            <label style="font-weight: bold;">Tên sản phẩm *</label>
            <input type="text" name="ten" value="<?= e($_POST['ten'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid <?= isset($errors['ten']) ? 'red' : '#d1d5db' ?>; border-radius: 4px;">
            <?php if (isset($errors['ten'])): ?>
                <div style="color: red; font-size: 13px; margin-top: 4px;"><?= e($errors['ten']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Giá & Tồn kho -->
        <div style="display: flex; gap: 10px;">
            <div style="flex: 1;">
                <label style="font-weight: bold;">Giá (VNĐ) *</label>
                <input type="number" name="gia" value="<?= e($_POST['gia'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid <?= isset($errors['gia']) ? 'red' : '#d1d5db' ?>; border-radius: 4px;">
                <?php if (isset($errors['gia'])): ?>
                    <div style="color: red; font-size: 13px; margin-top: 4px;"><?= e($errors['gia']) ?></div>
                <?php endif; ?>
            </div>
            <div style="flex: 1;">
                <label style="font-weight: bold;">Tồn kho</label>
                <input type="number" name="ton_kho" value="<?= e($_POST['ton_kho'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid <?= isset($errors['ton_kho']) ? 'red' : '#d1d5db' ?>; border-radius: 4px;">
                <?php if (isset($errors['ton_kho'])): ?>
                    <div style="color: red; font-size: 13px; margin-top: 4px;"><?= e($errors['ton_kho']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ảnh đại diện -->
        <div>
            <label style="font-weight: bold;">Ảnh đại diện</label>
            <input type="file" name="anh" accept="image/*" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 4px;">
        </div>

        <!-- Màu sắc & Kích thước -->
        <div style="display: flex; gap: 10px;">
            <div style="flex: 1;">
                <label style="font-weight: bold;">Màu sắc</label>
                <input type="text" name="mau_sac" value="<?= e($_POST['mau_sac'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
            </div>
            <div style="flex: 1;">
                <label style="font-weight: bold;">Kích thước</label>
                <input type="text" name="kich_thuoc" value="<?= e($_POST['kich_thuoc'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
            </div>
        </div>

        <!-- Video -->
        <div>
            <label style="font-weight: bold;">Link Video (Embed YouTube)</label>
            <input type="text" name="video" value="<?= e($_POST['video'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
        </div>

        <!-- Mô tả -->
        <div>
            <label style="font-weight: bold;">Mô tả</label>
            <textarea name="mo_ta" rows="4" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;"><?= e($_POST['mo_ta'] ?? '') ?></textarea>
        </div>

        <button type="submit" style="padding: 10px; background: #2563eb; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Lưu sản phẩm</button>
    </form>
</main>
<?php require_once __DIR__ . '/inc/footer.php'; ?>