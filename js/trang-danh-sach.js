/**
 * File: trang-danh-sach.js
 */
import { doiTrangThaiYeuThich, kiemTraYeuThich, capNhatSoLuongYeuThich } from './yeu-thich.js';

document.addEventListener('DOMContentLoaded', () => {
    const vungDanhSach = document.querySelector('#than-bang-san-pham') || document.querySelector('.danh-sach-san-pham');

    if (!vungDanhSach) return;

    // 1. Cập nhật biểu tượng khi vừa load trang
    const cacNutYeuThich = vungDanhSach.querySelectorAll('[data-yeu-thich]');
    cacNutYeuThich.forEach(nut => {
        const idSanPham = Number(nut.dataset.yeuThich);
        // Chỉ hiện trái tim thay vì chữ dài để nút tròn không bị méo
        nut.textContent = kiemTraYeuThich(idSanPham) ? '♥' : '♡'; 
    });

    // 2. Xử lý sự kiện click
    vungDanhSach.addEventListener('click', (suKien) => {
        const nutYeuThich = suKien.target.closest('[data-yeu-thich]');
        if (!nutYeuThich) return;

        // Ngăn trình duyệt load lại trang hoặc nhảy link
        suKien.preventDefault(); 

        const idSanPham = Number(nutYeuThich.dataset.yeuThich);
        doiTrangThaiYeuThich(idSanPham);
        
        // Đổi trạng thái trái tim ngay lập tức
        nutYeuThich.textContent = kiemTraYeuThich(idSanPham) ? '♥' : '♡';
    });

    capNhatSoLuongYeuThich();
});