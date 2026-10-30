-- schema.sql: Khởi tạo cơ sở dữ liệu cho Fashion Store
-- Lưu ý: DROP DATABASE sẽ xóa toàn bộ dữ liệu cũ.
DROP DATABASE IF EXISTS ltweb_nhom08;

CREATE DATABASE ltweb_nhom08
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ltweb_nhom08;

-- 1. Nguoi dung
CREATE TABLE nguoi_dung (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    mat_khau VARCHAR(255) NOT NULL,
    ho_ten VARCHAR(100) NOT NULL,
    vai_tro VARCHAR(20) NOT NULL DEFAULT 'khach_hang',
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_nguoi_dung_vai_tro
        CHECK (vai_tro IN ('quan_tri', 'khach_hang'))
) ENGINE=InnoDB;

-- 2. Danh muc
CREATE TABLE danh_muc (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ten VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- 3. San pham (Đã bổ sung thêm các cột mau_sac, kich_thuoc, video)
CREATE TABLE san_pham (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    danh_muc_id INT UNSIGNED NOT NULL,
    ten VARCHAR(200) NOT NULL,
    gia INT UNSIGNED NOT NULL,
    ton_kho INT UNSIGNED NOT NULL DEFAULT 0,
    anh VARCHAR(255),
    mau_sac VARCHAR(100) DEFAULT 'Đen, Trắng',
    kich_thuoc VARCHAR(100) DEFAULT 'M, L, XL',
    video VARCHAR(255) DEFAULT '',
    mo_ta TEXT,
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_san_pham_gia CHECK (gia >= 1000),
    CONSTRAINT fk_san_pham_danh_muc
        FOREIGN KEY (danh_muc_id)
        REFERENCES danh_muc(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_san_pham_danh_muc (danh_muc_id)
) ENGINE=InnoDB;

-- 4. Don hang
CREATE TABLE don_hang (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id INT UNSIGNED NOT NULL,
    tong_tien INT UNSIGNED NOT NULL DEFAULT 0,
    trang_thai VARCHAR(50) NOT NULL DEFAULT 'moi',
    ngay_dat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_don_hang_nguoi_dung
        FOREIGN KEY (nguoi_dung_id)
        REFERENCES nguoi_dung(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_don_hang_nguoi_dung (nguoi_dung_id)
) ENGINE=InnoDB;

-- 5. Chi tiet don hang
CREATE TABLE chi_tiet_don (
    don_hang_id INT UNSIGNED NOT NULL,
    san_pham_id INT UNSIGNED NOT NULL,
    so_luong SMALLINT UNSIGNED NOT NULL,
    don_gia INT UNSIGNED NOT NULL,

    PRIMARY KEY (don_hang_id, san_pham_id),

    CONSTRAINT chk_chi_tiet_so_luong
        CHECK (so_luong > 0),

    CONSTRAINT fk_chi_tiet_don_don_hang
        FOREIGN KEY (don_hang_id)
        REFERENCES don_hang(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_chi_tiet_don_san_pham
        FOREIGN KEY (san_pham_id)
        REFERENCES san_pham(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_chi_tiet_san_pham (san_pham_id)
) ENGINE=InnoDB;

-- 6. Lien he (Đã bổ sung cột sdt và chu_de)
CREATE TABLE lien_he (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    sdt VARCHAR(20) DEFAULT NULL,
    chu_de VARCHAR(100) DEFAULT NULL,
    noi_dung TEXT NOT NULL,
    anh VARCHAR(255),
    ngay_gui DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 7. Bảng thành viên (Phần B)
CREATE TABLE thanh_vien (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ma_sv VARCHAR(50) NOT NULL UNIQUE,
    ho_ten VARCHAR(100) NOT NULL,
    vai_tro_nhom VARCHAR(100),
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 8. Bảng lưu bút cá nhân (Phần B - tv_luu_but)
CREATE TABLE tv_luu_but (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(50) NOT NULL,
    ten VARCHAR(100) NOT NULL,
    loi_nhan TEXT NOT NULL,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_luubut FOREIGN KEY (mssv) REFERENCES thanh_vien(ma_sv) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Bảng dự án cá nhân (Phần B - tv_du_an)
CREATE TABLE tv_du_an (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(50) NOT NULL,
    ten_du_an VARCHAR(200) NOT NULL,
    mo_ta TEXT,
    cong_nghe VARCHAR(100),
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_duan FOREIGN KEY (mssv) REFERENCES thanh_vien(ma_sv) ON DELETE CASCADE
) ENGINE=InnoDB;
-- 10. Bang thoi khoa bieu ca nhan
-- Phan B - Ngo Huong Giang - MSSV: 3120224040
-- Luu thoi khoa bieu de tra cuu va xuat CSV bang MySQL

CREATE TABLE tv_thoi_khoa_bieu (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(50) NOT NULL,
    thu TINYINT UNSIGNED NOT NULL,
    buoi_sang VARCHAR(200) NOT NULL,
    buoi_chieu VARCHAR(200) NOT NULL,

    CONSTRAINT uq_tkb_mssv_thu
        UNIQUE (mssv, thu),

    CONSTRAINT chk_tkb_thu
        CHECK (thu BETWEEN 2 AND 6),

    CONSTRAINT fk_tkb_thanh_vien
        FOREIGN KEY (mssv)
        REFERENCES thanh_vien(ma_sv)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;
-- PHAN B - NGUYEN SON CHU (3120224020): CHI DAN CUOI schema.sql.
-- Khong sua 10 bang chung cua nhom.
CREATE TABLE tv_muc_tieu (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(50) NOT NULL,
    noi_dung VARCHAR(120) NOT NULL,
    hoan_thanh TINYINT(1) NOT NULL DEFAULT 0,
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_chu_muc_tieu_mssv (mssv),
    CONSTRAINT fk_chu_muc_tieu FOREIGN KEY (mssv) REFERENCES thanh_vien(ma_sv) ON DELETE CASCADE,
    CONSTRAINT ck_chu_muc_tieu_dodai CHECK (CHAR_LENGTH(TRIM(noi_dung)) BETWEEN 3 AND 120),
    CONSTRAINT ck_chu_muc_tieu_trangthai CHECK (hoan_thanh IN (0,1))
) ENGINE=InnoDB;

CREATE TABLE tv_cau_hoi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(50) NOT NULL,
    ma_cau VARCHAR(10) NOT NULL,
    noi_dung VARCHAR(255) NOT NULL,
    lua_chon_a VARCHAR(255) NOT NULL,
    lua_chon_b VARCHAR(255) NOT NULL,
    lua_chon_c VARCHAR(255) NOT NULL,
    dap_an CHAR(1) NOT NULL,
    giai_thich TEXT NOT NULL,
    UNIQUE KEY uq_chu_cauhoi (mssv, ma_cau),
    CONSTRAINT fk_chu_cauhoi FOREIGN KEY (mssv) REFERENCES thanh_vien(ma_sv) ON DELETE CASCADE,
    CONSTRAINT ck_chu_cauhoi_dapan CHECK (dap_an IN ('a','b','c'))
) ENGINE=InnoDB;

CREATE TABLE tv_ket_qua_trac_nghiem (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(50) NOT NULL,
    so_cau_dung TINYINT UNSIGNED NOT NULL,
    tong_cau TINYINT UNSIGNED NOT NULL,
    ngay_lam DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_chu_ketqua (mssv, ngay_lam),
    CONSTRAINT fk_chu_ketqua FOREIGN KEY (mssv) REFERENCES thanh_vien(ma_sv) ON DELETE CASCADE,
    CONSTRAINT ck_chu_ketqua CHECK (tong_cau > 0 AND so_cau_dung <= tong_cau)
) ENGINE=InnoDB;
