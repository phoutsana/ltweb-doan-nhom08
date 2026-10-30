/**
 * File: trang-chi-tiet.js
 * Chức năng: Xử lý sự kiện cho nút Yêu thích trên trang chi tiết (dữ liệu sản phẩm đã được PHP render từ MySQL).
 */
import { doiTrangThaiYeuThich, kiemTraYeuThich } from './yeu-thich.js';

document.addEventListener('DOMContentLoaded', () => {
    // Lấy ID sản phẩm từ URL
    const id = Number(new URLSearchParams(window.location.search).get('id'));

    if (!id) return;

    // Xử lý nút Yêu thích
    const nutYeuThich = document.querySelector('#nut-yeu-thich-ct');
    if (nutYeuThich) {
        // Khởi tạo trạng thái ban đầu
        nutYeuThich.textContent = kiemTraYeuThich(id) ? '♥ Bỏ thích' : '♡ Yêu thích';

        // Sự kiện khi click
        nutYeuThich.addEventListener('click', () => {
            doiTrangThaiYeuThich(id);
            nutYeuThich.textContent = kiemTraYeuThich(id) ? '♥ Bỏ thích' : '♡ Yêu thích';
        });
    }
});