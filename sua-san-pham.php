<?php

declare(strict_types=1);
require_once __DIR__ . '/inc/config.php';

// === KIỂM TRA PHÂN QUYỀN BẢO MẬT (Chặn khách hàng, chỉ cho quản trị viên truy cập) ===
if (!isset($_SESSION['user']) || $_SESSION['user']['vai_tro'] !== 'quan_tri') {
    http_response_code(403);
    echo "Bạn không có quyền truy cập trang này!";
    exit;
}

$pdo = db();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM san_pham WHERE id = ?");
$stmt->execute([$id]);
$sanPham = $stmt->fetch();

if (!$sanPham) {
    die("Sản phẩm không tồn tại!");
}

$stmtDm = $pdo->query("SELECT id, ten FROM danh_muc ORDER BY id ASC");
$danhMucList = $stmtDm->fetchAll();

$loi = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ten'] ?? '');
    $gia = (int)($_POST['gia'] ?? 0);
    $ton_kho = (int)($_POST['ton_kho'] ?? 0);
    $danh_muc_id = (int)($_POST['danh_muc_id'] ?? 0);

    if (empty($ten)) $loi['ten'] = 'Tên không được trống.';
    if ($gia <= 0) $loi['gia'] = 'Giá phải > 0.';
    if ($ton_kho < 0) $loi['ton_kho'] = 'Tồn kho không được âm.';

    if (empty($loi)) {
        $sql = "UPDATE san_pham SET danh_muc_id = ?, ten = ?, gia = ?, ton_kho = ? WHERE id = ?";
        $stmtUpdate = $pdo->prepare($sql);
        $stmtUpdate->execute([$danh_muc_id, $ten, $gia, $ton_kho, $id]);
        header("Location: quan-tri-san-pham.php?tb=thanh_cong");
        exit;
    }
}

$tieuDe = 'Sửa sản phẩm';
require_once __DIR__ . '/inc/header.php';
?>
<main style="max-width: 800px; margin: 40px auto; padding: 20px; background: white; border-radius: 8px;">
    <h2>Sửa sản phẩm #<?= $id ?></h2>
    <form method="POST" action="" style="display: flex; flex-direction: column; gap: 15px;">
        <div>
            <label>Tên sản phẩm *</label>
            <input type="text" name="ten" value="<?= htmlspecialchars($_POST['ten'] ?? $sanPham['ten']) ?>" style="width: 100%; padding: 8px;">
            <?php if (isset($loi['ten'])) echo "<span style='color:red;'>{$loi['ten']}</span>"; ?>
        </div>
        <div>
            <label>Danh mục *</label>
            <select name="danh_muc_id" style="width: 100%; padding: 8px;">
                <?php foreach ($danhMucList as $dm): ?>
                    <option value="<?= $dm['id'] ?>" <?= (($sanPham['danh_muc_id'] ?? 0) == $dm['id']) ? 'selected' : '' ?>><?= htmlspecialchars($dm['ten']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display: flex; gap: 15px;">
            <div style="flex: 1;"><label>Giá *</label><input type="number" name="gia" value="<?= (int)($_POST['gia'] ?? $sanPham['gia']) ?>" style="width: 100%; padding: 8px;"></div>
            <div style="flex: 1;"><label>Tồn kho *</label><input type="number" name="ton_kho" value="<?= (int)($_POST['ton_kho'] ?? $sanPham['ton_kho']) ?>" style="width: 100%; padding: 8px;"></div>
        </div>
        <button type="submit" style="padding: 10px; background: #2563eb; color: white; border: none; cursor: pointer;">Cập nhật</button>
    </form>
</main>
<?php require_once __DIR__ . '/inc/footer.php'; ?>