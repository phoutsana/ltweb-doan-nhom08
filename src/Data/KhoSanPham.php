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
        // Trỏ đường dẫn ngược ra ngoài thư mục gốc rồi vào data/san-pham.json
        $this->tepDuLieu = __DIR__ . '/../../data/san-pham.json';
    }

    /**
     * Lấy toàn bộ sản phẩm từ file JSON
     * @return SanPham[]
     */
    public function tatCa(): array
    {
        if (!file_exists($this->tepDuLieu)) {
            return [];
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