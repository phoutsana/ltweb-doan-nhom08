<?php
declare(strict_types=1);

// inc/header.php
// Các trang phải nạp inc/config.php trước khi nạp header.

require_once __DIR__ . '/csrf.php';

// Cấu hình đường dẫn và giao diện.
$gocTrang = (string)($goc ?? '');

$cssTrang = isset($cssRieng) && is_string($cssRieng)
    ? $cssRieng
    : '';

$tieuDeTrang = (string)($tieuDe ?? 'Fashion Store');
$trangHienTai = (string)($trang ?? '');

// Kiểm tra trạng thái đăng nhập.
$nguoiDung = isset($_SESSION['user']) &&
    is_array($_SESSION['user'])
        ? $_SESSION['user']
        : null;

$laQuanTri = $nguoiDung !== null &&
    ($nguoiDung['vai_tro'] ?? '') === 'quan_tri';

// BT7: Đếm giỏ hàng theo $_SESSION['gio'].
// Tương thích api/gio-hang.php và gio-hang.php.
$gioHangSession = $_SESSION['gio'] ?? [];
$tongSoLuong = 0;

if (is_array($gioHangSession)) {
    foreach ($gioHangSession as $item) {
        if (is_array($item)) {
            $soLuong = (int)($item['so_luong'] ?? 0);
        } else {
            $soLuong = (int)$item;
        }

        if ($soLuong > 0) {
            $tongSoLuong += $soLuong;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= e($tieuDeTrang) ?></title>

    <meta
        name="description"
        content="Fashion Store - Website bán quần áo trực tuyến với nhiều sản phẩm thời trang dành cho nam và nữ."
    >

    <!-- BT7: CSRF Token cho JavaScript fetch POST -->
    <meta
        name="csrf-token"
        content="<?= e(csrf_token()) ?>"
    >

    <!-- CSS chính -->
    <link
        rel="stylesheet"
        href="<?= e($gocTrang . 'css/style.css') ?>"
    >

    <!-- CSS Header -->
    <link
        rel="stylesheet"
        href="<?= e($gocTrang . 'css/header-bt7.css') ?>"
    >

    <!-- CSS menu quản trị -->
    <link
        rel="stylesheet"
        href="<?= e($gocTrang . 'css/admin-menu-bt7.css') ?>"
    >

    <!-- CSS riêng theo trang -->
    <?php if ($cssTrang !== ''): ?>
        <link
            rel="stylesheet"
            href="<?= e($gocTrang . $cssTrang) ?>"
        >
    <?php endif; ?>

    <!-- JavaScript menu, không dùng script nội tuyến -->
    <script
        src="<?= e($gocTrang . 'js/admin-menu.js') ?>"
        defer
    ></script>
</head>

<body class="bo-cuc-trang">

<header class="dau-trang bt7-site-header">

    <a
        href="<?= e($gocTrang . 'index.php') ?>"
        class="bt7-site-logo-link"
    >
        <img
            src="<?= e($gocTrang . 'images/logo.png') ?>"
            alt="Fashion Store Logo"
            class="bt7-site-logo"
            width="70"
            height="70"
        >
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
        aria-controls="menu-chinh"
    >
        ☰ Menu
    </button>

    <ul class="dieu-huong__danh-sach menu" id="menu-chinh">

        <!-- Trang chủ -->
        <li class="dieu-huong__muc">
            <a
                class="dieu-huong__lien-ket"
                href="<?= e($gocTrang . 'index.php') ?>"
                <?= $trangHienTai === 'trang-chu'
                    ? 'aria-current="page"'
                    : '' ?>
            >
                Trang chủ
            </a>
        </li>

        <!-- Sản phẩm -->
        <li class="dieu-huong__muc">
            <a
                class="dieu-huong__lien-ket"
                href="<?= e($gocTrang . 'danh-sach.php') ?>"
                <?= $trangHienTai === 'san-pham'
                    ? 'aria-current="page"'
                    : '' ?>
            >
                Sản phẩm
            </a>
        </li>

        <!-- Giới thiệu -->
        <li class="dieu-huong__muc">
            <a
                class="dieu-huong__lien-ket"
                href="<?= e($gocTrang . 'gioi-thieu.php') ?>"
                <?= $trangHienTai === 'gioi-thieu'
                    ? 'aria-current="page"'
                    : '' ?>
            >
                Giới thiệu
            </a>
        </li>

        <!-- Liên hệ -->
        <li class="dieu-huong__muc">
            <a
                class="dieu-huong__lien-ket"
                href="<?= e($gocTrang . 'lien-he.php') ?>"
                <?= $trangHienTai === 'lien-he'
                    ? 'aria-current="page"'
                    : '' ?>
            >
                Liên hệ
            </a>
        </li>

        <!-- Giỏ hàng -->
        <li class="dieu-huong__muc">
            <a
                class="dieu-huong__lien-ket"
                href="<?= e($gocTrang . 'gio-hang.php') ?>"
                <?= $trangHienTai === 'gio-hang'
                    ? 'aria-current="page"'
                    : '' ?>
            >
                Giỏ hàng (
                <span class="so-luong-gio-hang">
                    <?= $tongSoLuong ?>
                </span>
                )
            </a>
        </li>

        <?php if ($laQuanTri): ?>

            <!-- Menu quản trị -->
            <li
                class="dieu-huong__muc bt7-admin-menu-parent"
                id="menu-quan-tri-parent"
            >

                <button
                    type="button"
                    class="dieu-huong__lien-ket bt7-admin-menu-toggle"
                    id="btn-toggle-admin"
                    aria-expanded="false"
                    aria-controls="submenu-admin"
                >
                    Quản trị viên ▾
                </button>

                <ul
                    class="menu-con bt7-admin-submenu"
                    id="submenu-admin"
                >

                    <li>
                        <a href="<?= e($gocTrang . 'thong-ke-quan-tri.php') ?>">
                            Tổng quan
                        </a>
                    </li>

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
                        <a href="<?= e($gocTrang . 'quan-tri.php?tab=lien-he') ?>">
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

            <!-- Đăng xuất quản trị viên bằng POST -->
            <li class="dieu-huong__muc">
                <form
                    method="post"
                    action="<?= e($gocTrang . 'dang-xuat.php') ?>"
                    class="bt7-logout-form"
                >
                    <?= csrf_field() ?>

                    <button
                        type="submit"
                        class="dieu-huong__lien-ket bt7-logout-button"
                    >
                        Đăng xuất
                    </button>
                </form>
            </li>

        <?php elseif ($nguoiDung !== null): ?>

            <!-- Đơn hàng của khách hàng -->
            <li class="dieu-huong__muc">
                <a
                    class="dieu-huong__lien-ket"
                    href="<?= e($gocTrang . 'don-hang.php') ?>"
                >
                    Đơn hàng của tôi
                </a>
            </li>

            <!-- Đăng xuất khách hàng bằng POST -->
            <li class="dieu-huong__muc">
                <form
                    method="post"
                    action="<?= e($gocTrang . 'dang-xuat.php') ?>"
                    class="bt7-logout-form"
                >
                    <?= csrf_field() ?>

                    <button
                        type="submit"
                        class="dieu-huong__lien-ket bt7-logout-button"
                    >
                        Đăng xuất
                    </button>
                </form>
            </li>

        <?php else: ?>

            <!-- Đăng ký -->
            <li class="dieu-huong__muc">
                <a
                    class="dieu-huong__lien-ket"
                    href="<?= e($gocTrang . 'dang-ky.php') ?>"
                    <?= $trangHienTai === 'dang-ky'
                        ? 'aria-current="page"'
                        : '' ?>
                >
                    Đăng ký
                </a>
            </li>

            <!-- Đăng nhập -->
            <li class="dieu-huong__muc">
                <a
                    class="dieu-huong__lien-ket"
                    href="<?= e($gocTrang . 'dang-nhap.php') ?>"
                    <?= $trangHienTai === 'dang-nhap'
                        ? 'aria-current="page"'
                        : '' ?>
                >
                    Đăng nhập
                </a>
            </li>

        <?php endif; ?>

    </ul>
</nav>
