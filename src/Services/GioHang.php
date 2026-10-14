<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\KhoSanPham;
use InvalidArgumentException;

final class GioHang
{
    private const SESSION_KEY = 'gio';

    private KhoSanPham $khoSanPham;

    public function __construct(?KhoSanPham $khoSanPham = null)
    {
        $this->khoSanPham = $khoSanPham ?? new KhoSanPham();

        // Tự động start session nếu chưa có thay vì ném lỗi để code chạy mượt hơn
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            !isset($_SESSION[self::SESSION_KEY])
            || !is_array($_SESSION[self::SESSION_KEY])
        ) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    /**
     * Lấy danh sách ID sản phẩm và số lượng trong giỏ.
     *
     * @return array<string, int>
     */
    public function tatCa(): array
    {
        $gioHang = $_SESSION[self::SESSION_KEY] ?? [];

        if (!is_array($gioHang)) {
            return [];
        }

        $danhSachId = [];

        foreach ($this->khoSanPham->tatCa() as $sanPham) {
            // Sửa lỗi: dùng thuộc tính trực tiếp ->id thay vì hàm ->getId()
            $danhSachId[(string) $sanPham->id] = true;
        }

        $ketQua = [];

        foreach ($gioHang as $id => $soLuong) {
            $id = (string) $id;

            if (
                isset($danhSachId[$id])
                && is_numeric($soLuong)
                && (int) $soLuong >= 1
                && (int) $soLuong <= 99
            ) {
                $ketQua[$id] = (int) $soLuong;
            }
        }

        return $ketQua;
    }

    /**
     * Thêm sản phẩm hoặc tăng số lượng.
     */
    public function them(string|int $id, int $soLuong = 1): void
    {
        $id = trim((string) $id);

        if ($id === '' || $soLuong < 1 || $soLuong > 99) {
            throw new InvalidArgumentException('Sản phẩm hoặc số lượng không hợp lệ.');
        }

        $this->kiemTraSanPhamTonTai($id);

        $gioHang = $this->tatCa();
        $soLuongMoi = ($gioHang[$id] ?? 0) + $soLuong;

        if ($soLuongMoi > 99) {
            throw new InvalidArgumentException('Số lượng tối đa cho mỗi sản phẩm là 99.');
        }

        $gioHang[$id] = $soLuongMoi;
        $_SESSION[self::SESSION_KEY] = $gioHang;
    }

    /**
     * Cập nhật số lượng.
     */
    public function capNhat(string|int $id, int $soLuong): void
    {
        $id = trim((string) $id);

        if ($id === '') {
            throw new InvalidArgumentException('ID sản phẩm không hợp lệ.');
        }

        if ($soLuong < 0 || $soLuong > 99) {
            throw new InvalidArgumentException('Số lượng phải nằm trong khoảng từ 0 đến 99.');
        }

        $this->kiemTraSanPhamTonTai($id);
        $gioHang = $this->tatCa();

        if ($soLuong === 0) {
            unset($gioHang[$id]);
        } else {
            $gioHang[$id] = $soLuong;
        }

        $_SESSION[self::SESSION_KEY] = $gioHang;
    }

    /**
     * Xóa một sản phẩm khỏi giỏ.
     */
    public function xoa(string|int $id): void
    {
        $id = trim((string) $id);
        $gioHang = $this->tatCa();
        unset($gioHang[$id]);
        $_SESSION[self::SESSION_KEY] = $gioHang;
    }

    /**
     * Xóa toàn bộ giỏ hàng.
     */
    public function lamTrong(): void
    {
        $_SESSION[self::SESSION_KEY] = [];
    }

    /**
     * Đếm tổng số lượng sản phẩm.
     */
    public function tongSoLuong(): int
    {
        return array_sum($this->tatCa());
    }

    /**
     * Lấy danh sách chi tiết sản phẩm trong giỏ.
     *
     * @return list<array{sanPham: \App\Models\SanPham, soLuong: int, thanhTien: float|int}>
     */
    public function chiTiet(): array
    {
        $danhSachSanPham = [];

        foreach ($this->khoSanPham->tatCa() as $sanPham) {
            // Sửa lỗi: dùng ->id
            $danhSachSanPham[(string) $sanPham->id] = $sanPham;
        }

        $ketQua = [];

        foreach ($this->tatCa() as $id => $soLuong) {
            if (!isset($danhSachSanPham[$id])) {
                continue;
            }

            $sanPham = $danhSachSanPham[$id];

            $ketQua[] = [
                'sanPham' => $sanPham,
                'soLuong' => $soLuong,
                // Sửa lỗi: dùng ->gia thay vì ->getGia()
                'thanhTien' => $sanPham->gia * $soLuong,
            ];
        }

        return $ketQua;
    }

    /**
     * Tính tổng tiền.
     */
    public function tongTien(): float|int
    {
        $tongTien = 0;

        foreach ($this->chiTiet() as $item) {
            $tongTien += $item['thanhTien'];
        }

        return $tongTien;
    }

    /**
     * Kiểm tra ID sản phẩm.
     */
    private function kiemTraSanPhamTonTai(string $id): void
    {
        foreach ($this->khoSanPham->tatCa() as $sanPham) {
            if ((string) $sanPham->id === $id) {
                return;
            }
        }

        throw new InvalidArgumentException('Sản phẩm không tồn tại.');
    }
}