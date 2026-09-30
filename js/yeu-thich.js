/**
 * File: yeu-thich.js
 * Chức năng: Đọc/ghi danh sách yêu thích vào localStorage, cập nhật số đếm trên Header.
 */

const KHOA_LUU_TRU = 'danhSachYeuThich_FashionStore';

export function layDanhSachYeuThich() {
    const duLieu = localStorage.getItem(KHOA_LUU_TRU);
    if (!duLieu) return [];
    try {
        const danhSach = JSON.parse(duLieu);
        return Array.isArray(danhSach) ? danhSach : [];
    } catch (loi) {
        return [];
    }
}

function luuDanhSachYeuThich(danhSach) {
    localStorage.setItem(KHOA_LUU_TRU, JSON.stringify(danhSach));
}

export function kiemTraYeuThich(id) {
    const danhSach = layDanhSachYeuThich();
    return danhSach.includes(id);
}

export function doiTrangThaiYeuThich(id) {
    let danhSach = layDanhSachYeuThich();
    if (danhSach.includes(id)) {
        danhSach = danhSach.filter(item => item !== id);
    } else {
        danhSach.push(id);
    }
    luuDanhSachYeuThich(danhSach);
    capNhatSoLuongYeuThich();
}

export function capNhatSoLuongYeuThich() {
    const danhSach = layDanhSachYeuThich();
    const theHienThi = document.querySelectorAll('.so-luong-yeu-thich');
    theHienThi.forEach(the => {
        the.textContent = String(danhSach.length);
    });
}