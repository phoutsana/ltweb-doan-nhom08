<?php
/**
 * Phần đầu HTML và điều hướng chung của Fashion Store.
 * Nhận $goc, $tieuDe, tùy chọn $cssRieng; được kiểm tra qua trang cá nhân.
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($tieuDe ?? 'Fashion Store') ?></title>
    <meta name="description" content="Trang cá nhân thành viên Fashion Store.">
    <link rel="stylesheet" href="<?= e($goc ?? '') ?>css/style.css">
    <?php if (isset($cssRieng) && is_string($cssRieng) && $cssRieng !== ''): ?>
        <link rel="stylesheet" href="<?= e($cssRieng) ?>">
    <?php endif; ?>
</head>
<body class="bo-cuc-trang">
    <header class="dau-trang">
        <h1>Fashion Store</h1>
        <p class="dau-trang__khau-hieu">Cửa hàng thời trang trực tuyến</p>
    </header>
    <nav class="dieu-huong" aria-label="Điều hướng chính">
        <button type="button" class="nut-menu" aria-expanded="false" aria-controls="menu-chinh">☰ Menu</button>
        <ul class="dieu-huong__danh-sach menu" id="menu-chinh">
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="<?= e($goc) ?>index.html">Trang chủ</a></li>
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="<?= e($goc) ?>danh-sach.html">Sản phẩm</a></li>
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="<?= e($goc) ?>chi-tiet.html">Chi tiết sản phẩm</a></li>
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="<?= e($goc) ?>gioi-thieu.html">Giới thiệu</a></li>
            <li class="dieu-huong__muc"><a class="dieu-huong__lien-ket" href="<?= e($goc) ?>lien-he.html">Liên hệ</a></li>
        </ul>
    </nav>
