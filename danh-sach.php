<?php
require_once __DIR__ . '/inc/config.php';

$pdo = \db();

// --- XỬ LÝ LẤY DANH MỤC TỪ CSDL ĐỂ HIỂN THỊ ĐỘNG ---
$stmtDm = $pdo->query("SELECT id, ten FROM danh_muc");
$danhMucList = $stmtDm->fetchAll();

// --- NHẬN THAM SỐ TỪ BIỂU MẪU GET ---
$tuKhoa = trim($_GET['q'] ?? '');
$danhMucId = filter_var($_GET['dm'] ?? '', FILTER_VALIDATE_INT);
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

$sqlWhere = '';
if (!empty($dieuKien)) {
    $sqlWhere = " WHERE " . implode(" AND ", $dieuKien);
}

// 3. Đếm tổng số lượng kết quả (Phục vụ phân trang)
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM san_pham" . $sqlWhere);
$stmtCount->execute($thamSo);
$tongSoDong = $stmtCount->fetchColumn();
$tongSoTrang = ceil($tongSoDong / $soLuongMoiTrang);
if ($trangHienTai > $tongSoTrang && $tongSoTrang > 0) {
    $trangHienTai = $tongSoTrang;
}
$offset = ($trangHienTai - 1) * $soLuongMoiTrang;

// 4. Xử lý sắp xếp (Chỉ cho phép các cột an toàn theo yêu cầu đề bài)
$cotSapXepChoPhieu = [
    'gia-tang' => 'gia ASC',
    'gia-giam' => 'gia DESC',
    'ten-az'   => 'ten ASC'
];
$sqlOrderBy = " ORDER BY id DESC"; // Mặc định mới nhất
if (array_key_exists($sapXep, $cotSapXepChoPhieu)) {
    $sqlOrderBy = " ORDER BY " . $cotSapXepChoPhieu[$sapXep];
}

// 5. Truy vấn danh sách sản phẩm chính thức có phân trang (LIMIT, OFFSET)
$sql = "SELECT id, danh_muc_id, ten, gia, ton_kho, anh FROM san_pham" . $sqlWhere . $sqlOrderBy . " LIMIT " . (int)$soLuongMoiTrang . " OFFSET " . (int)$offset;
$stmt = $pdo->prepare($sql);
$stmt->execute($thamSo);
$danhSach = $stmt->fetchAll();

$tieuDe = 'Danh sách sản phẩm - Fashion Store';
$trang = 'danh-sach';
require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh" style="max-width: 1200px; margin: 0 auto; padding: 20px;">

    <!-- Khối bộ lọc -->
    <form method="GET" action="danh-sach.php" class="khoi-noi-dung bo-loc-san-pham" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 30px; display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">

        <div style="display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 200px;">
            <label for="tim-kiem" style="font-weight: bold; color: #374151;">Tìm kiếm</label>
            <input type="text" id="tim-kiem" name="q" value="<?= e($tuKhoa) ?>" placeholder="Tên sản phẩm..." style="padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
        </div>

        <div style="display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 150px;">
            <label for="loc-danh-muc" style="font-weight: bold; color: #374151;">Danh mục</label>
            <select id="loc-danh-muc" name="dm" style="padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; background: white;">
                <option value="">Tất cả</option>
                <?php foreach ($danhMucList as $dm): ?>
                    <option value="<?= $dm['id'] ?>" <?= ($danhMucId === (int)$dm['id']) ? 'selected' : '' ?>><?= e($dm['ten']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 150px;">
            <label for="sap-xep" style="font-weight: bold; color: #374151;">Sắp xếp</label>
            <select id="sap-xep" name="sx" style="padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; background: white;">
                <option value="">Mặc định</option>
                <option value="gia-tang" <?= $sapXep === 'gia-tang' ? 'selected' : '' ?>>Giá tăng dần</option>
                <option value="gia-giam" <?= $sapXep === 'gia-giam' ? 'selected' : '' ?>>Giá giảm dần</option>
                <option value="ten-az" <?= $sapXep === 'ten-az' ? 'selected' : '' ?>>Tên A-Z</option>
            </select>
        </div>

        <button type="submit" style="padding: 10px 25px; background-color: #2563eb; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; height: 42px;">Lọc</button>
    </form>

    <!-- Khu vực hiển thị sản phẩm -->
    <section id="than-bang-san-pham">
        <?php if (empty($danhSach)): ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <p style="font-weight: bold; color: #dc2626; font-size: 1.2rem;">Không có sản phẩm nào phù hợp với tìm kiếm của bạn.</p>
                <a href="danh-sach.php" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px;">Xem tất cả sản phẩm</a>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px;">
                <?php foreach ($danhSach as $sp): ?>
                    <div style="position: relative; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column;">
                        
                        <!-- Nút Yêu Thích -->
                        <button type="button" data-yeu-thich="<?= e($sp['id']) ?>" style="position: absolute; top: 10px; right: 10px; z-index: 10; background: white; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.15); color: #dc2626; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">
                            ♡
                        </button>

                        <!-- Ảnh sản phẩm -->
                        <div style="width: 100%; height: 250px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <img src="<?= e($sp['anh']) ?>" alt="<?= e($sp['ten']) ?>" loading="lazy" style="max-width: 100%; max-height: 100%; object-fit: contain; padding: 10px;">
                        </div>

                        <!-- Thông tin sản phẩm -->
                        <div style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <h2 style="font-size: 1.1rem; color: #111827; margin: 0 0 10px 0; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= e($sp['ten']) ?>
                                </h2>
                                <p style="color: #dc2626; font-size: 1.25rem; font-weight: 700; margin: 0 0 15px 0;">
                                    <?= number_format($sp['gia'], 0, ',', '.') ?> đ
                                </p>
                            </div>

                            <a href="chi-tiet.php?id=<?= e($sp['id']) ?>" style="display: block; text-align: center; width: 100%; padding: 10px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- PHÂN TRANG (Giữ nguyên tham số bộ lọc khi chuyển trang) -->
            <?php if ($tongSoTrang > 1): ?>
                <div style="display: flex; justify-content: center; gap: 8px; margin-top: 40px;">
                    <?php for ($i = 1; $i <= $tongSoTrang; $i++): ?>
                        <?php 
                            $params = $_GET;
                            $params['trang'] = $i;
                            $urlTrang = 'danh-sach.php?' . http_build_query($params);
                        ?>
                        <a href="<?= e($urlTrang) ?>" style="padding: 8px 14px; border-radius: 4px; text-decoration: none; font-weight: bold; background: <?= ($i === $trangHienTai) ? '#2563eb' : '#e5e7eb' ?>; color: <?= ($i === $trangHienTai) ? 'white' : '#374151' ?>;">
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