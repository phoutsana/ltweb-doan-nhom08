<?php
declare(strict_types=1);

set_exception_handler(function ($e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["loi" => "Lỗi máy chủ"], JSON_UNESCAPED_UNICODE);
    exit;
});

require_once __DIR__ . '/../inc/config.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

// 1. Xử lý phương thức GET: Lấy thông tin giỏ hàng
if ($method === 'GET') {
    $gioHang = $_SESSION['gio'] ?? [];
    http_response_code(200);
    echo json_encode(["gio_hang" => $gioHang], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. Xử lý phương thức POST: Thêm sản phẩm vào giỏ hàng
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    
    // Kiểm tra thân request có phải là JSON hợp lệ hay không -> Bắt buộc trả về 400 nếu sai
    $inputData = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($inputData)) {
        http_response_code(400);
        echo json_encode(["loi" => "Dữ liệu JSON không hợp lệ"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Nhận diện linh hoạt khóa ID và số lượng từ client gửi lên
    $sanPhamId = isset($inputData['san_pham_id']) ? (int)$inputData['san_pham_id'] : (isset($inputData['id']) ? (int)$inputData['id'] : 0);
    $soLuong = isset($inputData['so_luong']) ? (int)$inputData['so_luong'] : (isset($inputData['sl']) ? (int)$inputData['sl'] : 1);

    // Kiểm tra ID hoặc số lượng âm/bằng 0 -> Trả về 422
    if ($sanPhamId <= 0 || $soLuong <= 0) {
        http_response_code(422);
        echo json_encode(["loi" => "Dữ liệu không hợp lệ hoặc số lượng âm"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $pdo = \db();
        $stmt = $pdo->prepare("SELECT id, ten, gia, ton_kho, anh FROM san_pham WHERE id = ? LIMIT 1");
        $stmt->execute([$sanPhamId]);
        $sp = $stmt->fetch();

        // Kiểm tra sản phẩm không tồn tại trong CSDL -> Trả về 404
        if (!$sp) {
            http_response_code(404);
            echo json_encode(["loi" => "Sản phẩm không tồn tại"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Tính toán số lượng hiện có trong giỏ hàng để kiểm tra tồn kho
        $soLuongDangCoTrongGio = 0;
        if (isset($_SESSION['gio'][$sanPhamId])) {
            if (is_array($_SESSION['gio'][$sanPhamId])) {
                $soLuongDangCoTrongGio = (int)($_SESSION['gio'][$sanPhamId]['so_luong'] ?? 0);
            } else {
                $soLuongDangCoTrongGio = (int)$_SESSION['gio'][$sanPhamId];
            }
        }
        $tongSoLuongYeuCau = $soLuongDangCoTrongGio + $soLuong;

        // Kiểm tra vượt quá tồn kho thực tế -> Trả về 422
        if ((int)$sp['ton_kho'] < $tongSoLuongYeuCau) {
            http_response_code(422);
            echo json_encode(["loi" => "Số lượng tồn kho không đủ"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!isset($_SESSION['gio']) || !is_array($_SESSION['gio'])) {
            $_SESSION['gio'] = [];
        }

        // Cập nhật giỏ hàng trong session khi hợp lệ hoàn toàn
        if (isset($_SESSION['gio'][$sanPhamId])) {
            if (is_array($_SESSION['gio'][$sanPhamId])) {
                $_SESSION['gio'][$sanPhamId]['so_luong'] = $tongSoLuongYeuCau;
            } else {
                $_SESSION['gio'][$sanPhamId] = $tongSoLuongYeuCau;
            }
        } else {
            $_SESSION['gio'][$sanPhamId] = [
                'id' => $sp['id'],
                'ten' => $sp['ten'],
                'gia' => $sp['gia'],
                'anh' => $sp['anh'],
                'so_luong' => $soLuong
            ];
        }

        $tongSoMon = 0;
        foreach ($_SESSION['gio'] as $item) {
            $tongSoMon += is_array($item) ? ($item['so_luong'] ?? 0) : (int)$item;
        }

        http_response_code(200);
        echo json_encode(["thanh_cong" => true, "tong_so_mon" => $tongSoMon], JSON_UNESCAPED_UNICODE);

    } catch (\Exception $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode(["loi" => "Lỗi máy chủ"], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// 3. Xử lý các phương thức không được phép (VD: DELETE, PUT...) -> Trả về 405 kèm header Allow
http_response_code(405);
header("Allow: GET, POST");
echo json_encode(["loi" => "Phương thức không được phép"], JSON_UNESCAPED_UNICODE);