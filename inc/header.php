
<?php
// inc/header.php
// Trang goi da nap inc/config.php truoc khi nap header.

// BT7: Nap bo ham CSRF cho form dang xuat
require_once __DIR__ . '/csrf.php';

// Cau hinh duong dan va giao dien
$gocTrang = (string) ($goc ?? '');

$cssTrang = isset($cssRieng) && is_string($cssRieng)
    ? $cssRieng
    : '';

$tieuDeTrang = (string) ($tieuDe ?? 'Fashion Store');
$trangHienTai = (string) ($trang ?? '');

// Kiem tra trang thai dang nhap
$nguoiDung = isset($_SESSION['user']) &&
    is_array($_SESSION['user'])
        ? $_SESSION['user']
        : null;

$laQuanTri = $nguoiDung !== null &&
    ($nguoiDung['vai_tro'] ?? '') === 'quan_tri';

// Lay so luong san pham trong gio hang
$gioHangSvc = new \App\Services\GioHang();
$tongSoLuong = (int) $gioHangSvc->tongSoLuong();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= e($tieuDeTrang) ?></title>

    <meta name="description"
          content="Fashion Store - Website bán quần áo trực tuyến với nhiều sản phẩm thời trang dành cho nam và nữ.">

    <link rel="stylesheet"
          href="<?= e($gocTrang . 'css/style.css') ?>">

    <link rel="stylesheet"
          href="<?= e($gocTrang . 'css/header-bt7.css') ?>">

    <?php if ($cssTrang !== ''): ?>
        <link rel="stylesheet"
              href="<?= e($gocTrang . $cssTrang) ?>">
    <?php endif; ?>

    <script
        src="<?= e($gocTrang . 'js/admin-menu.js') ?>"
        defer>
    </script>
<link rel="stylesheet" href="<?= e($gocTrang . 'css/admin-menu-bt7.css') ?>">
</head>

<body class="bo-cuc-trang">

<header class="dau-trang bt7-site-header">

    <a href="<?= e($gocTrang . 'index.php') ?>"
       class="bt7-site-logo-link">

        <img
            src="<?= e($gocTrang . 'images/logo.png') ?>"
            alt="Fashion Store Logo"
            class="bt7-site-logo"
            width="70"
            height="70">
    </a>

    <h1 class="bt7-site-title">
        Fashion Store
    </h1>

    <p class="dau-trang__khau-hieu bt7-site-slogan">
        Cửa hàng thời trang trực tuyến
    </p>

</header>

