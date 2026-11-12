<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

// BT7: Chi quan tri vien duoc sua san pham.
if (
    !is_array($_SESSION['user'] ?? null) ||
    ($_SESSION['user']['vai_tro'] ?? null) !== 'quan_tri'
) {
    http_response_code(403);
    exit('Ban khong co quyen truy cap trang nay.');
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (!in_array($method, ['GET', 'POST'], true)) {
    header('Allow: GET, POST');
    http_response_code(405);
    exit('405 Method Not Allowed');
}

// POST phai co CSRF token hop le.
$isPost = ($method === 'POST');

if ($isPost) {
    csrf_verify();
}

// Kiem tra ma san pham.
$id = filter_var(
    $_GET['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($id === false || $id === null) {
    http_response_code(400);
    exit('Ma san pham khong hop le.');
}

$pdo = db();

// Lay thong tin san pham.
$stmt = $pdo->prepare(
    'SELECT id, danh_muc_id, ten, gia, ton_kho
     FROM san_pham
     WHERE id = ?'
);
$stmt->execute([$id]);
$sanPham = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sanPham) {
    http_response_code(404);
    exit('San pham khong ton tai.');
}

// Lay danh sach danh muc.
$stmtDm = $pdo->query(
    'SELECT id, ten FROM danh_muc ORDER BY id ASC'
);
$danhMucList = $stmtDm->fetchAll(PDO::FETCH_ASSOC);

$loi = [];

// Doc du lieu chuoi an toan.
$getText = static function (string $key): string {
    $value = $_POST[$key] ?? null;
    return is_string($value) ? trim($value) : '';
};

// Giu lai du lieu da nhap neu form co loi.
$displayValue = static function (
    string $key,
    mixed $fallback
) use ($isPost): string {
    $value = $isPost ? ($_POST[$key] ?? null) : $fallback;
    return is_scalar($value) ? (string)$value : '';
};

$selectedDanhMuc = (int)$sanPham['danh_muc_id'];

if ($isPost) {
    $ten = $getText('ten');

    $gia = filter_var(
        $getText('gia'),
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    $tonKho = filter_var(
        $getText('ton_kho'),
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 0]]
    );

    $danhMucId = filter_var(
        $getText('danh_muc_id'),
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($ten === '') {
        $loi['ten'] = 'Tên sản phẩm không được để trống.';
    } elseif (
        (function_exists('mb_strlen')
            ? mb_strlen($ten, 'UTF-8')
            : strlen($ten)) > 255
    ) {
        $loi['ten'] = 'Tên sản phẩm quá dài.';
    }

    if ($gia === false) {
        $loi['gia'] = 'Giá phải là số nguyên lớn hơn 0.';
    }

    if ($tonKho === false) {
        $loi['ton_kho'] = 'Tồn kho phải là số nguyên không âm.';
    }

    if ($danhMucId === false) {
        $loi['danh_muc_id'] = 'Danh mục không hợp lệ.';
    } else {
        $stmtCheckDm = $pdo->prepare(
            'SELECT COUNT(*) FROM danh_muc WHERE id = ?'
        );

        $stmtCheckDm->execute([$danhMucId]);

        if ((int)$stmtCheckDm->fetchColumn() === 0) {
            $loi['danh_muc_id'] = 'Danh mục không tồn tại.';
        } else {
            $selectedDanhMuc = $danhMucId;
        }
    }

    // Chi UPDATE khi tat ca du lieu hop le.
    if (empty($loi)) {
        try {
            $stmtUpdate = $pdo->prepare(
                'UPDATE san_pham
                 SET danh_muc_id = ?,
                     ten = ?,
                     gia = ?,
                     ton_kho = ?
                 WHERE id = ?'
            );

            $stmtUpdate->execute([
                $danhMucId,
                $ten,
                $gia,
                $tonKho,
                $id
            ]);

            header(
                'Location: quan-tri-san-pham.php?tb=thanh_cong',
                true,
                303
            );
            exit;

        } catch (PDOException $e) {
            error_log(
                '[BT7_SUA_SAN_PHAM] ' . $e->getMessage()
            );

            $loi['chung'] =
                'Không thể cập nhật sản phẩm. Vui lòng thử lại.';
        }
    }
}

$tieuDe = 'Sửa sản phẩm - Fashion Store';
$cssRieng = 'css/sua-san-pham-bt7.css';

require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chinh bt7-sua-sp">
    <h2>Sửa sản phẩm #<?= (int)$id ?></h2>

    <?php if (isset($loi['chung'])): ?>
        <div class="bt7-sua-sp__error" role="alert">
            <?= e($loi['chung']) ?>
        </div>
    <?php endif; ?>

    <form
        method="post"
        action="sua-san-pham.php?id=<?= (int)$id ?>"
        class="bt7-sua-sp__form"
    >
        <?= csrf_field() ?>

        <div class="bt7-sua-sp__field">
            <label for="sp-ten">Tên sản phẩm *</label>

            <input
                id="sp-ten"
                type="text"
                name="ten"
                maxlength="255"
                required
                value="<?= e($displayValue('ten', $sanPham['ten'])) ?>"
            >

            <?php if (isset($loi['ten'])): ?>
                <span class="bt7-sua-sp__error" role="alert">
                    <?= e($loi['ten']) ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="bt7-sua-sp__field">
            <label for="sp-danh-muc">Danh mục *</label>

            <select
                id="sp-danh-muc"
                name="danh_muc_id"
                required
            >
                <?php foreach ($danhMucList as $dm): ?>
                    <option
                        value="<?= (int)$dm['id'] ?>"
                        <?= $selectedDanhMuc === (int)$dm['id']
                            ? 'selected'
                            : '' ?>
                    >
                        <?= e((string)$dm['ten']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (isset($loi['danh_muc_id'])): ?>
                <span class="bt7-sua-sp__error" role="alert">
                    <?= e($loi['danh_muc_id']) ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="bt7-sua-sp__row">
            <div class="bt7-sua-sp__field">
                <label for="sp-gia">Giá (VNĐ) *</label>

                <input
                    id="sp-gia"
                    type="number"
                    name="gia"
                    min="1"
                    step="1"
                    required
                    value="<?= e($displayValue('gia', $sanPham['gia'])) ?>"
                >

                <?php if (isset($loi['gia'])): ?>
                    <span class="bt7-sua-sp__error" role="alert">
                        <?= e($loi['gia']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="bt7-sua-sp__field">
                <label for="sp-ton-kho">Tồn kho *</label>

                <input
                    id="sp-ton-kho"
                    type="number"
                    name="ton_kho"
                    min="0"
                    step="1"
                    required
                    value="<?= e($displayValue('ton_kho', $sanPham['ton_kho'])) ?>"
                >

                <?php if (isset($loi['ton_kho'])): ?>
                    <span class="bt7-sua-sp__error" role="alert">
                        <?= e($loi['ton_kho']) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="bt7-sua-sp__actions">
            <a
                href="quan-tri-san-pham.php"
                class="bt7-sua-sp__back"
            >
                Quay lại
            </a>

            <button
                type="submit"
                class="bt7-sua-sp__submit"
            >
                Cập nhật
            </button>
        </div>
    </form>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
