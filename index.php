<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Store - Cửa hàng thời trang trực tuyến</title>
    <meta name="description"
        content="Fashion Store - Website bán quần áo trực tuyến với nhiều sản phẩm thời trang dành cho nam và nữ.">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body class="bo-cuc-trang">
    <header class="dau-trang">
        <img src="images/logo.png" alt="Fashion Store Logo" style="height: 60px; margin-bottom: 10px;">
        <h1>Fashion Store</h1>
        <p class="dau-trang__khau-hieu">Cửa hàng thời trang trực tuyến</p>
    </header>

    <nav class="dieu-huong" aria-label="Điều hướng chính">
        <!-- Nút Menu thu gọn cho Mobile -->
        <button type="button" class="nut-menu" aria-expanded="false" aria-controls="menu-chinh">☰ Menu</button>
        <ul class="dieu-huong__danh-sach menu" id="menu-chinh">
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="index.html">Trang chủ</a></li>
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="danh-sach.html">Sản phẩm</a></li>
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="chi-tiet.html">Chi tiết sản phẩm</a></li>
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="gioi-thieu.html">Giới thiệu</a></li>
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="lien-he.html">Liên hệ</a></li>
            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="danh-sach.html">Yêu thích (<span class="so-luong-yeu-thich"
                        aria-live="polite">0</span>)</a>
            </li>
        </ul>
    </nav>

    <main class="noi-dung-chinh">
        <section class="khoi-noi-dung">
            <h2>Chào mừng đến với Fashion Store</h2>
            <p>Fashion Store là website bán quần áo trực tuyến, cung cấp các sản phẩm thời trang phù hợp với nhiều phong
                cách.</p>
            <figure>
                <img src="images/banner.jpg" alt="Không gian thời trang của Fashion Store" width="800" height="450">
                <figcaption>Fashion Store - Thời trang cho phong cách của bạn</figcaption>
            </figure>
        </section>

        <!-- Khối hiển thị thời tiết từ API -->
        <section class="khoi-noi-dung">
            <h2>Thời tiết Đà Nẵng hiện tại</h2>
            <p id="trang-thai-thoi-tiet" aria-live="polite" style="font-weight: 500; color: #2563eb;">Đang tải dữ liệu
                thời tiết...</p>
            <p id="nhiet-do" style="font-size: 1.2rem; font-weight: bold;"></p>
        </section>

        <section class="khoi-noi-dung">
            <h2>Sản phẩm nổi bật</h2>
            <div class="luoi-the">
                <article class="the-thong-tin">
                    <img src="images/ao-thun-nam.jpg" alt="Áo thun nam"
                        style="width: 100%; height: 220px; object-fit: contain; background-color: #ffffff; border-radius: 8px; margin-bottom: 15px; border: 1px solid #e5e7eb; padding: 10px;">
                    <h3>Áo thun nam</h3>
                    <p>Áo thun nam với thiết kế đơn giản, phù hợp sử dụng hằng ngày.</p>
                    <a class="nut-bam nut-bam--chinh" href="chi-tiet.html?id=1">Xem chi tiết</a>
                </article>
                <article class="the-thong-tin">
                    <img src="images/ao-so-mi-nu.jpg" alt="Áo sơ mi nữ"
                        style="width: 100%; height: 220px; object-fit: contain; background-color: #ffffff; border-radius: 8px; margin-bottom: 15px; border: 1px solid #e5e7eb; padding: 10px;">
                    <h3>Áo sơ mi nữ</h3>
                    <p>Áo sơ mi nữ phù hợp với phong cách thanh lịch và hiện đại.</p>
                    <a class="nut-bam nut-bam--chinh" href="chi-tiet.html?id=2">Xem chi tiết</a>
                </article>
            </div>
        </section>

        <section class="khoi-noi-dung">
            <h2>Khám phá sản phẩm</h2>
            <p><a class="nut-bam nut-bam--phu" href="danh-sach.html">Xem toàn bộ danh sách sản phẩm</a></p>
        </section>
    </main>

    <footer class="chan-trang">
        <p>&copy; 2026 Fashion Store. All rights reserved.</p>
    </footer>

    <!-- Nhúng Script -->
    <script type="module" src="js/main.js"></script>
    <script type="module" src="js/trang-chu.js"></script>
</body>

</html>