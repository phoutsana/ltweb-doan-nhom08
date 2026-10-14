<?php
// Bắt buộc gọi config.php đầu tiên ở mọi trang
require_once __DIR__ . '/inc/config.php';

use App\Data\KhoSanPham;

$kho = new KhoSanPham();

// =====================================================================
// ĐỌC COOKIE: LẤY DANH SÁCH 4 SẢN PHẨM ĐÃ XEM GẦN ĐÂY (Chuẩn Gợi ý 4)
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

// Cài đặt tiêu đề cho trang web
$tieuDe = 'Trang chủ - Fashion Store';
$trang = 'index'; // Đánh dấu trang hiện tại cho menu

// Gọi header dùng chung
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
    <!-- Khối hiển thị thời tiết từ API -->
    <section class="khoi-noi-dung">
        <h2>Thời tiết Đà Nẵng hiện tại</h2>
        <p id="trang-thai-thoi-tiet" aria-live="polite" style="font-weight: 500; color: #2563eb;">Đang tải dữ liệu thời tiết...</p>
        <p id="nhiet-do" style="font-size: 1.5rem; font-weight: bold; margin-top: 10px;"></p>

        <script>
            // Mã JavaScript gọi REST API 
            document.addEventListener('DOMContentLoaded', function() {
                const trangThai = document.getElementById('trang-thai-thoi-tiet');
                const nhietDo = document.getElementById('nhiet-do');

                fetch('https://api.open-meteo.com/v1/forecast?latitude=16.0678&longitude=108.2208&current_weather=true')
                    .then(response => response.json())
                    .then(data => {
                        trangThai.textContent = 'Dữ liệu thời tiết đã được tải thành công!';
                        trangThai.style.color = '#10b981'; // Xanh lá cây nhạt như hình mẫu
                        nhietDo.textContent = data.current_weather.temperature + ' °C';
                        nhietDo.style.color = '#dc2626'; // Đỏ nổi bật cho nhiệt độ
                    })
                    .catch(error => {
                        trangThai.textContent = 'Không tải được dữ liệu thời tiết.';
                        trangThai.style.color = '#dc2626';
                    });
            });
        </script>
    </section>

    <section class="khoi-noi-dung">
        <h2>Sản phẩm nổi bật</h2>
        <div class="luoi-the">
            <article class="the-thong-tin">
                <img src="images/ao-thun-nam.jpg" alt="Áo thun nam" style="width: 100%; height: 220px; object-fit: contain; background-color: #ffffff; border-radius: 8px; margin-bottom: 15px; border: 1px solid #e5e7eb; padding: 10px;">
                <h3>Áo thun nam</h3>
                <p>Áo thun nam với thiết kế đơn giản, phù hợp sử dụng hằng ngày.</p>
                <a class="nut-bam nut-bam--chinh" href="chi-tiet.php?id=1">Xem chi tiết</a>
            </article>
            <article class="the-thong-tin">
                <img src="images/ao-so-mi-nu.jpg" alt="Áo sơ mi nữ" style="width: 100%; height: 220px; object-fit: contain; background-color: #ffffff; border-radius: 8px; margin-bottom: 15px; border: 1px solid #e5e7eb; padding: 10px;">
                <h3>Áo sơ mi nữ</h3>
                <p>Áo sơ mi nữ phù hợp với phong cách thanh lịch và hiện đại.</p>
                <a class="nut-bam nut-bam--chinh" href="chi-tiet.php?id=2">Xem chi tiết</a>
            </article>
        </div>
    </section>

    <!-- KHỐI ĐÃ XEM GẦN ĐÂY (Sẽ tự động hiện ra khi có Cookie) -->
    <?php if (!empty($daXem)): ?>
        <section class="khoi-noi-dung" style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 12px; margin-bottom: 30px;">
            <h2 style="color: #1e3a8a; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px;">
                Đã xem gần đây
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
                <?php foreach ($daXem as $sp): ?>
                    <article style="background: white; border-radius: 8px; padding: 15px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <img src="<?= e($sp->hinhAnh) ?>" alt="<?= e($sp->ten) ?>" style="width: 100%; height: 180px; object-fit: contain; margin-bottom: 10px;">
                            <h3 style="font-size: 1.05rem; margin-bottom: 10px; line-height: 1.4; max-height: 2.8em; overflow: hidden;">
                                <?= e($sp->ten) ?>
                            </h3>
                            <p style="color: #dc2626; font-weight: bold; margin-bottom: 15px; font-size: 1.1rem;">
                                <?= vnd($sp->gia) ?>
                            </p>
                        </div>
                        <a class="nut-bam nut-bam--chinh" href="chi-tiet.php?id=<?= e($sp->id) ?>" style="width: 100%; box-sizing: border-box;">Xem lại</a>
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

<?php
// Gọi footer dùng chung
require_once __DIR__ . '/inc/footer.php';
?>