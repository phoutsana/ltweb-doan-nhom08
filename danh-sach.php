<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/config.php';

$pdo = \db();

// --- XỬ LÝ LẤY DANH MỤC TỪ CSDL ĐỂ HIỂN THỊ ĐỘNG ---
$stmtDm = $pdo->query("SELECT id, ten FROM danh_muc");
$danhMucList = $stmtDm->fetchAll();

// --- NHẬN THAM SỐ TỪ BIỂU MẪU GET ---
$qNhap = $_GET['q'] ?? '';
$tuKhoa = is_string($qNhap) ? trim($qNhap) : '';
$danhMucId = filter_var($_GET['dm'] ?? '', FILTER_VALIDATE_INT);
$giaToiDa = filter_var($_GET['gia_toi_da'] ?? '', FILTER_VALIDATE_INT);
$sapXep = trim($_GET['sx'] ?? '');
$trangHienTai = filter_var($_GET['trang'] ?? 1, FILTER_VALIDATE_INT);
$trangHienTai = ($trangHienTai > 0) ? $trangHienTai : 1;
$soLuongMoiTrang = 7; // Số sản phẩm trên mỗi trang

// --- XÂY DỰNG CÂU TRUY VẤN ĐỘNG (DÙNG PREPARED STATEMENT CHỐNG SQL INJECTION) ---
$dieuKien = [];
$thamSo = [];

// 1. Điều kiện tìm kiếm theo từ khóa
if ($tuKhoa !== '') {
    $dieuKien[] = "ten LIKE ?";
    $thamSo[] = '%' . addcslashes($tuKhoa, '%_\\') . '%';
}

// 2. Điều kiện lọc theo danh mục (Dùng id chuẩn theo bảng danh_muc)
if ($danhMucId !== false && $danhMucId > 0) {
    $dieuKien[] = "danh_muc_id = ?";
    $thamSo[] = $danhMucId;
}

// 3. Điều kiện lọc theo mức giá tối đa
if ($giaToiDa !== false && $giaToiDa > 0) {
    $dieuKien[] = "gia <= ?";
    $thamSo[] = $giaToiDa;
}

$sqlWhere = '';
if (!empty($dieuKien)) {
    $sqlWhere = " WHERE " . implode(" AND ", $dieuKien);
}

// 4. Đếm tổng số lượng kết quả (Phục vụ phân trang)
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM san_pham" . $sqlWhere);
$stmtCount->execute($thamSo);
$tongSoDong = $stmtCount->fetchColumn();
$tongSoTrang = (int)ceil($tongSoDong / $soLuongMoiTrang);
if ($trangHienTai > $tongSoTrang && $tongSoTrang > 0) {
    $trangHienTai = $tongSoTrang;
}

// Đã khắc phục lỗi dòng offset bị lỗi ký tự
$offset = ($trangHienTai - 1) * $soLuongMoiTrang;

// 5. Xử lý sắp xếp (Chỉ cho phép các cột an toàn theo yêu cầu đề bài)
$cotSapXepChoPhieu = [
    'gia-tang' => 'gia ASC',
    'gia-giam' => 'gia DESC',
    'ten-az'   => 'ten ASC'
];
$sqlOrderBy = " ORDER BY id DESC"; // Mặc định mới nhất
if (array_key_exists($sapXep, $cotSapXepChoPhieu)) {
    $sqlOrderBy = " ORDER BY " . $cotSapXepChoPhieu[$sapXep];
}

// 6. Truy vấn danh sách sản phẩm chính thức có phân trang (LIMIT, OFFSET)
$sql = "SELECT id, danh_muc_id, ten, gia, ton_kho, anh FROM san_pham" . $sqlWhere . $sqlOrderBy . " LIMIT " . (int)$soLuongMoiTrang . " OFFSET " . (int)$offset;
$stmt = $pdo->prepare($sql);
$stmt->execute($thamSo);
$danhSach = $stmt->fetchAll();

