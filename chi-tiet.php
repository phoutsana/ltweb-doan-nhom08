<?php
require_once __DIR__ . '/inc/config.php';

use App\Data\KhoSanPham;

$kho = new KhoSanPham();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$sanPham = $id ? $kho->timTheoId($id) : null;

// Xử lý nếu ID sai hoặc sản phẩm không tồn tại (Chuẩn Gợi ý 4)
if (!$sanPham) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

// =====================================================================
// XỬ LÝ COOKIE: LƯU 4 SẢN PHẨM ĐÃ XEM GẦN NHẤT (Chuẩn Gợi ý 4)
// =====================================================================
$cu = array_map('intval', explode(',', $_COOKIE['da_xem'] ?? ''));
$moi = array_unique([$sanPham->id, ...array_filter($cu)]);

setcookie('da_xem', implode(',', array_slice($moi, 0, 4)), [
    'expires'  => time() + 30 * 24 * 3600, // 30 ngày
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
// =====================================================================

$tieuDe = $sanPham->ten . ' - Fashion Store';
$trang = 'chi-tiet';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh">
    <article class="the-thong-tin khoi-noi-dung">
        <header>
            <h2><?= e($sanPham->ten) ?></h2>
        </header>

        <figure>
            <img src="<?= e($sanPham->hinhAnh) ?>" alt="<?= e($sanPham->ten) ?>" width="500" height="500" style="max-width: 100%; height: auto; border-radius: 8px;">
            <figcaption><?= e($sanPham->ten) ?></figcaption>
        </figure>

        <section class="khoi-noi-dung">
            <h2>Mô tả sản phẩm</h2>
            <p><?= e($sanPham->moTa) ?></p>
        </section>

        <section class="khoi-noi-dung">
            <h2>Thông số sản phẩm</h2>
            <div class="hop-bang">
                <table>
                    <caption>Thông số <?= e($sanPham->ten) ?></caption>
                    <thead>
                        <tr>
                            <th scope="col">Thông tin</th>
                            <th scope="col">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Danh mục</td>
                            <td><?= e($sanPham->danhMuc) ?></td>
                        </tr>
                        <tr>
                            <td>Màu sắc</td>
                            <td><?= e($sanPham->mauSac) ?></td>
                        </tr>
                        <tr>
                            <td>Kích thước</td>
                            <td><?= e($sanPham->kichThuoc) ?></td>
                        </tr>
                        <tr>
                            <td>Giá</td>
                            <td style="color: #dc2626; font-weight: bold;"><?= vnd($sanPham->gia) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <?php if (!empty($sanPham->video)): ?>
            <section class="khoi-noi-dung">
                <h2>Video giới thiệu</h2>
                <div class="khung-video">
                    <iframe width="560" height="315" src="<?= e($sanPham->video) ?>"
                        title="Video giới thiệu sản phẩm"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>
                </div>
            </section>
        <?php endif; ?>

        <form action="gio-hang.php" method="POST" style="margin-top: 15px;">
            <input type="hidden" name="id" value="<?= e($sanPham->id) ?>">
            <button type="submit" name="hanh_dong" value="them" class="nut-bam nut-bam--chinh">
                ♡ thêm vào giỏ hàng / yêu thích
            </button>
        </form>

        <hr class="phan-cach">
    </article>
</main>

<?php
require_once __DIR__ . '/inc/footer.php';
?>