<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/config.php';

// Kiểm tra phân quyền Quản trị viên an toàn
if (!isset($_SESSION['user']) || $_SESSION['user']['vai_tro'] !== 'quan_tri') {
    http_response_code(403);
    if (file_exists(__DIR__ . '/403.php')) {
        require __DIR__ . '/403.php';
    } else {
        echo "Bạn không có quyền truy cập trang này!";
    }
    exit;
}

$pdo = \db();
$thanhCongMsg = '';
$loiMsg = '';
$errors = [];

// --- XỬ LÝ THÊM SẢN PHẨM MỚI (Tích hợp ngay trong trang) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'them_san_pham') {
    $danhMucId = (int)($_POST['danh_muc_id'] ?? 0);
    $ten = trim($_POST['ten'] ?? '');
    $gia = filter_var($_POST['gia'], FILTER_VALIDATE_INT);
    $tonKho = filter_var($_POST['ton_kho'], FILTER_VALIDATE_INT);
    $mauSac = trim($_POST['mau_sac'] ?? '');
    $kichThuoc = trim($_POST['kich_thuoc'] ?? '');
    $video = trim($_POST['video'] ?? '');
    $moTa = trim($_POST['mo_ta'] ?? '');

    if (empty($ten)) {
        $errors['ten'] = 'Tên sản phẩm không được để trống.';
    }
    if ($gia === false || $gia <= 0) {
        $errors['gia'] = 'Giá sản phẩm phải lớn hơn 0.';
    }
    if ($tonKho === false || $tonKho < 0) {
        $errors['ton_kho'] = 'Tồn kho không được nhỏ hơn 0.';
    }

    $stmtCheckDM = $pdo->prepare("SELECT COUNT(*) FROM danh_muc WHERE id = ?");
    $stmtCheckDM->execute([$danhMucId]);
    if ($stmtCheckDM->fetchColumn() == 0) {
        $errors['danh_muc'] = 'Danh mục không hợp lệ hoặc không tồn tại.';
    }

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

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO san_pham (danh_muc_id, ten, gia, ton_kho, anh, mau_sac, kich_thuoc, video, mo_ta, ngay_tao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$danhMucId, $ten, $gia, $tonKho, $anh, $mauSac, $kichThuoc, $video, $moTa]);

            header('Location: quan-tri.php?tab=san-pham&thanh_cong=them_sp');
            exit;
        } catch (\PDOException $e) {
            $errors['chung'] = 'Không thể thêm sản phẩm vào cơ sở dữ liệu.';
        }
    }
}

// --- XỬ LÝ XÓA LIÊN HỆ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'xoa_lien_he') {
    $idLienHe = (int)($_POST['id'] ?? 0);
    if ($idLienHe > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM lien_he WHERE id = ?");
            $stmt->execute([$idLienHe]);
            header('Location: quan-tri.php?tab=lien-he&thanh_cong=xoa_lh');
            exit;
        } catch (\Exception $e) {
            $loiMsg = 'Không thể xóa liên hệ này.';
        }
    }
}

// Lấy tab hiện tại (Mặc định là tổng quan)
$tab = $_GET['tab'] ?? 'tong-quan';

// Lấy dữ liệu thống kê tổng quan
$tongSanPham = $pdo->query("SELECT COUNT(*) FROM san_pham")->fetchColumn();
$tongDonHang = $pdo->query("SELECT COUNT(*) FROM don_hang")->fetchColumn();
$tongLienHe = $pdo->query("SELECT COUNT(*) FROM lien_he")->fetchColumn();
$danhMucList = $pdo->query("SELECT id, ten FROM danh_muc")->fetchAll();

