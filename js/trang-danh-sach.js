/**
 * File: js/trang-danh-sach.js
 */
import { doiTrangThaiYeuThich, kiemTraYeuThich, capNhatSoLuongYeuThich } from './yeu-thich.js';

document.addEventListener('DOMContentLoaded', () => {
    const vungDanhSach = document.querySelector('#than-bang-san-pham') || document.querySelector('.danh-sach-san-pham');
    const formBoLoc = document.querySelector('.bo-loc-san-pham');
    const searchInput = document.querySelector('input[name="q"]');

    if (!vungDanhSach) return;

    // --- 1. XỬ LÝ YÊU THÍCH ---
    const capNhatNutYeuThich = () => {
        const cacNutYeuThich = vungDanhSach.querySelectorAll('[data-yeu-thich]');
        cacNutYeuThich.forEach(nut => {
            const idSanPham = Number(nut.dataset.yeuThich);
            nut.textContent = kiemTraYeuThich(idSanPham) ? '♥' : '♡'; 
        });
    };
    capNhatNutYeuThich();

    vungDanhSach.addEventListener('click', (suKien) => {
        const nutYeuThich = suKien.target.closest('[data-yeu-thich]');
        if (!nutYeuThich) return;
        suKien.preventDefault(); 
        const idSanPham = Number(nutYeuThich.dataset.yeuThich);
        doiTrangThaiYeuThich(idSanPham);
        nutYeuThich.textContent = kiemTraYeuThich(idSanPham) ? '♥' : '♡';
    });
    capNhatSoLuongYeuThich();

    // --- 2. XỬ LÝ TÌM KIẾM & LỌC BẰNG AJAX (KHÔNG LOAD LẠI TRANG) ---
    if (formBoLoc) {
        let timeoutId = null;

        const thucHienTimKiemAjax = (url) => {
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const noiDungMoi = doc.querySelector('#than-bang-san-pham') || doc.querySelector('.danh-sach-san-pham');
                
                if (noiDungMoi) {
                    vungDanhSach.innerHTML = noiDungMoi.innerHTML;
                    capNhatNutYeuThich(); 
                }
            })
            .catch(err => console.error('Lỗi AJAX tìm kiếm:', err));
        };

        // Gõ phím tìm kiếm (debounce 300ms)
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(timeoutId);
                const query = this.value.trim();
                
                timeoutId = setTimeout(() => {
                    const formData = new FormData(formBoLoc);
                    const queryString = new URLSearchParams(formData).toString();
                    const url = `danh-sach.php?${queryString}`;
                    
                    window.history.pushState({}, '', url);
                    thucHienTimKiemAjax(url);
                }, 300);
            });
        }

        // Bấm nút Lọc hoặc Submit form
        formBoLoc.addEventListener('submit', function (e) {
            e.preventDefault(); // CHẶN LOAD LẠI TRANG
            const formData = new FormData(formBoLoc);
            const queryString = new URLSearchParams(formData).toString();
            const url = `danh-sach.php?${queryString}`;
            
            window.history.pushState({}, '', url);
            thucHienTimKiemAjax(url);
        });
    }
});