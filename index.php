<?php
// Bắt buộc gọi config.php đầu tiên ở mọi trang
require_once __DIR__ . '/inc/config.php';

use App\Data\KhoSanPham;

$kho = new KhoSanPham();

// =====================================================================
// ĐỌC COOKIE: LẤY DANH SÁCH 4 SẢN PHẨM ĐÃ XEM GẦN ĐÂY
// =====================================================================
$daXem = [];
foreach (explode(',', $_COOKIE['da_xem'] ?? '') as $x) {
    $idCookie = filter_var($x, FILTER_VALIDATE_INT);
    $spCookie = $idCookie ? $kho->timTheoId($idCookie) : null;
    if ($spCookie !== null) {
        $daXem[] = $spCookie; // Chỉ thêm vào danh sách nếu sản phẩm thực sự tồn tại
    }
}
// =====================================================================

$tieuDe = 'Trang chủ - Fashion Store';
$trang = 'index';

$cssRieng = 'css/index-bt7-csp.css';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh">
    <section class="khoi-noi-dung">
        <h2>Chào mừng đến với Fashion Store</h2>
        <p>Fashion Store là website bán quần áo trực tuyến, cung cấp các sản phẩm thời trang phù hợp với nhiều phong cách.</p>
        <figure>
            <img src="images/banner.jpg" alt="Không gian thời trang của Fashion Store" width="800" height="450">
            <figcaption>Fashion Store - Thời trang cho phong cách của bạn</figcaption>
        </figure>
    </section>

    <!-- Khối hiển thị thời tiết từ API -->
    <section class="khoi-noi-dung">
        <h2>Thời tiết Đà Nẵng hiện tại</h2>
        <p id="trang-thai-thoi-tiet" aria-live="polite" data-bt7-css="s001">Đang tải dữ liệu thời tiết...</p>
        <p id="nhiet-do" data-bt7-css="s002"></p>

        <script src="js/index-bt7-csp.js" defer></script>
    </section>

    <section class="khoi-noi-dung">
        <h2>Sản phẩm nổi bật</h2>
        <div class="luoi-the">
            <article class="the-thong-tin">
                <img src="images/ao-thun-nam.jpg" alt="Áo thun nam" data-bt7-css="s003">
                <h3>Áo thun nam</h3>
                <p>Áo thun nam với thiết kế đơn giản, phù hợp sử dụng hằng ngày.</p>
                <a class="nut-bam nut-bam--chinh" href="chi-tiet.php?id=1">Xem chi tiết</a>
            </article>
            <article class="the-thong-tin">
                <img src="images/ao-so-mi-nu.jpg" alt="Áo sơ mi nữ" data-bt7-css="s004">
                <h3>Áo sơ mi nữ</h3>
                <p>Áo sơ mi nữ phù hợp với phong cách thanh lịch và hiện đại.</p>
                <a class="nut-bam nut-bam--chinh" href="chi-tiet.php?id=2">Xem chi tiết</a>
            </article>
        </div>
    </section>

    <!-- KHỐI ĐÃ XEM GẦN ĐÂY -->
    <?php if (!empty($daXem)): ?>
        <section class="khoi-noi-dung" data-bt7-css="s005">
            <h2 data-bt7-css="s006">
                Đã xem gần đây
            </h2>
            <div data-bt7-css="s007">
                <?php foreach ($daXem as $sp):
                    // Kiểm tra xem biến gọi bằng Getter hay Thuộc tính
                    $spId = method_exists($sp, 'getId') ? $sp->getId() : $sp->id;
                    $spTen = method_exists($sp, 'getTen') ? $sp->getTen() : $sp->ten;
                    $spHinhAnh = method_exists($sp, 'getHinhAnh') ? $sp->getHinhAnh() : $sp->anh ?? $sp->hinhAnh;
                    $spGia = method_exists($sp, 'getGia') ? $sp->getGia() : $sp->gia;
                ?>
                    <article data-bt7-css="s008">
                        <div>
                            <img src="<?= e($spHinhAnh) ?>" alt="<?= e($spTen) ?>" data-bt7-css="s009">
                            <h3 data-bt7-css="s010">
                                <?= e($spTen) ?>
                            </h3>
                            <p data-bt7-css="s011">
                                <?= vnd($spGia) ?>
                            </p>
                        </div>
                        <a class="nut-bam nut-bam--chinh" href="chi-tiet.php?id=<?= e($spId) ?>" data-bt7-css="s012">Xem lại</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="khoi-noi-dung">
        <h2>Khám phá sản phẩm</h2>
        <p><a class="nut-bam nut-bam--phu" href="danh-sach.php">Xem toàn bộ danh sách sản phẩm</a></p>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>