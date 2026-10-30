<?php
declare(strict_types=1);

namespace App\Data;

use App\Models\SanPham;
use PDO;

/**
 * Lớp truy cập dữ liệu Sản Phẩm từ cơ sở dữ liệu MySQL (PDO)
 */
class KhoSanPham
{
    /**
     * Lấy toàn bộ sản phẩm từ bảng san_pham
     */
    public function tatCa(): array
    {
        try {
            $pdo = \db(); // Gọi hàm db() toàn cục
            
            $sql = "SELECT p.*, d.ten AS ten_danh_muc 
                    FROM san_pham p 
                    LEFT JOIN danh_muc d ON p.danh_muc_id = d.id 
                    ORDER BY p.id DESC";
            
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();

            $danhSach = [];
            foreach ($rows as $row) {
                $d = [
                    'id'         => (int)$row['id'],
                    'ten'        => $row['ten'],
                    'gia'        => (int)$row['gia'],
                    'soLuong'    => (int)$row['ton_kho'],
                    'danhMuc'    => $row['ten_danh_muc'] ?? 'Thời trang',
                    'moTa'       => $row['mo_ta'] ?? '',
                    'hinhAnh'    => !empty($row['anh']) ? $row['anh'] : 'images/default.jpg',
                    'mauSac'     => $row['mau_sac'] ?? 'Đen, Trắng',
                    'kichThuoc'  => $row['kich_thuoc'] ?? 'M, L, XL',
                    'video'      => $row['video'] ?? ''
                ];
                $danhSach[] = SanPham::tuMang($d);
            }

            return $danhSach;
        } catch (\PDOException $e) {
            http_response_code(500);
            echo 'Lỗi truy vấn cơ sở dữ liệu: ' . htmlspecialchars($e->getMessage());
            exit;
        }
    }

    /**
     * Tìm một sản phẩm theo ID (dùng Prepared Statement)
     */
    public function timTheoId(int $id): ?SanPham
    {
        try {
            $pdo = \db(); // Gọi hàm db() toàn cục
            
            $sql = "SELECT p.*, d.ten AS ten_danh_muc 
                    FROM san_pham p 
                    LEFT JOIN danh_muc d ON p.danh_muc_id = d.id 
                    WHERE p.id = :id LIMIT 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();

            if (!$row) {
                return null;
            }

            $d = [
                'id'         => (int)$row['id'],
                'ten'        => $row['ten'],
                'gia'        => (int)$row['gia'],
                'soLuong'    => (int)$row['ton_kho'],
                'danhMuc'    => $row['ten_danh_muc'] ?? 'Thời trang',
                'moTa'       => $row['mo_ta'] ?? '',
                'hinhAnh'    => !empty($row['anh']) ? $row['anh'] : 'images/default.jpg',
                'mauSac'     => $row['mau_sac'] ?? 'Đen, Trắng',
                'kichThuoc'  => $row['kich_thuoc'] ?? 'M, L, XL',
                'video'      => $row['video'] ?? ''
            ];

            return SanPham::tuMang($d);
        } catch (\PDOException $e) {
            return null;
        }
    }
}