<?php
require_once __DIR__ . '/inc/config.php';

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
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh">
    <article class="the-thong-tin khoi-noi-dung" style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <header>
            <h2 style="font-size: 2rem; color: #111827; margin-bottom: 20px;"><?= e($spTen) ?></h2>
        </header>

        <figure style="text-align: center; margin-bottom: 30px;">
            <img src="<?= e($spHinhAnh) ?>" alt="<?= e($spTen) ?>" width="500" height="500" style="max-width: 100%; height: auto; border-radius: 8px; object-fit: contain; background: #f9fafb; padding: 20px;">
        </figure>

        <section class="khoi-noi-dung" style="margin-bottom: 30px;">
            <h2 style="border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">Mô tả sản phẩm</h2>
            <p style="line-height: 1.6; color: #4b5563; margin-top: 15px;"><?= e($spMoTa) ?></p>
        </section>

        <section class="khoi-noi-dung" style="margin-bottom: 30px;">
            <h2 style="border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">Thông số sản phẩm</h2>
            <div class="hop-bang" style="margin-top: 15px;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: #f3f4f6;">
                            <th scope="col" style="padding: 12px; border: 1px solid #e5e7eb;">Thông tin</th>
                            <th scope="col" style="padding: 12px; border: 1px solid #e5e7eb;">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 12px; border: 1px solid #e5e7eb; font-weight: bold;">Danh mục</td>
                            <td style="padding: 12px; border: 1px solid #e5e7eb;"><?= e($spDanhMuc) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border: 1px solid #e5e7eb; font-weight: bold;">Màu sắc</td>
                            <td style="padding: 12px; border: 1px solid #e5e7eb;"><?= e($spMauSac) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border: 1px solid #e5e7eb; font-weight: bold;">Kích thước</td>
                            <td style="padding: 12px; border: 1px solid #e5e7eb;"><?= e($spKichThuoc) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border: 1px solid #e5e7eb; font-weight: bold;">Giá</td>
                            <td style="padding: 12px; border: 1px solid #e5e7eb; color: #dc2626; font-weight: bold; font-size: 1.2rem;"><?= vnd($spGia) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <?php if (!empty($spVideo)): ?>
            <section class="khoi-noi-dung" style="margin-bottom: 30px;">
                <h2 style="border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 15px;">Video giới thiệu</h2>
                <div class="khung-video" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px;">
                    <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" src="<?= e($spVideo) ?>"
                        title="Video giới thiệu sản phẩm"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen>
                    </iframe>
                </div>
            </section>
        <?php endif; ?>

        <!-- Đã tách riêng Giỏ hàng và Yêu thích để JS có thể hoạt động -->
        <div style="display: flex; gap: 15px; margin-top: 20px;">
            <form action="gio-hang.php" method="POST" style="flex: 1;">
                <input type="hidden" name="id" value="<?= e($spId) ?>">
                <button type="submit" name="hanh_dong" value="them" class="nut-bam nut-bam--chinh" style="width: 100%; padding: 15px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: bold; font-size: 1.1rem; cursor: pointer; transition: background 0.3s;">
                    🛒 Thêm vào giỏ hàng
                </button>
            </form>

            <button type="button" id="nut-yeu-thich-ct" data-yeu-thich="<?= e($spId) ?>" class="nut-bam" style="padding: 15px 30px; background: white; border: 2px solid #dc2626; color: #dc2626; border-radius: 6px; font-weight: bold; font-size: 1.1rem; cursor: pointer; transition: background 0.3s;">
                ♡ Yêu thích
            </button>
        </div>

    </article>
</main>

<!-- Liên kết với file JS chuyên bắt sự kiện Yêu thích -->
<script type="module" src="js/trang-chi-tiet.js"></script>

<?php
require_once __DIR__ . '/inc/footer.php';
?>