$tieuDe = 'Trang Quản Trị Hệ Thống - Fashion Store';
$trang = 'quan-tri';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" style="max-width: 1200px; margin: 30px auto; padding: 20px;">

    <!-- TIÊU ĐỀ VÀ ĐĂNG XUẤT -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 2px solid #2563eb; padding-bottom: 10px;">
        <h2 style="color: #1e3a8a; margin: 0;">Trang Quản Trị Hệ Thống (Admin Dashboard)</h2>
        <a href="dang-xuat.php" style="background-color: #dc2626; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold;">Đăng xuất</a>
    </div>

    <!-- THÔNG BÁO THÀNH CÔNG -->
    <?php if (isset($_GET['thanh_cong'])): ?>
        <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: bold; border: 1px solid #34d399;">
            Thao tác thành công!
        </div>
    <?php endif; ?>

    <!-- THANH MENU TAB CHUYỂN ĐỔI QUẢN TRỊ -->
    <div style="display: flex; gap: 10px; margin-bottom: 30px; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; flex-wrap: wrap;">
        <a href="quan-tri.php?tab=tong-quan" style="padding: 10px 20px; background: <?= $tab === 'tong-quan' ? '#2563eb' : '#f3f4f6' ?>; color: <?= $tab === 'tong-quan' ? 'white' : '#374151' ?>; border-radius: 6px; text-decoration: none; font-weight: bold;">📊 Tổng quan</a>
        <a href="quan-tri.php?tab=san-pham" style="padding: 10px 20px; background: <?= $tab === 'san-pham' ? '#2563eb' : '#f3f4f6' ?>; color: <?= $tab === 'san-pham' ? 'white' : '#374151' ?>; border-radius: 6px; text-decoration: none; font-weight: bold;">📦 Quản lý Sản phẩm</a>
        <a href="quan-tri.php?tab=them-san-pham" style="padding: 10px 20px; background: <?= $tab === 'them-san-pham' ? '#2563eb' : '#f3f4f6' ?>; color: <?= $tab === 'them-san-pham' ? 'white' : '#374151' ?>; border-radius: 6px; text-decoration: none; font-weight: bold;">➕ Thêm sản phẩm</a>
        <a href="quan-tri.php?tab=don-hang" style="padding: 10px 20px; background: <?= $tab === 'don-hang' ? '#2563eb' : '#f3f4f6' ?>; color: <?= $tab === 'don-hang' ? 'white' : '#374151' ?>; border-radius: 6px; text-decoration: none; font-weight: bold;">🛍️ Quản lý Đơn hàng</a>
        <a href="quan-tri.php?tab=lien-he" style="padding: 10px 20px; background: <?= $tab === 'lien-he' ? '#2563eb' : '#f3f4f6' ?>; color: <?= $tab === 'lien-he' ? 'white' : '#374151' ?>; border-radius: 6px; text-decoration: none; font-weight: bold;">✉️ Quản lý Liên hệ</a>
    </div>

    <!-- NỘI DUNG HIỂN THỊ THEO TAB -->
    <?php if ($tab === 'tong-quan'): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 10px 0; color: #4b5563; font-size: 1rem;">Tổng số sản phẩm</h3>
                <p style="font-size: 2rem; font-weight: bold; color: #2563eb; margin: 0;"><?= $tongSanPham ?></p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 10px 0; color: #4b5563; font-size: 1rem;">Tổng số đơn hàng</h3>
                <p style="font-size: 2rem; font-weight: bold; color: #16a34a; margin: 0;"><?= $tongDonHang ?></p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 10px 0; color: #4b5563; font-size: 1rem;">Ý kiến liên hệ</h3>
                <p style="font-size: 2rem; font-weight: bold; color: #ca8a04; margin: 0;"><?= $tongLienHe ?></p>
            </div>
        </div>

    <?php elseif ($tab === 'san-pham'): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0; color: #1e3a8a;">Danh sách sản phẩm trong hệ thống</h3>
            <a href="quan-tri.php?tab=them-san-pham" style="background: #16a34a; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold;">+ Thêm sản phẩm mới</a>
        </div>
        <?php
            $dsSanPham = $pdo->query("SELECT * FROM san_pham ORDER BY id DESC")->fetchAll();
        ?>
        <div style="background: white; border-radius: 8px; border: 1px solid #e5e7eb; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
                <thead style="background: #f8fafc; text-align: left;">
                    <tr>
                        <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">ID</th>
                        <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">Ảnh</th>
                        <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">Tên sản phẩm</th>
                        <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">Giá</th>
                        <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">Tồn kho</th>
                        <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dsSanPham as $sp): ?>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px;">#<?= $sp['id'] ?></td>
                            <td style="padding: 12px;"><img src="<?= e($sp['anh']) ?>" style="width: 40px; height: 40px; object-fit: contain;"></td>
                            <td style="padding: 12px; font-weight: bold;"><?= e($sp['ten']) ?></td>
                            <td style="padding: 12px; color: #dc2626; font-weight: bold;"><?= number_format((float)$sp['gia'], 0, ',', '.') ?> đ</td>
                            <td style="padding: 12px;"><?= $sp['ton_kho'] ?></td>
                            <td style="padding: 12px; text-align: center;">
                                <a href="sua-san-pham.php?id=<?= $sp['id'] ?>" style="color: #2563eb; text-decoration: none; margin-right: 10px; font-weight: bold;">Sửa</a>
                                <a href="xoa-san-pham.php?id=<?= $sp['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa?');" style="color: #dc2626; text-decoration: none; font-weight: bold;">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($tab === 'them-san-pham'): ?>
        <!-- TAB THÊM SẢN PHẨM MỚI -->
        <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #e5e7eb; max-width: 800px; margin: 0 auto; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="color: #1e3a8a; margin-top: 0; margin-bottom: 20px;">Thêm Sản Phẩm Mới</h3>

            <?php if (!empty($errors['chung'])): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 4px; margin-bottom: 15px;"><?= e($errors['chung']) ?></div>
            <?php endif; ?>

            <form method="POST" action="quan-tri.php?tab=them-san-pham" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">
                <input type="hidden" name="action" value="them_san_pham">
                
                <div>
                    <label style="font-weight: bold;">Danh mục *</label>
                    <select name="danh_muc_id" style="width: 100%; padding: 8px; border: 1px solid <?= isset($errors['danh_muc']) ? 'red' : '#d1d5db' ?>; border-radius: 4px;">
                        <?php foreach ($danhMucList as $dm): ?>
                            <option value="<?= $dm['id'] ?>" <?= (isset($_POST['danh_muc_id']) && $_POST['danh_muc_id'] == $dm['id']) ? 'selected' : '' ?>><?= e($dm['ten']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label style="font-weight: bold;">Tên sản phẩm *</label>
                    <input type="text" name="ten" value="<?= e($_POST['ten'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid <?= isset($errors['ten']) ? 'red' : '#d1d5db' ?>; border-radius: 4px;">
                    <?php if (isset($errors['ten'])): ?><div style="color: red; font-size: 13px;"><?= e($errors['ten']) ?></div><?php endif; ?>
                </div>

                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label style="font-weight: bold;">Giá (VNĐ) *</label>
                        <input type="number" name="gia" value="<?= e($_POST['gia'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid <?= isset($errors['gia']) ? 'red' : '#d1d5db' ?>; border-radius: 4px;">
                        <?php if (isset($errors['gia'])): ?><div style="color: red; font-size: 13px;"><?= e($errors['gia']) ?></div><?php endif; ?>
                    </div>
                    <div style="flex: 1;">
                        <label style="font-weight: bold;">Tồn kho</label>
                        <input type="number" name="ton_kho" value="<?= e($_POST['ton_kho'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid <?= isset($errors['ton_kho']) ? 'red' : '#d1d5db' ?>; border-radius: 4px;">
                        <?php if (isset($errors['ton_kho'])): ?><div style="color: red; font-size: 13px;"><?= e($errors['ton_kho']) ?></div><?php endif; ?>
                    </div>
                </div>

                <div>
                    <label style="font-weight: bold;">Ảnh đại diện</label>
                    <input type="file" name="anh" accept="image/*" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>

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

                <div>
                    <label style="font-weight: bold;">Mô tả</label>
                    <textarea name="mo_ta" rows="4" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;"><?= e($_POST['mo_ta'] ?? '') ?></textarea>
                </div>

                <button type="submit" style="padding: 12px; background: #2563eb; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Lưu sản phẩm mới</button>
            </form>
        </div>

    <?php elseif ($tab === 'don-hang'): ?>
        <h3 style="margin-bottom: 15px; color: #1e3a8a;">Quản lý tất cả đơn hàng</h3>
        <?php
            $dsDonHang = $pdo->query("SELECT dh.*, nd.ho_ten, nd.email FROM don_hang dh JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id ORDER BY dh.id DESC")->fetchAll();
        ?>
        <div style="background: white; border-radius: 8px; border: 1px solid #e5e7eb; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
                <thead style="background: #f8fafc; text-align: left;">
                    <tr>
                        <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">ID Đơn</th>
                        <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">Khách hàng</th>
                        <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">Ngày đặt</th>
                        <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">Tổng tiền</th>
                        <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">Trạng thái</th>
                        <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dsDonHang as $dh): ?>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px;">#<?= $dh['id'] ?></td>
                            <td style="padding: 12px;"><?= e($dh['ho_ten'] ?? $dh['email']) ?></td>
                            <td style="padding: 12px;"><?= e($dh['ngay_tao'] ?? $dh['ngay_dat'] ?? 'N/A') ?></td>
                            <td style="padding: 12px; color: #dc2626; font-weight: bold;"><?= number_format((float)$dh['tong_tien'], 0, ',', '.') ?> đ</td>
                            <td style="padding: 12px;"><span style="background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px;"><?= e($dh['trang_thai']) ?></span></td>
                            <td style="padding: 12px; text-align: center;"><a href="don-hang.php?id=<?= $dh['id'] ?>" style="color: #2563eb; font-weight: bold; text-decoration: none;">Xem</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($tab === 'lien-he'): ?>
        <h3 style="margin-bottom: 15px; color: #1e3a8a;">Danh sách Liên hệ khách hàng</h3>
        <?php
            $dsLienHe = $pdo->query("SELECT * FROM lien_he ORDER BY id DESC")->fetchAll();
        ?>
        <?php if (empty($dsLienHe)): ?>
            <p>Chưa có liên hệ nào.</p>
        <?php else: ?>
            <div style="background: white; border-radius: 8px; border: 1px solid #e5e7eb; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                    <thead style="background: #f8fafc; text-align: left;">
                        <tr>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; width: 15%;">Thời gian</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; width: 25%;">Khách hàng</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; width: 35%;">Nội dung</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; width: 15%; text-align: center;">Ảnh đính kèm</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; width: 10%; text-align: center;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dsLienHe as $lh): ?>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 12px; vertical-align: top; color: #4b5563;"><?= e($lh['ngay_gui'] ?? 'N/A') ?></td>
                                <td style="padding: 12px; vertical-align: top;">
                                    <strong><?= e($lh['ho_ten']) ?></strong><br>
                                    <small>📧 <?= e($lh['email']) ?></small><br>
                                    <?php if (!empty($lh['sdt'])): ?>
                                        <small>📞 <?= e($lh['sdt']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px; vertical-align: top;">
                                    <?php if (!empty($lh['chu_de'])): ?>
                                        <span style="font-weight: bold; color: #2563eb;">📌 <?= e($lh['chu_de']) ?></span><br>
                                    <?php endif; ?>
                                    <?= nl2br(e($lh['noi_dung'])) ?>
                                </td>
                                <td style="padding: 12px; vertical-align: top; text-align: center;">
                                    <?php if (!empty($lh['anh'])): ?>
                                        <a href="uploads/<?= e($lh['anh']) ?>" target="_blank">
                                            <img src="uploads/<?= e($lh['anh']) ?>" alt="Ảnh đính kèm" style="max-width: 80px; max-height: 80px; border-radius: 4px; border: 1px solid #d1d5db; object-fit: cover;">
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #9ca3af; font-size: 0.9em;">Không có ảnh</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px; vertical-align: top; text-align: center;">
                                    <form method="POST" action="quan-tri.php?tab=lien-he" onsubmit="return confirm('Bạn có chắc muốn xóa liên hệ này?');" style="margin: 0;">
                                        <input type="hidden" name="action" value="xoa_lien_he">
                                        <input type="hidden" name="id" value="<?= (int)$lh['id'] ?>">
                                        <button type="submit" style="background: #dc2626; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold;">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>