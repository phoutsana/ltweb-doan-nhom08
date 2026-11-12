<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

use App\Data\KhoSanPham;

$kho = new KhoSanPham();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Dùng getter getId() nếu class KhoSanPham trả về Object
$sanPham = $id ? $kho->timTheoId($id) : null;

// Xử lý nếu ID sai hoặc sản phẩm không tồn tại
if (!$sanPham) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

// Lấy các thuộc tính (đảm bảo đồng bộ hàm get giống bên danh-sach.php)
$spId = method_exists($sanPham, 'getId') ? $sanPham->getId() : $sanPham->id;
$spTen = method_exists($sanPham, 'getTen') ? $sanPham->getTen() : $sanPham->ten;
$spHinhAnh = method_exists($sanPham, 'getHinhAnh') ? $sanPham->getHinhAnh() : $sanPham->anh ?? $sanPham->hinhAnh;
$spGia = method_exists($sanPham, 'getGia') ? $sanPham->getGia() : $sanPham->gia;
$spMoTa = method_exists($sanPham, 'getMoTa') ? $sanPham->getMoTa() : $sanPham->mo_ta ?? $sanPham->moTa;
$spDanhMuc = method_exists($sanPham, 'getDanhMuc') ? $sanPham->getDanhMuc() : $sanPham->danh_muc_id ?? $sanPham->danhMuc;
$spMauSac = method_exists($sanPham, 'getMauSac') ? $sanPham->getMauSac() : $sanPham->mau_sac ?? $sanPham->mauSac;
$spKichThuoc = method_exists($sanPham, 'getKichThuoc') ? $sanPham->getKichThuoc() : $sanPham->kich_thuoc ?? $sanPham->kichThuoc;
$spVideo = method_exists($sanPham, 'getVideo') ? $sanPham->getVideo() : $sanPham->video;

// =====================================================================
// XỬ LÝ COOKIE: LƯU 4 SẢN PHẨM ĐÃ XEM GẦN NHẤT
// =====================================================================
$cu = array_map('intval', explode(',', $_COOKIE['da_xem'] ?? ''));
$moi = array_unique([$spId, ...array_filter($cu)]);

// Đã fix lỗi cú pháp tính toán thời gian ở đây (có dấu *)
setcookie('da_xem', implode(',', array_slice($moi, 0, 4)), [
    'expires'  => time() + 30 * 24 * 3600, // 30 ngày
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
// =====================================================================

$tieuDe = $spTen . ' - Fashion Store';
$trang = 'chi-tiet';
$cssRieng = 'css/chi-tiet-bt7-csp.css';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh">
    <article class="the-thong-tin khoi-noi-dung" data-bt7-ct="ct001">
        <header>
            <h2 data-bt7-ct="ct002"><?= e($spTen) ?></h2>
        </header>

        <figure data-bt7-ct="ct003">
            <img src="<?= e($spHinhAnh) ?>" alt="<?= e($spTen) ?>" width="500" height="500" data-bt7-ct="ct004">
        </figure>

        <section class="khoi-noi-dung" data-bt7-ct="ct005">
            <h2 data-bt7-ct="ct006">Mô tả sản phẩm</h2>
            <p data-bt7-ct="ct007"><?= e($spMoTa) ?></p>
        </section>

        <section class="khoi-noi-dung" data-bt7-ct="ct008">
            <h2 data-bt7-ct="ct009">Thông số sản phẩm</h2>
            <div class="hop-bang" data-bt7-ct="ct010">
                <table data-bt7-ct="ct011">
                    <thead>
                        <tr data-bt7-ct="ct012">
                            <th scope="col" data-bt7-ct="ct013">Thông tin</th>
                            <th scope="col" data-bt7-ct="ct014">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-bt7-ct="ct015">Danh mục</td>
                            <td data-bt7-ct="ct016"><?= e($spDanhMuc) ?></td>
                        </tr>
                        <tr>
                            <td data-bt7-ct="ct017">Màu sắc</td>
                            <td data-bt7-ct="ct018"><?= e($spMauSac) ?></td>
                        </tr>
                        <tr>
                            <td data-bt7-ct="ct019">Kích thước</td>
                            <td data-bt7-ct="ct020"><?= e($spKichThuoc) ?></td>
                        </tr>
                        <tr>
                            <td data-bt7-ct="ct021">Giá</td>
                            <td data-bt7-ct="ct022"><?= vnd($spGia) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <?php if (!empty($spVideo)): ?>
            <section class="khoi-noi-dung" data-bt7-ct="ct023">
                <h2 data-bt7-ct="ct024">Video giới thiệu</h2>
                <div class="khung-video" data-bt7-ct="ct025">
                    <iframe data-bt7-ct="ct026" src="<?= e($spVideo) ?>"
                        title="Video giới thiệu sản phẩm"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen>
                    </iframe>
                </div>
            </section>
        <?php endif; ?>

        <!-- Đã thêm class 'form-them-gio-hang' để tệp js/gio-hang.js bắt sự kiện thành công -->
        <div data-bt7-ct="ct027">
            <form action="gio-hang.php" method="POST" data-bt7-ct="ct028" class="form-them-gio-hang">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= e($spId) ?>">
                <button type="submit" name="hanh_dong" value="them" class="nut-bam nut-bam--chinh" data-bt7-ct="ct029">
                    🛒 Thêm vào giỏ hàng
                </button>
            </form>

            <button type="button" id="nut-yeu-thich-ct" data-yeu-thich="<?= e($spId) ?>" class="nut-bam" data-bt7-ct="ct030">
                ♡ Yêu thích
            </button>
        </div>

    </article>
</main>

<!-- Nạp đầy đủ các tệp JavaScript xử lý giỏ hàng, tìm kiếm và chi tiết -->
<script src="js/gio-hang.js"></script>
<script src="js/tim-kiem.js"></script>
<script type="module" src="js/trang-chi-tiet.js"></script>

<?php
require_once __DIR__ . '/inc/footer.php';
?>