<?php
require_once __DIR__ . '/inc/config.php';
http_response_code(404); // Trả về đúng mã 404 cho trình duyệt
$tieuDe = '404 - Không tìm thấy trang';
$trang = '404';
require_once __DIR__ . '/inc/header.php';
?>
<main class="noi-dung-chinh" style="text-align: center; padding: 100px 20px;">
    <h1 style="font-size: 4rem; color: #1e3a8a;">404</h1>
    <h2 style="margin-bottom: 20px;">Rất tiếc, trang bạn tìm kiếm không tồn tại!</h2>
    <a href="index.php" class="nut-bam nut-bam--chinh">Về trang chủ</a>
</main>
<?php require_once __DIR__ . '/inc/footer.php'; ?>