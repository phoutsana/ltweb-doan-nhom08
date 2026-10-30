<?php
set_exception_handler(function ($e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["loi" => "Lỗi máy chủ"]);
    exit;
});

require_once __DIR__ . '/../inc/config.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Trả về danh sách giỏ hàng hiện tại trong Session
    $gioHang = $_SESSION['gio_hang'] ?? [];
    http_response_code(200);
    echo json_encode(["gio_hang" => $gioHang], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'POST') {
    // Nhận dữ liệu JSON từ JavaScript (fetch)
    $inputData = json_decode(file_get_contents('php://input'), true);
    $sanPhamId = isset($inputData['san_pham_id']) ? (int)$inputData['san_pham_id'] : 0;
    $soLuong = isset($inputData['so_luong']) ? (int)$inputData['so_luong'] : 1;

    if ($sanPhamId <= 0 || $soLuong <= 0) {
        http_response_code(422);
        echo json_encode(["loi" => "Dữ liệu không hợp lệ"]);
        exit;
    }

    try {
        $pdo = \db();
        $stmt = $pdo->prepare("SELECT id, ten, gia, ton_kho, anh FROM san_pham WHERE id = ? LIMIT 1");
        $stmt->execute([$sanPhamId]);
        $sp = $stmt->fetch();

        if (!$sp) {
            http_response_code(404);
            echo json_encode(["loi" => "Sản phẩm không tồn tại"]);
            exit;
        }

        if ($sp['ton_kho'] < $soLuong) {
            http_response_code(422);
            echo json_encode(["loi" => "Số lượng tồn kho không đủ"]);
            exit;
        }

        // Khởi tạo giỏ hàng trong session nếu chưa có
        if (!isset($_SESSION['gio_hang'])) {
            $_SESSION['gio_hang'] = [];
        }

        if (isset($_SESSION['gio_hang'][$sanPhamId])) {
            $_SESSION['gio_hang'][$sanPhamId]['so_luong'] += $soLuong;
        } else {
            $_SESSION['gio_hang'][$sanPhamId] = [
                'id' => $sp['id'],
                'ten' => $sp['ten'],
                'gia' => $sp['gia'],
                'anh' => $sp['anh'],
                'so_luong' => $soLuong
            ];
        }

        // Tính tổng số món trong giỏ để trả về cập nhật header ngay lập tức
        $tongSoMon = array_sum(array_column($_SESSION['gio_hang'], 'so_luong'));

        http_response_code(200);
        echo json_encode(["thanh_cong" => true, "tong_so_mon" => $tongSoMon], JSON_UNESCAPED_UNICODE);

    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(["loi" => "Lỗi máy chủ"]);
    }
    exit;
}

// Nếu dùng phương thức HTTP khác (VD: DELETE, PUT...)
http_response_code(405);
header("Allow: GET, POST");
echo json_encode(["loi" => "Phương thức không được hỗ trợ"]);