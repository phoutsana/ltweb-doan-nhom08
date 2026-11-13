<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

$kho = new \App\Data\KhoSanPham();

// Kiểm tra ID sản phẩm.
$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

$sanPham = ($id !== false && $id !== null)
    ? $kho->timTheoId($id)
    : null;

if (!$sanPham) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

// Đọc dữ liệu sản phẩm, tương thích model BT6.
$spId = method_exists($sanPham, 'getId')
    ? $sanPham->getId()
    : $sanPham->id;

$spTen = method_exists($sanPham, 'getTen')
    ? $sanPham->getTen()
    : $sanPham->ten;

$spHinhAnh = method_exists($sanPham, 'getHinhAnh')
    ? $sanPham->getHinhAnh()
    : ($sanPham->anh ?? $sanPham->hinhAnh ?? '');

$spGia = method_exists($sanPham, 'getGia')
    ? $sanPham->getGia()
    : $sanPham->gia;

$spMoTa = method_exists($sanPham, 'getMoTa')
    ? $sanPham->getMoTa()
    : ($sanPham->mo_ta ?? $sanPham->moTa ?? '');

$spDanhMuc = method_exists($sanPham, 'getDanhMuc')
    ? $sanPham->getDanhMuc()
    : ($sanPham->danh_muc_id ?? $sanPham->danhMuc ?? '');

$spMauSac = method_exists($sanPham, 'getMauSac')
    ? $sanPham->getMauSac()
    : ($sanPham->mau_sac ?? $sanPham->mauSac ?? '');

$spKichThuoc = method_exists($sanPham, 'getKichThuoc')
    ? $sanPham->getKichThuoc()
    : ($sanPham->kich_thuoc ?? $sanPham->kichThuoc ?? '');

$spVideo = method_exists($sanPham, 'getVideo')
    ? $sanPham->getVideo()
    : ($sanPham->video ?? '');

// Chuẩn hóa các giá trị hiển thị.
$spId = (int)$spId;
$spTen = (string)$spTen;
$spHinhAnh = (string)$spHinhAnh;
$spMoTa = (string)$spMoTa;
$spDanhMuc = (string)$spDanhMuc;
$spMauSac = (string)$spMauSac;
$spKichThuoc = (string)$spKichThuoc;
$spVideo = (string)$spVideo;

// Cookie lưu tối đa 4 sản phẩm đã xem gần nhất.
$cookieDaXem = $_COOKIE['da_xem'] ?? '';

if (!is_string($cookieDaXem)) {
    $cookieDaXem = '';
}

$cu = array_map(
    'intval',
    explode(',', $cookieDaXem)
);

$moi = array_unique([
    $spId,
    ...array_filter($cu, static fn($x) => $x > 0)
]);

$https = (
    !empty($_SERVER['HTTPS']) &&
    $_SERVER['HTTPS'] !== 'off'
);

setcookie('da_xem', implode(',', array_slice($moi, 0, 4)), [
    'expires' => time() + 30 * 24 * 3600,
    'path' => '/',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);

$tieuDe = $spTen . ' - Fashion Store';
$trang = 'chi-tiet';
$cssRieng = 'css/chi-tiet-bt7-csp.css';

require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh">

    <article
        class="the-thong-tin khoi-noi-dung"
        data-bt7-ct="ct001"
    >

        <header>
            <h2 data-bt7-ct="ct002">
                <?= e($spTen) ?>
            </h2>
        </header>

        <!-- Hình ảnh sản phẩm -->
        <figure data-bt7-ct="ct003">
            <img
                src="<?= e($spHinhAnh) ?>"
                alt="<?= e($spTen) ?>"
                width="500"
                height="500"
                data-bt7-ct="ct004"
            >
        </figure>

        <!-- Mô tả sản phẩm -->
        <section
            class="khoi-noi-dung"
            data-bt7-ct="ct005"
        >
            <h2 data-bt7-ct="ct006">
                Mô tả sản phẩm
            </h2>

            <p data-bt7-ct="ct007">
                <?= e($spMoTa) ?>
            </p>
        </section>

        <!-- Thông số sản phẩm -->
        <section
            class="khoi-noi-dung"
            data-bt7-ct="ct008"
        >
            <h2 data-bt7-ct="ct009">
                Thông số sản phẩm
            </h2>

            <div
                class="hop-bang"
                data-bt7-ct="ct010"
            >
                <table data-bt7-ct="ct011">

                    <thead>
                        <tr data-bt7-ct="ct012">
                            <th
                                scope="col"
                                data-bt7-ct="ct013"
                            >
                                Thông tin
                            </th>

                            <th
                                scope="col"
                                data-bt7-ct="ct014"
                            >
                                Chi tiết
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td data-bt7-ct="ct015">
                                Danh mục
                            </td>

                            <td data-bt7-ct="ct016">
                                <?= e($spDanhMuc) ?>
                            </td>
                        </tr>

                        <tr>
                            <td data-bt7-ct="ct017">
                                Màu sắc
                            </td>

                            <td data-bt7-ct="ct018">
                                <?= e($spMauSac) ?>
                            </td>
                        </tr>

                        <tr>
                            <td data-bt7-ct="ct019">
                                Kích thước
                            </td>

                            <td data-bt7-ct="ct020">
                                <?= e($spKichThuoc) ?>
                            </td>
                        </tr>

                        <tr>
                            <td data-bt7-ct="ct021">
                                Giá
                            </td>

                            <td data-bt7-ct="ct022">
                                <?= vnd($spGia) ?>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </section>

        <!-- Video giới thiệu, nếu có -->
        <?php if ($spVideo !== ''): ?>

            <section
                class="khoi-noi-dung"
                data-bt7-ct="ct023"
            >
                <h2 data-bt7-ct="ct024">
                    Video giới thiệu
                </h2>

                <div
                    class="khung-video"
                    data-bt7-ct="ct025"
                >
                    <iframe
                        src="<?= e($spVideo) ?>"
                        title="Video giới thiệu sản phẩm"
                        data-bt7-ct="ct026"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                    ></iframe>
                </div>
            </section>

        <?php endif; ?>

        <!-- Form thêm giỏ hàng bằng AJAX -->
        <div data-bt7-ct="ct027">

            <form
                action="gio-hang.php"
                method="post"
                data-bt7-ct="ct028"
                class="form-them-gio-hang"
            >
                <?= csrf_field() ?>

                <!-- Giữ hai tên trường để tương thích JS cũ -->
                <input
                    type="hidden"
                    name="san_pham_id"
                    value="<?= $spId ?>"
                >

                <input
                    type="hidden"
                    name="id"
                    value="<?= $spId ?>"
                >

                <button
                    type="submit"
                    name="hanh_dong"
                    value="them"
                    class="nut-bam nut-bam--chinh"
                    data-bt7-ct="ct029"
                >
                    🛒 Thêm vào giỏ hàng
                </button>
            </form>

            <!-- Nút yêu thích -->
            <button
                type="button"
                id="nut-yeu-thich-ct"
                data-yeu-thich="<?= $spId ?>"
                class="nut-bam"
                data-bt7-ct="ct030"
            >
                ♡ Yêu thích
            </button>

        </div>

    </article>

</main>

<!-- JavaScript của trang chi tiết -->
<script src="js/gio-hang.js" defer></script>
<script src="js/tim-kiem.js" defer></script>
<script type="module" src="js/trang-chi-tiet.js"></script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
