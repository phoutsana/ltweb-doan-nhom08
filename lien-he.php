<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - Fashion Store</title>
    <meta name="description" content="Liên hệ với Fashion Store để gửi câu hỏi hoặc góp ý về sản phẩm.">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bo-cuc-trang">
    <header class="dau-trang">
        <h1>Liên hệ với Fashion Store</h1>
        <p class="dau-trang__khau-hieu">Gửi thông tin hoặc câu hỏi cho chúng tôi</p>
    </header>

    <nav class="dieu-huong" aria-label="Điều hướng chính">
        <button type="button" class="nut-menu" aria-expanded="false" aria-controls="menu-chinh">☰ Menu</button>
        <ul class="dieu-huong__danh-sach menu" id="menu-chinh">
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="index.html">Trang chủ</a></li>
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="danh-sach.html">Sản phẩm</a></li>
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="chi-tiet.html">Chi tiết sản phẩm</a></li>
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="gioi-thieu.html">Giới thiệu</a></li>
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="lien-he.html">Liên hệ</a></li>
            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="danh-sach.html">Yêu thích (<span class="so-luong-yeu-thich" aria-live="polite">0</span>)</a>
            </li>
        </ul>
    </nav>

    <main class="noi-dung-chinh">
        <section class="khoi-noi-dung">
            <h2>Form liên hệ</h2>
            <form id="form-lien-he" class="bieu-mau" action="https://jsonplaceholder.typicode.com/posts" method="POST" novalidate>
                <fieldset>
                    <legend>Thông tin người dùng</legend>

                    <p class="bieu-mau__nhom">
                        <label class="bieu-mau__nhan" for="fullname">Họ và tên:</label>
                        <input class="bieu-mau__nhap" type="text" id="fullname" name="fullname" minlength="2" required>
                        <span id="loi-fullname" aria-live="polite" style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"></span>
                    </p>

                    <p class="bieu-mau__nhom">
                        <label class="bieu-mau__nhan" for="email">Email:</label>
                        <input class="bieu-mau__nhap" type="email" id="email" name="email" required>
                        <span id="loi-email" aria-live="polite" style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"></span>
                    </p>

                    <p class="bieu-mau__nhom">
                        <label class="bieu-mau__nhan" for="phone">Số điện thoại:</label>
                        <input class="bieu-mau__nhap" type="tel" id="phone" name="phone" pattern="[0-9]{10}" placeholder="0123456789" required>
                    </p>

                    <p class="bieu-mau__nhom">
                        <label class="bieu-mau__nhan" for="age">Tuổi:</label>
                        <input class="bieu-mau__nhap" type="number" id="age" name="age" min="16" max="100" required>
                    </p>

                    <p class="bieu-mau__nhom">
                        <label class="bieu-mau__nhan" for="date">Ngày liên hệ:</label>
                        <input class="bieu-mau__nhap" type="date" id="date" name="date" required>
                    </p>

                    <p class="bieu-mau__nhom">
                        <label class="bieu-mau__nhan" for="topic">Chủ đề:</label>
                        <select class="bieu-mau__lua-chon" id="topic" name="topic" required>
                            <option value="">-- Chọn chủ đề --</option>
                            <option value="product">Hỏi về sản phẩm</option>
                            <option value="order">Hỏi về đơn hàng</option>
                            <option value="feedback">Góp ý</option>
                        </select>
                    </p>

                    <p class="bieu-mau__nhom">
                        <label class="bieu-mau__nhan" for="message">Nội dung:</label>
                    </p>
                    <p class="bieu-mau__nhom">
                        <textarea class="bieu-mau__van-ban" id="message" name="message" rows="5" cols="50" minlength="10" required></textarea>
                        <span id="loi-message" aria-live="polite" style="color: #dc2626; font-size: 0.9em; display: block; margin-top: 5px;"></span>
                    </p>

                    <p class="bieu-mau__nhom-nut">
                        <button id="nut-gui-lien-he" class="nut-bam nut-bam--chinh" type="submit">Gửi thông tin</button>
                        <button class="nut-bam nut-bam--phu" type="reset">Nhập lại</button>
                    </p>

                    <!-- Kết quả gửi -->
                    <p id="ket-qua-form" aria-live="polite" style="margin-top: 15px; font-weight: bold; text-align: center;"></p>
                </fieldset>
            </form>
        </section>
    </main>

    <footer class="chan-trang">
        <p>&copy; 2026 Fashion Store. All rights reserved.</p>
    </footer>

    <!-- Nhúng Script -->
    <script type="module" src="js/main.js"></script>
    <script type="module" src="js/trang-lien-he.js"></script>
</body>
</html>