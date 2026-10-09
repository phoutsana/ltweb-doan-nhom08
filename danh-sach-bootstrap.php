<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Danh sách Bootstrap - Fashion Store</title>

    <meta name="description"
        content="Danh sách sản phẩm thời trang Fashion Store Nhóm 08 được trình bày bằng Bootstrap 5.">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- CSS riêng -->
    <link rel="stylesheet" href="css/bootstrap-custom.css">
</head>

<body class="bg-light">

    <!-- ==================== HEADER ==================== -->
    <header class="bg-primary text-white text-center py-3">
        <div class="container">
            <h1 class="m-0 fs-4 fw-bold">
                Fashion Store - Phiên bản Bootstrap 5
            </h1>

            <p class="mb-0 mt-1">
                Danh sách sản phẩm thời trang
            </p>
        </div>
    </header>


    <!-- ==================== NAVBAR ==================== -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top" aria-label="Điều hướng Bootstrap">

        <div class="container">

            <a class="navbar-brand fw-bold" href="index.html">
                FashionStore
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuBootstrap"
                aria-controls="menuBootstrap" aria-expanded="false" aria-label="Mở menu">

                <span class="navbar-toggler-icon"></span>
            </button>


            <div class="collapse navbar-collapse" id="menuBootstrap">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link" href="index.html">
                            Trang chủ
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="danh-sach-bootstrap.html">
                            Sản phẩm Bootstrap
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="danh-sach.html">
                            Sản phẩm
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="chi-tiet.html">
                            Chi tiết sản phẩm
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="gioi-thieu.html">
                            Giới thiệu
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="lien-he.html">
                            Liên hệ
                        </a>
                    </li>

                </ul>

                <!-- Yêu thích -->
                <span class="navbar-text text-white">
                    Yêu thích
                    (<span class="so-luong-yeu-thich" aria-live="polite">0</span>)
                </span>

            </div>
        </div>
    </nav>


    <!-- ==================== MAIN ==================== -->
    <main class="container my-4">

        <h2 class="mb-4 text-primary">
            Danh sách sản phẩm
        </h2>


        <!-- ==================== ALERT ==================== -->
        <div class="alert alert-info alert-dismissible fade show" role="alert">

            <strong>Ưu đãi khai trương:</strong>
            Miễn phí vận chuyển toàn quốc cho tất cả đơn hàng từ
            500.000 VNĐ!

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng">
            </button>
        </div>


        <!-- ==================== BẢNG SẢN PHẨM ==================== -->
        <section class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <h3 class="h5 m-0 text-primary">
                    Danh sách sản phẩm
                </h3>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover table-striped mb-0">

                        <caption class="px-3">
                            Danh sách sản phẩm Fashion Store
                        </caption>

                        <thead class="table-dark">

                            <tr>
                                <th scope="col">STT</th>
                                <th scope="col">Tên sản phẩm</th>
                                <th scope="col">Danh mục</th>
                                <th scope="col">Giá</th>
                                <th scope="col">Chi tiết</th>
                                <th scope="col">Yêu thích</th>
                            </tr>

                        </thead>


                        <tbody>

                            <tr>
                                <th scope="row">1</th>
                                <td>Áo thun nam basic</td>
                                <td>Áo</td>
                                <td>120.000 VNĐ</td>
                                <td>
                                    <a href="chi-tiet.html?id=1" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        ♡ Yêu thích
                                    </button>
                                </td>
                            </tr>


                            <tr>
                                <th scope="row">2</th>
                                <td>Áo sơ mi nữ thanh lịch</td>
                                <td>Áo</td>
                                <td>250.000 VNĐ</td>
                                <td>
                                    <a href="chi-tiet.html?id=2" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        ♡ Yêu thích
                                    </button>
                                </td>
                            </tr>


                            <tr>
                                <th scope="row">3</th>
                                <td>Quần jean nam dáng suông</td>
                                <td>Quần</td>
                                <td>399.000 VNĐ</td>
                                <td>
                                    <a href="chi-tiet.html?id=3" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        ♡ Yêu thích
                                    </button>
                                </td>
                            </tr>


                            <tr>
                                <th scope="row">4</th>
                                <td>Chân váy nữ chữ A</td>
                                <td>Váy</td>
                                <td>349.000 VNĐ</td>
                                <td>
                                    <a href="chi-tiet.html?id=4" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        ♡ Yêu thích
                                    </button>
                                </td>
                            </tr>


                            <tr>
                                <th scope="row">5</th>
                                <td>Quần kaki nam</td>
                                <td>Quần</td>
                                <td>320.000 VNĐ</td>
                                <td>
                                    <a href="chi-tiet.html?id=5" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        ♡ Yêu thích
                                    </button>
                                </td>
                            </tr>


                            <tr>
                                <th scope="row">6</th>
                                <td>Áo polo nam</td>
                                <td>Áo</td>
                                <td>220.000 VNĐ</td>
                                <td>
                                    <a href="chi-tiet.html?id=6" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        ♡ Yêu thích
                                    </button>
                                </td>
                            </tr>


                            <tr>
                                <th scope="row">7</th>
                                <td>Quần short nam</td>
                                <td>Quần</td>
                                <td>180.000 VNĐ</td>
                                <td>
                                    <a href="chi-tiet.html?id=7" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        ♡ Yêu thích
                                    </button>
                                </td>
                            </tr>


                            <tr>
                                <th scope="row">8</th>
                                <td>Áo hoodie nam</td>
                                <td>Áo khoác</td>
                                <td>420.000 VNĐ</td>
                                <td>
                                    <a href="chi-tiet.html?id=8" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        ♡ Yêu thích
                                    </button>
                                </td>
                            </tr>


                            <tr>
                                <th scope="row">9</th>
                                <td>Áo khoác gió</td>
                                <td>Áo khoác</td>
                                <td>350.000 VNĐ</td>
                                <td>
                                    <a href="chi-tiet.html?id=9" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        ♡ Yêu thích
                                    </button>
                                </td>
                            </tr>


                            <tr>
                                <th scope="row">10</th>
                                <td>Quần jogger nam</td>
                                <td>Quần</td>
                                <td>280.000 VNĐ</td>
                                <td>
                                    <a href="chi-tiet.html?id=10" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        ♡ Yêu thích
                                    </button>
                                </td>
                            </tr>


                            <tr>
                                <th scope="row">11</th>
                                <td>Áo khoác bomber</td>
                                <td>Áo khoác</td>
                                <td>450.000 VNĐ</td>
                                <td>
                                    <a href="chi-tiet.html?id=11" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        ♡ Yêu thích
                                    </button>
                                </td>
                            </tr>


                            <tr>
                                <th scope="row">12</th>
                                <td>Áo thun nam xanh</td>
                                <td>Áo</td>
                                <td>150.000 VNĐ</td>
                                <td>
                                    <a href="chi-tiet.html?id=12" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        ♡ Yêu thích
                                    </button>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>
            </div>
        </section>


        <!-- ==================== DANH MỤC ==================== -->
        <section class="mb-4">

            <h3 class="h4 text-primary mb-3">
                Danh mục sản phẩm
            </h3>

            <div class="row g-3">

                <div class="col-6 col-md-3">
                    <div class="card text-center h-100 shadow-sm">
                        <div class="card-body">
                            <h4 class="h5">Áo</h4>
                            <p class="mb-0 text-muted">
                                Áo thời trang
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card text-center h-100 shadow-sm">
                        <div class="card-body">
                            <h4 class="h5">Quần</h4>
                            <p class="mb-0 text-muted">
                                Quần thời trang
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card text-center h-100 shadow-sm">
                        <div class="card-body">
                            <h4 class="h5">Váy</h4>
                            <p class="mb-0 text-muted">
                                Váy nữ
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card text-center h-100 shadow-sm">
                        <div class="card-body">
                            <h4 class="h5">Áo khoác</h4>
                            <p class="mb-0 text-muted">
                                Áo khoác thời trang
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </section>

    </main>


    <!-- ==================== FOOTER ==================== -->
    <footer class="bg-dark text-white text-center py-4 mt-5">

        <p class="m-0">
            © 2026 Fashion Store.
            All rights reserved.
        </p>

    </footer>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>

</body>

</html>