<?php
require_once __DIR__ . '/inc/config.php';
http_response_code(404); // Trả về đúng mã 404 cho trình duyệt
$tieuDe = '404 - Không tìm thấy trang';
$trang = '404';
$cssRieng = 'css/404-bt7-csp.css';
require_once __DIR__ . '/inc/header.php';
?>
<main class="noi-dung-chinh" data-bt7-csp="csp001">
    <h1 data-bt7-csp="csp002">404</h1>
    <h2 data-bt7-csp="csp003">Rất tiếc, trang bạn tìm kiếm không tồn tại!</h2>
    <a href="index.php" class="nut-bam nut-bam--chinh">Về trang chủ</a>
</main>
<?php require_once __DIR__ . '/inc/footer.php'; ?>