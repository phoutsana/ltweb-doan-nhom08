<?php
// inc/header.php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

            <!-- PHÂN QUYỀN HIỂN THỊ MENU CHO ADMIN VÀ KHÁCH HÀNG -->
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['vai_tro'] === 'quan_tri'): ?>
                <li class="dieu-huong__muc" style="position: relative;" id="menu-quan-tri-parent">
                    <!-- Đổi href thành javascript để bấm không bị nhảy trang mất kiểm soát -->
                    <a class="dieu-huong__lien-ket" href="javascript:void(0);" id="btn-toggle-admin" style="color: #fcd34d;">Quản trị viên ▾</a>
                    
                    <!-- Menu con cho Admin chuyển đổi qua lại giữa các trang quản lý -->
                    <ul class="menu-con" id="submenu-admin" style="display: none; position: absolute; top: 100%; left: 0; background: #1e3a8a; list-style: none; padding: 10px 0; margin: 0; min-width: 190px; box-shadow: 0 4px 6px rgba(0,0,0,0.2); z-index: 100; border-radius: 4px;">
                        <li style="padding: 6px 15px;"><a href="<?= $goc ?? '' ?>quan-tri-san-pham.php" style="color: white; text-decoration: none; display: block;">Quản lý sản phẩm</a></li>
                        <li style="padding: 6px 15px;"><a href="<?= $goc ?? '' ?>them-san-pham.php" style="color: white; text-decoration: none; display: block;">Thêm sản phẩm mới</a></li>
                        <li style="padding: 6px 15px;"><a href="<?= $goc ?? '' ?>quan-tri.php" style="color: white; text-decoration: none; display: block;">Quản lý liên hệ</a></li>
                        <li style="padding: 6px 15px;"><a href="<?= $goc ?? '' ?>don-hang.php" style="color: white; text-decoration: none; display: block;">Quản lý đơn hàng</a></li>
                    </ul>
                </li>
                
                <script>
                    // Xử lý sự kiện cả Hover (máy tính) lẫn Click (màn hình nhỏ/mobile)
                    document.addEventListener('DOMContentLoaded', function() {
                        const adminParent = document.getElementById('menu-quan-tri-parent');
                        const subMenu = document.getElementById('submenu-admin');
                        const btnToggle = document.getElementById('btn-toggle-admin');

                        if (adminParent && subMenu && btnToggle) {
                            // Dành cho máy tính (Hover)
                            adminParent.addEventListener('mouseenter', () => subMenu.style.display = 'block');
                            adminParent.addEventListener('mouseleave', () => subMenu.style.display = 'none');

                            // Dành cho màn hình nhỏ / mobile (Click vào chữ Quản trị viên để bật/tắt menu con)
                            btnToggle.addEventListener('click', function(e) {
                                e.preventDefault();
                                subMenu.style.display = subMenu.style.display === 'block' ? 'none' : 'block';
                            });
                        }
                    });
                </script>

                <li class="dieu-huong__muc">
                    <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>dang-xuat.php">Đăng xuất</a>
                </li>
            <?php elseif (isset($_SESSION['user']) && !empty($_SESSION['user'])): ?>
                <li class="dieu-huong__muc">
                    <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>don-hang.php">Đơn hàng của tôi</a>
                </li>
                <li class="dieu-huong__muc">
                    <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>dang-xuat.php">Đăng xuất</a>
                </li>
            <?php else: ?>
                <!-- NÚT ĐĂNG KÝ VÀ ĐĂNG NHẬP CHO KHÁCH CHƯA ĐĂNG NHẬP -->
                <li class="dieu-huong__muc">
                    <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>dang-ky.php" <?= ($trang ?? '') === 'dang-ky' ? 'aria-current="page"' : '' ?>>Đăng ký</a>
                </li>
                <li class="dieu-huong__muc">
                    <a class="dieu-huong__lien-ket" href="<?= $goc ?? '' ?>dang-nhap.php" <?= ($trang ?? '') === 'dang-nhap' ? 'aria-current="page"' : '' ?>>Đăng nhập</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>