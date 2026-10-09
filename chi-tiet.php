<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chi tiết các sản phẩm - Fashion Store</title>

    <meta name="description" content="Thông tin chi tiết về các sản phẩm tại Fashion Store.">

    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bo-cuc-trang">

    <!-- ================= HEADER ================= -->
    <header class="dau-trang">
        <h1>Chi tiết sản phẩm</h1>

        <p class="dau-trang__khau-hieu">
            Thông tin các sản phẩm nổi bật
        </p>
    </header>


    <!-- ================= MENU ================= -->
    <nav class="dieu-huong" aria-label="Điều hướng chính">

        <button type="button" class="nut-menu" aria-expanded="false" aria-controls="menu-chinh">
            ☰ Menu
        </button>

        <ul class="dieu-huong__danh-sach menu" id="menu-chinh">

            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="index.html">
                    Trang chủ
                </a>
            </li>

            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="danh-sach.html">
                    Sản phẩm
                </a>
            </li>
            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="chi-tiet.html">
                    Chi tiết sản phẩm
                </a>
            </li>
            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="gioi-thieu.html">
                    Giới thiệu
                </a>
            </li>
            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="lien-he.html">
                    Liên hệ
                </a>
            </li>

            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="danh-sach.html">

                    Yêu thích
                    (
                    <span class="so-luong-yeu-thich" aria-live="polite">
                        0
                    </span>
                    )

                </a>
            </li>

        </ul>

    </nav>


    <!-- ================= NỘI DUNG CHÍNH ================= -->
    <main class="noi-dung-chinh">


        <!-- ==================================================
             KHU VỰC HIỂN THỊ ĐỘNG KHI CÓ JS
             Ví dụ:
             chi-tiet.html?id=1
             ================================================== -->

        <section id="khung-chi-tiet" class="the-thong-tin khoi-noi-dung" style="display: none;">

            <!-- Trạng thái tải -->
            <p id="trang-thai-chi-tiet" aria-live="polite" style="font-weight: bold; color: #2563eb; padding: 20px;">

                Đang tải thông tin sản phẩm...

            </p>


            <!-- Nội dung sản phẩm động -->
            <div id="noi-dung-chi-tiet" style="display: none;">


                <!-- TÊN SẢN PHẨM -->
                <header>

                    <h2 id="ct-ten">Chi tiết sản phẩm</h2>

                </header>


                <!-- HÌNH ẢNH -->
                <figure>

                    <img id="ct-anh" src="images/logo.png" alt="Ảnh sản phẩm" width="500" height="500"
                        style="max-width: 100%; height: auto; border-radius: 8px;">

                    <figcaption id="ct-figcaption"></figcaption>

                </figure>


                <!-- MÔ TẢ -->
                <section class="khoi-noi-dung">

                    <h2>Mô tả sản phẩm</h2>

                    <p id="ct-mo-ta"></p>

                </section>


                <!-- THÔNG SỐ SẢN PHẨM -->
                <section class="khoi-noi-dung">

                    <h2>Thông số sản phẩm</h2>

                    <div class="hop-bang">

                        <table>

                            <caption id="ct-caption-bang"></caption>

                            <thead>

                                <tr>

                                    <th scope="col">
                                        Thông tin
                                    </th>

                                    <th scope="col">
                                        Chi tiết
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <tr>

                                    <td>
                                        Danh mục
                                    </td>

                                    <td id="ct-danh-muc"></td>

                                </tr>


                                <tr>

                                    <td>
                                        Chất liệu
                                    </td>

                                    <td id="ct-chat-lieu"></td>

                                </tr>


                                <tr>

                                    <td>
                                        Màu sắc
                                    </td>

                                    <td id="ct-mau-sac"></td>

                                </tr>


                                <tr>

                                    <td>
                                        Kích thước
                                    </td>

                                    <td id="ct-kich-thuoc"></td>

                                </tr>


                                <tr>

                                    <td>
                                        Giá
                                    </td>

                                    <td id="ct-gia" style="color: #dc2626; font-weight: bold;">
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </section>


                <!-- ==================================================
                     VIDEO GIỚI THIỆU SẢN PHẨM - PHẦN MỚI THÊM
                     ================================================== -->

                <section class="khoi-noi-dung">

                    <h2>
                        Video giới thiệu sản phẩm
                    </h2>

                    <div class="khung-video">

                        <iframe id="ct-video" width="560" height="315" src="about:blank"
                            title="Video giới thiệu sản phẩm"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                        </iframe>

                    </div>

                </section>


                <!-- NÚT YÊU THÍCH -->
                <button type="button" id="nut-yeu-thich-ct" class="nut-bam nut-bam--chinh" style="margin-top: 15px;">

                    ♡ Yêu thích

                </button>


                <hr class="phan-cach">

            </div>

        </section>


        <!-- ==================================================
             12 SẢN PHẨM TĨNH DỰ PHÒNG
             ================================================== -->


        <!-- ================= SẢN PHẨM 1 ================= -->
        <article id="ao-thun" class="the-thong-tin khoi-noi-dung">

            <header>

                <h2>
                    Áo thun nam Basic
                </h2>

            </header>


            <figure>

                <img src="images/ao-thun-nam.jpg" alt="Áo thun nam Basic màu trắng" width="500" height="500">

                <figcaption>
                    Áo thun nam Basic
                </figcaption>

            </figure>


            <section class="khoi-noi-dung">

                <h2>Mô tả sản phẩm</h2>

                <p>
                    Áo thun nam Basic có thiết kế đơn giản,
                    phù hợp sử dụng hằng ngày và dễ dàng kết hợp
                    với nhiều loại trang phục. Chất vải thoáng mát,
                    thấm hút mồ hôi cực tốt.
                </p>

            </section>


            <section class="khoi-noi-dung">

                <h2>Thông số sản phẩm</h2>

                <div class="hop-bang">

                    <table>

                        <caption>
                            Thông số áo thun nam Basic
                        </caption>

                        <thead>

                            <tr>

                                <th scope="col">
                                    Thông tin
                                </th>

                                <th scope="col">
                                    Chi tiết
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>
                                <td>Chất liệu</td>
                                <td>Cotton</td>
                            </tr>

                            <tr>
                                <td>Màu sắc</td>
                                <td>Xanh</td>
                            </tr>

                            <tr>
                                <td>Kích thước</td>
                                <td>S, M, L, XL</td>
                            </tr>

                            <tr>
                                <td>Giá</td>
                                <td>199.000 VNĐ</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>


            <!-- VIDEO SẢN PHẨM 1 -->
            <section class="khoi-noi-dung">

                <h2>
                    Video giới thiệu
                </h2>

                <div class="khung-video">

                    <iframe width="560" height="315" src="https://www.youtube.com/embed/Q4T5uVfQ03w?si=dF3zYc-sNa3FOJVl"
                        title="Video giới thiệu sản phẩm áo thun nam"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>

                </div>

            </section>


            <hr class="phan-cach">

        </article>


        <!-- ================= SẢN PHẨM 2 ================= -->
        <article id="ao-so-mi" class="the-thong-tin khoi-noi-dung">

            <header>

                <h2>
                    Áo sơ mi nữ thanh lịch
                </h2>

            </header>


            <figure>

                <img src="images/ao-so-mi-nu.jpg" alt="Áo sơ mi nữ thanh lịch" width="500" height="500" loading="lazy">

                <figcaption>
                    Áo sơ mi nữ thanh lịch
                </figcaption>

            </figure>


            <section class="khoi-noi-dung">

                <h2>Mô tả sản phẩm</h2>

                <p>
                    Áo sơ mi nữ với thiết kế thanh lịch,
                    chuẩn form dáng công sở hiện đại.
                    Phù hợp mặc đi làm hoặc đi chơi,
                    dễ dàng kết hợp cùng chân váy hoặc quần âu.
                </p>

            </section>


            <section class="khoi-noi-dung">

                <h2>Thông số sản phẩm</h2>

                <div class="hop-bang">

                    <table>

                        <caption>
                            Thông số áo sơ mi nữ
                        </caption>

                        <thead>

                            <tr>

                                <th scope="col">
                                    Thông tin
                                </th>

                                <th scope="col">
                                    Chi tiết
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>
                                <td>Chất liệu</td>
                                <td>Lụa / Voan</td>
                            </tr>

                            <tr>
                                <td>Màu sắc</td>
                                <td>Trắng</td>
                            </tr>

                            <tr>
                                <td>Kích thước</td>
                                <td>S, M, L</td>
                            </tr>

                            <tr>
                                <td>Giá</td>
                                <td>299.000 VNĐ</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>


            <section class="khoi-noi-dung">

                <h2>Video giới thiệu</h2>

                <div class="khung-video">

                    <iframe width="560" height="315" src="https://www.youtube.com/embed/Hf_dMGhF7zM?si=63-j2AgRkYY17aDy"
                        title="Video giới thiệu sản phẩm áo sơ mi nữ"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>

                </div>

            </section>


            <hr class="phan-cach">

        </article>


        <!-- ================= SẢN PHẨM 3 ================= -->
        <article id="quan-jean" class="the-thong-tin khoi-noi-dung">

            <header>

                <h2>
                    Quần jean nam dáng suông
                </h2>

            </header>


            <figure>

                <img src="images/quan-jean-nam.jpg" alt="Quần jean nam dáng suông" width="500" height="500"
                    loading="lazy">

                <figcaption>
                    Quần jean nam dáng suông
                </figcaption>

            </figure>


            <section class="khoi-noi-dung">

                <h2>Mô tả sản phẩm</h2>

                <p>
                    Quần jean nam chất liệu denim cao cấp,
                    giữ form tốt và không bị phai màu.
                    Thiết kế trẻ trung, phù hợp cho mọi hoạt động
                    hàng ngày.
                </p>

            </section>


            <section class="khoi-noi-dung">

                <h2>Thông số sản phẩm</h2>

                <div class="hop-bang">

                    <table>

                        <caption>
                            Thông số quần jean nam
                        </caption>

                        <thead>

                            <tr>

                                <th scope="col">
                                    Thông tin
                                </th>

                                <th scope="col">
                                    Chi tiết
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>
                                <td>Chất liệu</td>
                                <td>Denim 98%, Spandex 2%</td>
                            </tr>

                            <tr>
                                <td>Màu sắc</td>
                                <td>Xanh</td>
                            </tr>

                            <tr>
                                <td>Kích thước</td>
                                <td>29, 30, 31, 32</td>
                            </tr>

                            <tr>
                                <td>Giá</td>
                                <td>399.000 VNĐ</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>


            <section class="khoi-noi-dung">

                <h2>Video giới thiệu</h2>

                <div class="khung-video">

                    <iframe width="560" height="315" src="https://www.youtube.com/embed/yUdXs2msI_s?si=fwErv-M-9olib0PX"
                        title="Video giới thiệu sản phẩm quần jean nam"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>

                </div>

            </section>


            <hr class="phan-cach">

        </article>


        <!-- ================= SẢN PHẨM 4 ================= -->
        <article id="chan-vay" class="the-thong-tin khoi-noi-dung">

            <header>

                <h2>
                    Chân váy nữ chữ A
                </h2>

            </header>


            <figure>

                <img src="images/chan-vay-nu.jpg" alt="Chân váy nữ dáng chữ A" width="500" height="500" loading="lazy">

                <figcaption>
                    Chân váy nữ dáng chữ A
                </figcaption>

            </figure>


            <section class="khoi-noi-dung">

                <h2>Mô tả sản phẩm</h2>

                <p>
                    Chân váy form chữ A tôn dáng,
                    dễ thương và năng động.
                    Bên trong có may kèm quần lót bảo hộ
                    giúp bạn luôn tự tin vận động thoải mái.
                </p>

            </section>


            <section class="khoi-noi-dung">

                <h2>Thông số sản phẩm</h2>

                <div class="hop-bang">

                    <table>

                        <caption>
                            Thông số chân váy nữ
                        </caption>

                        <thead>

                            <tr>

                                <th scope="col">
                                    Thông tin
                                </th>

                                <th scope="col">
                                    Chi tiết
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>
                                <td>Chất liệu</td>
                                <td>Kaki mỏng</td>
                            </tr>

                            <tr>
                                <td>Màu sắc</td>
                                <td>Đen</td>
                            </tr>

                            <tr>
                                <td>Kích thước</td>
                                <td>S, M, L</td>
                            </tr>

                            <tr>
                                <td>Giá</td>
                                <td>349.000 VNĐ</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>


            <section class="khoi-noi-dung">

                <h2>Video giới thiệu</h2>

                <div class="khung-video">

                    <iframe width="560" height="315" src="https://www.youtube.com/embed/7IO8IB0DCvY?si=0tS1NXcnzt20UhJ_"
                        title="Video giới thiệu sản phẩm chân váy nữ"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>

                </div>

            </section>


            <hr class="phan-cach">

        </article>


        <!-- SẢN PHẨM 5 -->
        <article id="quan-kaki" class="the-thong-tin khoi-noi-dung">

            <header>
                <h2>Quần kaki nam</h2>
            </header>

            <figure>
                <img src="images/Quan_kaki_nam.jpg" alt="Quần kaki nam" width="500" height="500" loading="lazy">

                <figcaption>Quần kaki nam</figcaption>
            </figure>

            <section class="khoi-noi-dung">
                <h2>Mô tả sản phẩm</h2>

                <p>
                    Quần kaki nam với chất vải mềm mịn, co giãn nhẹ.
                    Kiểu dáng vừa vặn, lịch sự, cực kỳ phù hợp mặc đi
                    học và đi làm.
                </p>
            </section>

            <section class="khoi-noi-dung">
                <h2>Thông số sản phẩm</h2>

                <div class="hop-bang">

                    <table>

                        <caption>
                            Thông số quần kaki nam
                        </caption>

                        <thead>

                            <tr>
                                <th scope="col">Thông tin</th>
                                <th scope="col">Chi tiết</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr>
                                <td>Chất liệu</td>
                                <td>Kaki cotton</td>
                            </tr>

                            <tr>
                                <td>Màu sắc</td>
                                <td>Vàng Đất</td>
                            </tr>

                            <tr>
                                <td>Kích thước</td>
                                <td>29, 30, 31, 32</td>
                            </tr>

                            <tr>
                                <td>Giá</td>
                                <td>320.000 VNĐ</td>
                            </tr>

                        </tbody>

                    </table>

                </div>
            </section>

            <section class="khoi-noi-dung">

                <h2>Video giới thiệu</h2>

                <div class="khung-video">

                    <iframe width="560" height="315" src="https://www.youtube.com/embed/CRd_dkot4BM?si=XNjcglVwoKsfcnN4"
                        title="Video giới thiệu sản phẩm quần kaki nam"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>

                </div>

            </section>

            <hr class="phan-cach">

        </article>


        <!-- SẢN PHẨM 6 -->
        <article id="ao-polo" class="the-thong-tin khoi-noi-dung">

            <header>
                <h2>Áo polo nam</h2>
            </header>

            <figure>

                <img src="images/Ao_polo_nam.jpg" alt="Áo polo nam" width="500" height="500" loading="lazy">

                <figcaption>
                    Áo polo nam
                </figcaption>

            </figure>

            <section class="khoi-noi-dung">

                <h2>Mô tả sản phẩm</h2>

                <p>
                    Áo polo nam thiết kế cổ bẻ đơn giản,
                    lịch sự nhưng không kém phần trẻ trung.
                    Rất dễ phối cùng quần jean hoặc kaki.
                </p>

            </section>

            <section class="khoi-noi-dung">

                <h2>Thông số sản phẩm</h2>

                <div class="hop-bang">

                    <table>

                        <caption>
                            Thông số áo polo nam
                        </caption>

                        <thead>

                            <tr>

                                <th scope="col">Thông tin</th>
                                <th scope="col">Chi tiết</th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr>
                                <td>Chất liệu</td>
                                <td>Cotton cá sấu</td>
                            </tr>

                            <tr>
                                <td>Màu sắc</td>
                                <td>Trắng</td>
                            </tr>

                            <tr>
                                <td>Kích thước</td>
                                <td>M, L, XL</td>
                            </tr>

                            <tr>
                                <td>Giá</td>
                                <td>220.000 VNĐ</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="khoi-noi-dung">

                <h2>Video giới thiệu</h2>

                <div class="khung-video">

                    <iframe width="560" height="315" src="https://www.youtube.com/embed/1at4WHmWhzA?si=oY-qAgdjuLnLYGux"
                        title="Video giới thiệu sản phẩm áo polo nam"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>

                </div>

            </section>

            <hr class="phan-cach">

        </article>


        <!-- SẢN PHẨM 7 -->
        <article id="quan-short" class="the-thong-tin khoi-noi-dung">

            <header>
                <h2>Quần short nam</h2>
            </header>

            <figure>

                <img src="images/Quan_short_nam.jpg" alt="Quần short nam" width="500" height="500" loading="lazy">

                <figcaption>
                    Quần short nam
                </figcaption>

            </figure>

            <section class="khoi-noi-dung">

                <h2>Mô tả sản phẩm</h2>

                <p>
                    Quần short nam mang lại cảm giác thoải mái tối đa
                    cho các hoạt động thể thao hoặc mặc ở nhà hằng ngày.
                </p>

            </section>

            <section class="khoi-noi-dung">

                <h2>Thông số sản phẩm</h2>

                <div class="hop-bang">

                    <table>

                        <caption>
                            Thông số quần short nam
                        </caption>

                        <thead>

                            <tr>
                                <th scope="col">Thông tin</th>
                                <th scope="col">Chi tiết</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr>
                                <td>Chất liệu</td>
                                <td>Thun lạnh</td>
                            </tr>

                            <tr>
                                <td>Màu sắc</td>
                                <td>Trắng</td>
                            </tr>

                            <tr>
                                <td>Kích thước</td>
                                <td>M, L, XL</td>
                            </tr>

                            <tr>
                                <td>Giá</td>
                                <td>180.000 VNĐ</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="khoi-noi-dung">

                <h2>Video giới thiệu</h2>

                <div class="khung-video">

                    <iframe width="560" height="315" src="https://www.youtube.com/embed/ABYuNdnU7Jk?si=v4j2jiIHc-M6HOrG"
                        title="Video giới thiệu sản phẩm quần short nam"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>

                </div>

            </section>

            <hr class="phan-cach">

        </article>


        <!-- SẢN PHẨM 8 -->
        <article id="ao-hoodie" class="the-thong-tin khoi-noi-dung">

            <header>
                <h2>Áo hoodie nam</h2>
            </header>

            <figure>

                <img src="images/ao-hoodie-nam.jpg" alt="Áo hoodie nam" width="500" height="500" loading="lazy">

                <figcaption>
                    Áo hoodie nam
                </figcaption>

            </figure>

            <section class="khoi-noi-dung">

                <h2>Mô tả sản phẩm</h2>

                <p>
                    Áo hoodie nam phong cách thời trang đường phố
                    trẻ trung, chất nỉ bông ấm áp, phù hợp cho những
                    ngày se lạnh.
                </p>

            </section>

            <section class="khoi-noi-dung">

                <h2>Thông số sản phẩm</h2>

                <div class="hop-bang">

                    <table>

                        <caption>
                            Thông số áo hoodie nam
                        </caption>

                        <thead>

                            <tr>
                                <th scope="col">Thông tin</th>
                                <th scope="col">Chi tiết</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr>
                                <td>Chất liệu</td>
                                <td>Nỉ bông cotton</td>
                            </tr>

                            <tr>
                                <td>Màu sắc</td>
                                <td>Đen</td>
                            </tr>

                            <tr>
                                <td>Kích thước</td>
                                <td>M, L, XL</td>
                            </tr>

                            <tr>
                                <td>Giá</td>
                                <td>420.000 VNĐ</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="khoi-noi-dung">

                <h2>Video giới thiệu</h2>

                <div class="khung-video">

                    <iframe width="560" height="315" src="https://www.youtube.com/embed/U08_Wu7wPn8?si=imH9DASAIDKMcKSP"
                        title="Video giới thiệu sản phẩm áo hoodie nam"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>

                </div>

            </section>

            <hr class="phan-cach">

        </article>


        <!-- SẢN PHẨM 9 -->
        <article id="ao-khoac-gio" class="the-thong-tin khoi-noi-dung">

            <header>
                <h2>Áo khoác gió</h2>
            </header>

            <figure>

                <img src="images/Ao_khoac_gio.jpg" alt="Áo khoác gió" width="500" height="500" loading="lazy">

                <figcaption>
                    Áo khoác gió
                </figcaption>

            </figure>

            <section class="khoi-noi-dung">

                <h2>Mô tả sản phẩm</h2>

                <p>
                    Áo khoác gió 2 lớp nhẹ nhàng, cản gió và chống thấm
                    nước tốt. Rất tiện lợi khi đi xe máy hoặc di chuyển
                    ngoài trời.
                </p>

            </section>

            <section class="khoi-noi-dung">

                <h2>Thông số sản phẩm</h2>

                <div class="hop-bang">

                    <table>

                        <caption>
                            Thông số áo khoác gió
                        </caption>

                        <thead>

                            <tr>
                                <th scope="col">Thông tin</th>
                                <th scope="col">Chi tiết</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr>
                                <td>Chất liệu</td>
                                <td>Polyester cản gió</td>
                            </tr>

                            <tr>
                                <td>Màu sắc</td>
                                <td>Đen</td>
                            </tr>

                            <tr>
                                <td>Kích thước</td>
                                <td>M, L, XL</td>
                            </tr>

                            <tr>
                                <td>Giá</td>
                                <td>350.000 VNĐ</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="khoi-noi-dung">

                <h2>Video giới thiệu</h2>

                <div class="khung-video">

                    <iframe width="560" height="315" src="https://www.youtube.com/embed/C8373DrkI0w?si=BDVWzNU5Fe57z0c3"
                        title="Video giới thiệu sản phẩm áo khoác gió"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>

                </div>

            </section>

            <hr class="phan-cach">

        </article>


        <!-- SẢN PHẨM 10 -->
        <article id="quan-jogger" class="the-thong-tin khoi-noi-dung">

            <header>
                <h2>Quần jogger nam</h2>
            </header>

            <figure>

                <img src="images/quan-jogger-nam.jpg" alt="Quần jogger nam" width="500" height="500" loading="lazy">

                <figcaption>
                    Quần jogger nam
                </figcaption>

            </figure>

            <section class="khoi-noi-dung">

                <h2>Mô tả sản phẩm</h2>

                <p>
                    Quần jogger nam với bo chun gấu quần tạo sự gọn gàng,
                    tôn dáng. Phong cách thể thao năng động, chất liệu
                    co giãn 4 chiều.
                </p>

            </section>

            <section class="khoi-noi-dung">

                <h2>Thông số sản phẩm</h2>

                <div class="hop-bang">

                    <table>

                        <caption>
                            Thông số quần jogger nam
                        </caption>

                        <thead>

                            <tr>
                                <th scope="col">Thông tin</th>
                                <th scope="col">Chi tiết</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr>
                                <td>Chất liệu</td>
                                <td>Thun nỉ</td>
                            </tr>

                            <tr>
                                <td>Màu sắc</td>
                                <td>Đen</td>
                            </tr>

                            <tr>
                                <td>Kích thước</td>
                                <td>M, L, XL</td>
                            </tr>

                            <tr>
                                <td>Giá</td>
                                <td>280.000 VNĐ</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="khoi-noi-dung">

                <h2>Video giới thiệu</h2>

                <div class="khung-video">

                    <iframe width="560" height="315" src="https://www.youtube.com/embed/g1aicLHOiks?si=qB_2YpZ0JTyFlWNv"
                        title="Video giới thiệu sản phẩm quần jogger nam"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>

                </div>

            </section>

            <hr class="phan-cach">

        </article>


        <!-- SẢN PHẨM 11 -->
        <article id="ao-bomber" class="the-thong-tin khoi-noi-dung">

            <header>
                <h2>Áo khoác bomber</h2>
            </header>

            <figure>

                <img src="images/ao-bomber-nam.jpg" alt="Áo khoác bomber" width="500" height="500" loading="lazy">

                <figcaption>
                    Áo khoác bomber
                </figcaption>

            </figure>

            <section class="khoi-noi-dung">

                <h2>Mô tả sản phẩm</h2>

                <p>
                    Áo khoác bomber là item không thể thiếu cho những
                    tín đồ thời trang. Dáng áo bo eo khỏe khoắn,
                    chất dù cao cấp dày dặn.
                </p>

            </section>

            <section class="khoi-noi-dung">

                <h2>Thông số sản phẩm</h2>

                <div class="hop-bang">

                    <table>

                        <caption>
                            Thông số áo khoác bomber
                        </caption>

                        <thead>

                            <tr>
                                <th scope="col">Thông tin</th>
                                <th scope="col">Chi tiết</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr>
                                <td>Chất liệu</td>
                                <td>Dù dù lót chần bông</td>
                            </tr>

                            <tr>
                                <td>Màu sắc</td>
                                <td>Đen</td>
                            </tr>

                            <tr>
                                <td>Kích thước</td>
                                <td>M, L, XL</td>
                            </tr>

                            <tr>
                                <td>Giá</td>
                                <td>450.000 VNĐ</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="khoi-noi-dung">

                <h2>Video giới thiệu</h2>

                <div class="khung-video">

                    <iframe width="560" height="315" src="https://www.youtube.com/embed/lwOYguJR95Y?si=acGZD6ajZKHAKmp4"
                        title="Video giới thiệu sản phẩm áo khoác bomber"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>

                </div>

            </section>

            <hr class="phan-cach">

        </article>


        <!-- SẢN PHẨM 12 -->
        <article id="ao-thun-xanh" class="the-thong-tin khoi-noi-dung">

            <header>
                <h2>Áo thun nam xanh</h2>
            </header>

            <figure>

                <img src="images/ao-thun-xanh.jpg" alt="Áo thun nam xanh" width="500" height="500" loading="lazy">

                <figcaption>
                    Áo thun nam xanh
                </figcaption>

            </figure>

            <section class="khoi-noi-dung">

                <h2>Mô tả sản phẩm</h2>

                <p>
                    Áo thun nam màu xanh tươi mát, cổ tròn basic.
                    Form áo rộng vừa phải, chất thun lạnh thấm hút
                    mồ hôi cực kỳ tốt cho mùa hè.
                </p>

            </section>

            <section class="khoi-noi-dung">

                <h2>Thông số sản phẩm</h2>

                <div class="hop-bang">

                    <table>

                        <caption>
                            Thông số áo thun nam xanh
                        </caption>

                        <thead>

                            <tr>
                                <th scope="col">Thông tin</th>
                                <th scope="col">Chi tiết</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr>
                                <td>Chất liệu</td>
                                <td>Thun lạnh 4 chiều</td>
                            </tr>

                            <tr>
                                <td>Màu sắc</td>
                                <td>Xanh</td>
                            </tr>

                            <tr>
                                <td>Kích thước</td>
                                <td>M, L, XL</td>
                            </tr>

                            <tr>
                                <td>Giá</td>
                                <td>150.000 VNĐ</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="khoi-noi-dung">

                <h2>Video giới thiệu</h2>

                <div class="khung-video">

                    <iframe width="560" height="315" src="https://www.youtube.com/embed/Mpii6eYHSqk?si=Mkt3-09k-iAwNHdU"
                        title="Video giới thiệu sản phẩm áo thun nam xanh"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>

                </div>

            </section>

            <hr class="phan-cach">

        </article>

    </main>


    <!-- ================= FOOTER ================= -->
    <footer class="chan-trang">

        <p>
            &copy; 2026 Fashion Store. All rights reserved.
        </p>

    </footer>


    <!-- ================= JAVASCRIPT ================= -->

    <script type="module" src="js/main.js">
    </script>

    <script type="module" src="js/trang-chi-tiet.js">
    </script>

</body>

</html>