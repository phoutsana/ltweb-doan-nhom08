<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';

// Chỉ quản trị viên được truy cập.
if (
    !is_array($_SESSION['user'] ?? null) ||
    ($_SESSION['user']['vai_tro'] ?? '') !== 'quan_tri'
) {
    http_response_code(403);
    exit('403 Forbidden');
}

$pdo = db();

// Chỉ truy vấn thống kê, không thay đổi dữ liệu.
$tongSanPham = (int)$pdo
    ->query('SELECT COUNT(*) FROM san_pham')
    ->fetchColumn();

$tongDonHang = (int)$pdo
    ->query('SELECT COUNT(*) FROM don_hang')
    ->fetchColumn();

$tongLienHe = (int)$pdo
    ->query('SELECT COUNT(*) FROM lien_he')
    ->fetchColumn();

$tieuDe = 'Tổng quan quản trị - Fashion Store';
$trang = 'quan-tri';
$cssRieng = 'css/quan-tri-bt7-csp.css';

require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" data-bt7-qt="qt001">

    <h2>Tổng quan hệ thống</h2>

    <div data-bt7-qt="qt007">

        <table data-bt7-qt="qt008">
            <thead>
                <tr>
                    <th>Chỉ số</th>
                    <th>Số lượng</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Tổng số sản phẩm</td>
                    <td><?= $tongSanPham ?></td>
                </tr>

                <tr>
                    <td>Tổng số đơn hàng</td>
                    <td><?= $tongDonHang ?></td>
                </tr>

                <tr>
                    <td>Tổng số liên hệ</td>
                    <td><?= $tongLienHe ?></td>
                </tr>
            </tbody>
        </table>

    </div>

    <h2>Quản lý hệ thống</h2>

    <nav aria-label="Chức năng quản trị">
        <p>
            <a href="quan-tri-san-pham.php">
                Quản lý sản phẩm
            </a>
        </p>

        <p>
            <a href="them-san-pham.php">
                Thêm sản phẩm
            </a>
        </p>

        <p>
            <a href="don-hang.php">
                Quản lý đơn hàng
            </a>
        </p>

        <p>
            <a href="quan-tri.php">
                Quản lý liên hệ
            </a>
        </p>
    </nav>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