<nav class="dieu-huong" aria-label="Điều hướng chính">

    <button
        type="button"
        class="nut-menu"
        aria-expanded="false"
        aria-controls="menu-chinh">
        ☰ Menu
    </button>

    <ul class="dieu-huong__danh-sach menu" id="menu-chinh">

        <!-- TRANG CHU -->
        <li class="dieu-huong__muc">
            <a
                class="dieu-huong__lien-ket"
                href="<?= e($gocTrang . 'index.php') ?>"
                <?= $trangHienTai === 'trang-chu'
                    ? 'aria-current="page"'
                    : '' ?>>
                Trang chủ
            </a>
        </li>

        <!-- SAN PHAM -->
        <li class="dieu-huong__muc">
            <a
                class="dieu-huong__lien-ket"
                href="<?= e($gocTrang . 'danh-sach.php') ?>"
                <?= $trangHienTai === 'san-pham'
                    ? 'aria-current="page"'
                    : '' ?>>
                Sản phẩm
            </a>
        </li>

        <!-- GIOI THIEU -->
        <li class="dieu-huong__muc">
            <a
                class="dieu-huong__lien-ket"
                href="<?= e($gocTrang . 'gioi-thieu.php') ?>"
                <?= $trangHienTai === 'gioi-thieu'
                    ? 'aria-current="page"'
                    : '' ?>>
                Giới thiệu
            </a>
        </li>

        <!-- LIEN HE -->
        <li class="dieu-huong__muc">
            <a
                class="dieu-huong__lien-ket"
                href="<?= e($gocTrang . 'lien-he.php') ?>"
                <?= $trangHienTai === 'lien-he'
                    ? 'aria-current="page"'
                    : '' ?>>
                Liên hệ
            </a>
        </li>

        <!-- GIO HANG -->
        <li class="dieu-huong__muc">
            <a
                class="dieu-huong__lien-ket"
                href="<?= e($gocTrang . 'gio-hang.php') ?>"
                <?= $trangHienTai === 'gio-hang'
                    ? 'aria-current="page"'
                    : '' ?>>

                Giỏ hàng (
                <span class="so-luong-gio-hang"><?= $tongSoLuong ?></span>
                )
            </a>
        </li>

        <?php if ($laQuanTri): ?>

            <!-- MENU QUAN TRI -->
            <li
                class="dieu-huong__muc bt7-admin-menu-parent"
                id="menu-quan-tri-parent">

                <button
                    type="button"
                    class="dieu-huong__lien-ket bt7-admin-menu-toggle"
                    id="btn-toggle-admin"
                    aria-expanded="false"
                    aria-controls="submenu-admin">

                    Quản trị viên ▾
                </button>

                <ul
                    class="menu-con bt7-admin-submenu"
                    id="submenu-admin">

                    <li>
                        <a href="<?= e($gocTrang . 'quan-tri-san-pham.php') ?>">
                            Quản lý sản phẩm
                        </a>
                    </li>

                    <li>
                        <a href="<?= e($gocTrang . 'them-san-pham.php') ?>">
                            Thêm sản phẩm mới
                        </a>
                    </li>

                    <li>
                        <a href="<?= e($gocTrang . 'quan-tri.php') ?>">
                            Quản lý liên hệ
                        </a>
                    </li>

                    <li>
                        <a href="<?= e($gocTrang . 'don-hang.php') ?>">
                            Quản lý đơn hàng
                        </a>
                    </li>

                </ul>
            </li>

            <!-- BT7: DANG XUAT QUAN TRI BANG POST -->
            <li class="dieu-huong__muc">

                <form
                    method="post"
                    action="<?= e($gocTrang . 'dang-xuat.php') ?>"
                    class="bt7-logout-form">

                    <?= csrf_field() ?>

                    <button
                        type="submit"
                        class="dieu-huong__lien-ket bt7-logout-button">
                        Đăng xuất
                    </button>

                </form>
            </li>

        <?php elseif ($nguoiDung !== null): ?>

            <!-- DON HANG CUA KHACH HANG -->
            <li class="dieu-huong__muc">
                <a
                    class="dieu-huong__lien-ket"
                    href="<?= e($gocTrang . 'don-hang.php') ?>">
                    Đơn hàng của tôi
                </a>
            </li>

            <!-- BT7: DANG XUAT KHACH HANG BANG POST -->
            <li class="dieu-huong__muc">

                <form
                    method="post"
                    action="<?= e($gocTrang . 'dang-xuat.php') ?>"
                    class="bt7-logout-form">

                    <?= csrf_field() ?>

                    <button
                        type="submit"
                        class="dieu-huong__lien-ket bt7-logout-button">
                        Đăng xuất
                    </button>

                </form>
            </li>

        <?php else: ?>

            <!-- DANG KY -->
            <li class="dieu-huong__muc">
                <a
                    class="dieu-huong__lien-ket"
                    href="<?= e($gocTrang . 'dang-ky.php') ?>"
                    <?= $trangHienTai === 'dang-ky'
                        ? 'aria-current="page"'
                        : '' ?>>
                    Đăng ký
                </a>
            </li>

            <!-- DANG NHAP -->
            <li class="dieu-huong__muc">
                <a
                    class="dieu-huong__lien-ket"
                    href="<?= e($gocTrang . 'dang-nhap.php') ?>"
                    <?= $trangHienTai === 'dang-nhap'
                        ? 'aria-current="page"'
                        : '' ?>>
                    Đăng nhập
                </a>
            </li>

        <?php endif; ?>

    </ul>
</nav>
