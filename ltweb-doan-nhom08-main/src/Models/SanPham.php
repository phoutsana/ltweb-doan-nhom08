<?php

namespace App\Models;

/**
 * Lớp thực thể Sản Phẩm
 */
class SanPham
{
    public function __construct(
        public readonly int $id,
        public readonly string $ten,
        public readonly int $gia,
        public readonly int $soLuong,
        public readonly string $danhMuc,
        public readonly string $moTa,
        public readonly string $hinhAnh,
        public readonly string $mauSac,
        public readonly string $kichThuoc,
        public readonly string $video
    ) {}

    // ========================================================
    // BỔ SUNG CÁC HÀM GETTER ĐỂ TƯƠNG THÍCH VỚI CODE CỦA THẦY
    // ========================================================
    public function getId(): int
    {
        return $this->id;
    }
    public function getTen(): string
    {
        return $this->ten;
    }
    public function getGia(): int
    {
        return $this->gia;
    }
    public function getSoLuong(): int
    {
        return $this->soLuong;
    }
    public function getDanhMuc(): string
    {
        return $this->danhMuc;
    }
    public function getMoTa(): string
    {
        return $this->moTa;
    }
    public function getHinhAnh(): string
    {
        return $this->hinhAnh;
    }
    public function getMauSac(): string
    {
        return $this->mauSac;
    }
    public function getKichThuoc(): string
    {
        return $this->kichThuoc;
    }
    public function getVideo(): string
    {
        return $this->video;
    }

    /**
     * Tạo đối tượng SanPham từ một mảng (đọc từ JSON)
     */
    public static function tuMang(array $d): self
    {
        return new self(
            $d['id'],
            $d['ten'],
            $d['gia'],
            $d['soLuong'],
            $d['danhMuc'],
            $d['moTa'],
            $d['hinhAnh'],
            $d['mauSac'],
            $d['kichThuoc'],
            $d['video'] ?? ''
        );
    }
}
