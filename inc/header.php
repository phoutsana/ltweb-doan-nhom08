<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Đã bọc hàm e() để chống XSS theo yêu cầu của Giảng viên -->
    <title><?= isset($tieuDe) ? e($tieuDe) : e('Fashion Store') ?></title>
    <meta name="description" content="Fashion Store - Website bán quần áo trực tuyến với nhiều sản phẩm thời trang dành cho nam và nữ.">
    <link rel="stylesheet" type="text/css" href="<?= $goc ?? '' ?>css/style.css">
    <?php if (!empty($cssRieng)): ?>
    <link rel="stylesheet" href="<?= e($cssRieng) ?>">
<?php endif; ?>
</head>

<body class="bo-cuc-trang">
    <?php
    $gioHangSvc = new \App\Services\GioHang();
    $tongSoLuong = $gioHangSvc->tongSoLuong();
    ?>

    <header class="dau-trang" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 20px 0;">
        <a href="<?= $goc ?? '' ?>index.php" style="display: inline-block;">
            <img src="<?= $goc ?? '' ?>images/logo.png" alt="Fashion Store Logo" style="height: 70px; margin-bottom: 10px;">
        </a>
        <h1 style="margin: 0;">Fashion Store</h1>
        <p class="dau-trang__khau-hieu" style="margin-top: 5px;">Cửa hàng thời trang trực tuyến</p>
    </header>

    <nav class="dieu-huong" aria-label="Điều hướng chính">
        <button type="button" class="nut-menu" aria-expanded="false" aria-controls="menu-chinh">☰ Menu</button>

        <ul class="dieu-huong__danh-sach menu" id="menu-chinh">
            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>index.php" <?= ($trang ?? '') === 'trang-chu' ? 'aria-current="page"' : '' ?>>Trang chủ</a>
            </li>
            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>danh-sach.php" <?= ($trang ?? '') === 'san-pham' ? 'aria-current="page"' : '' ?>>Sản phẩm</a>
            </li>
            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>gioi-thieu.php" <?= ($trang ?? '') === 'gioi-thieu' ? 'aria-current="page"' : '' ?>>Giới thiệu</a>
            </li>
            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>lien-he.php" <?= ($trang ?? '') === 'lien-he' ? 'aria-current="page"' : '' ?>>Liên hệ</a>
            </li>
            <li class="dieu-huong__muc">
                <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>gio-hang.php" <?= ($trang ?? '') === 'gio-hang' ? 'aria-current="page"' : '' ?>>
                    Giỏ hàng (<span class="so-luong-gio-hang" style="font-weight: bold; color: #facc15;"><?= $tongSoLuong ?></span>)
                </a>
            </li>

            <!-- Đã bổ sung logic kiểm tra trạng thái đăng nhập -->
            <?php if (isset($_SESSION['da_dang_nhap']) && $_SESSION['da_dang_nhap'] === true): ?>
                <li class="dieu-huong__muc">
                    <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>quan-tri.php" style="color: #fcd34d;">Quản trị viên</a>
                </li>
                <li class="dieu-huong__muc">
                    <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>dang-xuat.php">Đăng xuất</a>
                </li>
            <?php else: ?>
                <li class="dieu-huong__muc">
                    <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>dang-nhap.php">Đăng nhập</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>