$tieuDe = 'Danh sách sản phẩm - Fashion Store';
$trang = 'danh-sach';
$cssRieng = 'css/danh-sach-bt7-csp.css';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" data-bt7-ds="ds001">

    <!-- Khối bộ lọc (Đã bổ sung bộ lọc Giá tối đa theo yêu cầu Bảng 1) -->
    <form method="GET" action="danh-sach.php" class="khoi-noi-dung bo-loc-san-pham" data-bt7-ds="ds002">

        <div data-bt7-ds="ds003">
            <label for="tim-kiem" data-bt7-ds="ds004">Tìm kiếm</label>
            <input type="text" id="tim-kiem" name="q" value="<?= e($tuKhoa) ?>" placeholder="Tên sản phẩm..." data-bt7-ds="ds005">
        </div>

        <div data-bt7-ds="ds006">
            <label for="loc-danh-muc" data-bt7-ds="ds007">Danh mục</label>
            <select id="loc-danh-muc" name="dm" data-bt7-ds="ds008">
                <option value="">Tất cả</option>
                <?php foreach ($danhMucList as $dm): ?>
                    <option value="<?= $dm['id'] ?>" <?= ($danhMucId === (int)$dm['id']) ? 'selected' : '' ?>><?= e($dm['ten']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Ô lọc giá tối đa mới thêm -->
        <div data-bt7-ds="ds009">
            <label for="gia-toi-da" data-bt7-ds="ds010">Giá tối đa (VNĐ)</label>
            <input type="number" id="gia-toi-da" name="gia_toi_da" value="<?= ($giaToiDa !== false && $giaToiDa > 0) ? $giaToiDa : '' ?>" placeholder="VD: 500000" data-bt7-ds="ds011">
        </div>

        <div data-bt7-ds="ds012">
            <label for="sap-xep" data-bt7-ds="ds013">Sắp xếp</label>
            <select id="sap-xep" name="sx" data-bt7-ds="ds014">
                <option value="">Mặc định</option>
                <option value="gia-tang" <?= $sapXep === 'gia-tang' ? 'selected' : '' ?>>Giá tăng dần</option>
                <option value="gia-giam" <?= $sapXep === 'gia-giam' ? 'selected' : '' ?>>Giá giảm dần</option>
                <option value="ten-az" <?= $sapXep === 'ten-az' ? 'selected' : '' ?>>Tên A-Z</option>
            </select>
        </div>

        <button type="submit" data-bt7-ds="ds015">Lọc</button>
    </form>

    <!-- Khu vực hiển thị sản phẩm -->
    <section id="than-bang-san-pham">
        <?php if (empty($danhSach)): ?>
            <div data-bt7-ds="ds016">
                <p data-bt7-ds="ds017">Không có sản phẩm nào phù hợp với tìm kiếm của bạn.</p>
                <a href="danh-sach.php" data-bt7-ds="ds018">Xem tất cả sản phẩm</a>
            </div>
        <?php else: ?>
            <div data-bt7-ds="ds019">
                <?php foreach ($danhSach as $sp): ?>
                    <div data-bt7-ds="ds020">

                        <!-- Nút Yêu Thích -->
                        <button type="button" data-yeu-thich="<?= e($sp['id']) ?>" data-bt7-ds="ds021">
                            ♡
                        </button>

                        <!-- Ảnh sản phẩm -->
                        <div data-bt7-ds="ds022">
                            <img src="<?= e($sp['anh']) ?>" alt="<?= e($sp['ten']) ?>" loading="lazy" data-bt7-ds="ds023">
                        </div>

                        <!-- Thông tin sản phẩm -->
                        <div data-bt7-ds="ds024">
                            <div>
                                <h2 data-bt7-ds="ds025">
                                    <?= e($sp['ten']) ?>
                                </h2>
                                <p data-bt7-ds="ds026">
                                    <?= number_format($sp['gia'], 0, ',', '.') ?> đ
                                </p>
                            </div>

                            <a href="chi-tiet.php?id=<?= e($sp['id']) ?>" data-bt7-ds="ds027">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- PHÂN TRANG (Giữ nguyên tham số bộ lọc khi chuyển trang) -->
            <?php if ($tongSoTrang > 1): ?>
                <div data-bt7-ds="ds028">
                    <?php for ($i = 1; $i <= $tongSoTrang; $i++): ?>
                        <?php
                            $params = $_GET;
                            $params['trang'] = $i;
                            $urlTrang = 'danh-sach.php?' . http_build_query($params);
                        ?>
                        <a href="<?= e($urlTrang) ?>" class="<?= ($i === $trangHienTai) ? 'bt7-page-active' : 'bt7-page-inactive' ?>" data-bt7-ds="ds029">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </section>
</main>
<script type="module" src="js/trang-danh-sach.js"></script>
<?php
require_once __DIR__ . '/inc/footer.php';
?>