<?php

namespace App\Data;

use App\Models\SanPham;

/**
 * Lớp truy cập dữ liệu Sản Phẩm từ tệp JSON
 */
class KhoSanPham
{
    private string $tepDuLieu;

    public function __construct()
    {
        $this->tepDuLieu = __DIR__ . '/../../data/san-pham.json';
    }

    /**
     * Lấy toàn bộ sản phẩm từ file JSON
     * @return SanPham[]
     */
    public function tatCa(): array
    {
        // Kiểm tra nếu mất file JSON thì xuất ra trang lỗi 500 giao diện đẹp
        if (!file_exists($this->tepDuLieu) || file_get_contents($this->tepDuLieu) === false) {
            http_response_code(500);
            echo '<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Lỗi hệ thống</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa; /* Màu nền xám nhạt */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        h1 {
            color: #dc2626; /* Màu đỏ */
            font-size: 3rem;
            margin-bottom: 15px;
        }
        p {
            color: #4b5563; /* Màu xám đậm */
            font-size: 1.2rem;
            margin-top: 0;
        }
    </style>
</head>
<body>
    <h1>500 - Lỗi hệ thống</h1>
    <p>Hệ thống đang gặp sự cố. Vui lòng quay lại sau!</p>
</body>
</html>';
            exit; // Dừng toàn bộ chương trình lại tại đây
        }

        $json = file_get_contents($this->tepDuLieu);
        $mangDuLieu = json_decode($json, true);

        if (!is_array($mangDuLieu)) {
            return [];
        }

        $danhSach = [];
        foreach ($mangDuLieu as $d) {
            $danhSach[] = SanPham::tuMang($d);
        }

        return $danhSach;
    }

    /**
     * Tìm một sản phẩm theo ID (dùng cho trang chi-tiet.php)
     */
    public function timTheoId(int $id): ?SanPham
    {
        $danhSach = $this->tatCa();
        foreach ($danhSach as $sp) {
            if ($sp->id === $id) {
                return $sp;
            }
        }
        return null;
    }
}
