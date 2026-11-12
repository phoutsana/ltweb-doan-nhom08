<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

if (!is_array($_SESSION['user'] ?? null) ||
    ($_SESSION['user']['vai_tro'] ?? null) !== 'quan_tri') {
    http_response_code(403);
    exit('Bạn không có quyền truy cập trang này.');
}

$pdo = db();
$sanPhams = $pdo->query(
    'SELECT sp.id, sp.ten, sp.gia, sp.ton_kho, sp.anh,
            dm.ten AS ten_danh_muc
     FROM san_pham sp
     LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id
     ORDER BY sp.id ASC'
)->fetchAll(PDO::FETCH_ASSOC);

$tieuDe = 'Quản lý sản phẩm - Fashion Store';
$trang = 'quan-tri-san-pham';
$cssRieng = 'css/quan-tri-san-pham-bt7.css';
require_once __DIR__ . '/inc/header.php';
?>
<main class="noi-dung-chinh bt7-products">
    <div class="bt7-products-top">
        <h2>Quản lý sản phẩm</h2>
        <a class="bt7-btn bt7-add" href="them-san-pham.php">+ Thêm sản phẩm mới</a>
    </div>

    <?php if (($_GET['tb'] ?? '') === 'thanh_cong'): ?>
        <p class="bt7-notice bt7-success" role="status">Thao tác thành công!</p>
    <?php endif; ?>

    <?php if (($_GET['loi'] ?? '') === 'dang_dung'): ?>
        <p class="bt7-notice bt7-error" role="alert">
            Không thể xóa: sản phẩm đã có khách đặt hàng.
        </p>
    <?php endif; ?>

    <div class="bt7-table-wrap">
        <table class="bt7-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá (VNĐ)</th>
                    <th>Tồn kho</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sanPhams as $sp): ?>
                    <tr>
                        <td><?= (int) $sp['id'] ?></td>
                        <td>
                            <?php if (!empty($sp['anh'])): ?>
                                <img
                                    class="bt7-product-img"
                                    src="<?= e((string) $sp['anh']) ?>"
                                    alt="Ảnh <?= e((string) $sp['ten']) ?>"
                                    width="50"
                                    height="50"
                                    loading="lazy"
                                >
                            <?php else: ?>
                                Chưa có
                            <?php endif; ?>
                        </td>
                        <td class="bt7-product-name">
                            <?= e((string) $sp['ten']) ?>
                        </td>
                        <td>
                            <?= e((string) ($sp['ten_danh_muc'] ?? 'Không có')) ?>
                        </td>
                        <td class="bt7-price">
                            <?= number_format((float) $sp['gia'], 0, ',', '.') ?> đ
                        </td>
                        <td class="bt7-stock">
                            <?= (int) $sp['ton_kho'] <= 0 ? 'Hết hàng' : (int) $sp['ton_kho'] ?>
                        </td>
                        <td>
                            <div class="bt7-actions">
                                <a
                                    class="bt7-btn bt7-edit"
                                    href="sua-san-pham.php?id=<?= (int) $sp['id'] ?>"
                                >Sửa</a>

                                <form method="post"
                                      action="xoa-san-pham.php"
                                      data-confirm-delete>
                                    <?= csrf_field() ?>
                                    <input type="hidden"
                                           name="id"
                                           value="<?= (int) $sp['id'] ?>">
                                    <button class="bt7-btn bt7-delete"
                                            type="submit">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (!$sanPhams): ?>
                    <tr>
                        <td colspan="7" class="bt7-empty">
                            Chưa có sản phẩm nào trong hệ thống.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script src="js/quan-tri-san-pham.js" defer></script>
<?php require_once __DIR__ . '/inc/footer.php'; ?>