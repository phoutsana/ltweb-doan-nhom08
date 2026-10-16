<?php
require_once __DIR__ . '/inc/config.php';


// Gọi lớp KhoSanPham để lấy dữ liệu
use App\Data\KhoSanPham;

$kho = new KhoSanPham();
$danhSach = $kho->tatCa();

// --- XỬ LÝ TÌM KIẾM, LỌC VÀ SẮP XẾP TỪ BIỂU MẪU GET ---
$tuKhoa = trim($_GET['q'] ?? '');
$danhMuc = trim($_GET['dm'] ?? '');
$sapXep = trim($_GET['sx'] ?? '');

// 1. Tìm theo từ khóa (tên sản phẩm)
if ($tuKhoa !== '') {
    $danhSach = array_filter($danhSach, function ($sp) use ($tuKhoa) {
        // mb_stripos giúp tìm kiếm không phân biệt hoa thường có dấu tiếng Việt
        return mb_stripos($sp->getTen(), $tuKhoa) !== false;
    });
}

// 2. Lọc theo danh mục
if ($danhMuc !== '') {
    $danhSach = array_filter($danhSach, function ($sp) use ($danhMuc) {
        return $sp->getDanhMuc() === $danhMuc;
    });
}

// 3. Sắp xếp giá hoặc tên
if ($sapXep === 'gia-tang') {
    usort($danhSach, fn($a, $b) => $a->getGia() <=> $b->getGia());
} elseif ($sapXep === 'gia-giam') {
    usort($danhSach, fn($a, $b) => $b->getGia() <=> $a->getGia());
} elseif ($sapXep === 'ten-az') {
    // Sắp xếp theo tên A-Z
    usort($danhSach, fn($a, $b) => strcasecmp($a->getTen(), $b->getTen()));
}

$tieuDe = 'Danh sách sản phẩm - Fashion Store';
$trang = 'san-pham';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" style="max-width: 1200px; margin: 0 auto; padding: 20px;">

    <!-- Khối bộ lọc -->
    <form method="GET" action="danh-sach.php" class="khoi-noi-dung bo-loc-san-pham" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 30px; display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">

        <div style="display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 200px;">
            <label for="tim-kiem" style="font-weight: bold; color: #374151;">Tìm kiếm</label>
            <input type="text" id="tim-kiem" name="q" value="<?= e($tuKhoa) ?>" placeholder="Tên sản phẩm..." style="padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
        </div>

        <div style="display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 150px;">
            <label for="loc-danh-muc" style="font-weight: bold; color: #374151;">Danh mục</label>
            <select id="loc-danh-muc" name="dm" style="padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; background: white;">
                <option value="">Tất cả</option>
                <option value="Áo" <?= $danhMuc === 'Áo' ? 'selected' : '' ?>>Áo</option>
                <option value="Quần" <?= $danhMuc === 'Quần' ? 'selected' : '' ?>>Quần</option>
                <option value="Váy" <?= $danhMuc === 'Váy' ? 'selected' : '' ?>>Váy</option>
                <option value="Áo khoác" <?= $danhMuc === 'Áo khoác' ? 'selected' : '' ?>>Áo khoác</option>
            </select>
        </div>

        <div style="display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 150px;">
            <label for="sap-xep" style="font-weight: bold; color: #374151;">Sắp xếp</label>
            <select id="sap-xep" name="sx" style="padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; background: white;">
                <option value="">Mặc định</option>
                <option value="gia-tang" <?= $sapXep === 'gia-tang' ? 'selected' : '' ?>>Giá tăng dần</option>
                <option value="gia-giam" <?= $sapXep === 'gia-giam' ? 'selected' : '' ?>>Giá giảm dần</option>
                <option value="ten-az" <?= $sapXep === 'ten-az' ? 'selected' : '' ?>>Tên A-Z</option>
            </select>
        </div>

        <button type="submit" style="padding: 10px 25px; background-color: #2563eb; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; height: 42px; transition: background 0.3s;">Lọc</button>
    </form>

    <!-- Khu vực hiển thị sản phẩm -->
    <section>
        <?php if (empty($danhSach)): ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <p style="font-weight: bold; color: #dc2626; font-size: 1.2rem;">Không có sản phẩm nào phù hợp với tìm kiếm của bạn.</p>
                <a href="danh-sach.php" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px;">Xem tất cả sản phẩm</a>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px;">
                <?php foreach ($danhSach as $sp): ?>
                    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; flex-direction: column;"
                        onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)';">

                        <!-- Ảnh sản phẩm -->
                        <div style="width: 100%; height: 250px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <img src="<?= e($sp->getHinhAnh()) ?>" alt="<?= e($sp->getTen()) ?>" loading="lazy" style="max-width: 100%; max-height: 100%; object-fit: contain; padding: 10px;">
                        </div>

                        <!-- Thông tin sản phẩm -->
                        <div style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <h2 style="font-size: 1.1rem; color: #111827; margin: 0 0 10px 0; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= e($sp->getTen()) ?>
                                </h2>
                                <p style="color: #dc2626; font-size: 1.25rem; font-weight: 700; margin: 0 0 15px 0;">
                                    <?= vnd($sp->getGia()) ?>
                                </p>
                            </div>

                            <a href="chi-tiet.php?id=<?= e($sp->getId()) ?>" style="display: block; text-align: center; width: 100%; padding: 10px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#1d4ed8';" onmouseout="this.style.backgroundColor='#2563eb';">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php
require_once __DIR__ . '/inc/footer.php';
?>