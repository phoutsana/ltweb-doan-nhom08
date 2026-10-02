/**
 * File: canhan.js - tương tác cho trang cá nhân của Phoutsana.
 * Gồm: đổi giao diện sáng/tối (lưu bằng localStorage) và menu thu gọn trên điện thoại.
 * Cách thử: bấm "Chế độ tối" rồi tải lại trang; thu cửa sổ dưới 768px để dùng nút ☰ Menu.
 * Nhấn Esc để đóng menu trên điện thoại.
 */

const nutGiaoDien = document.querySelector('#nut-giao-dien');
const thanhDieuHuong = document.querySelector('.dieu-huong');
const nutMenu = document.querySelector('#nut-menu-mobile');
const danhSachMenu = document.querySelector('#danh-sach-menu');
const cacLienKet = document.querySelectorAll('.dieu-huong__lien-ket');

const KHOA_GIAO_DIEN = 'cheDoGiaoDien_Phoutsana';
const manHinhNho = window.matchMedia('(max-width: 767px)');

/* ===== 1. Đổi giao diện sáng/tối ===== */

function docLuaChon() {
    try {
        return localStorage.getItem(KHOA_GIAO_DIEN);
    } catch (loi) {
        return null;
    }
}

function ghiLuaChon(giaTri) {
    try {
        localStorage.setItem(KHOA_GIAO_DIEN, giaTri);
    } catch (loi) {
        // Không làm gì nếu trình duyệt không cho phép lưu localStorage.
    }
}

function datGiaoDien(toi) {
    document.body.classList.toggle('che-do-toi', toi);

    if (nutGiaoDien) {
        nutGiaoDien.textContent =
            toi ? '☀️ Chế độ sáng' : '🌙 Chế độ tối';
    }
}

datGiaoDien(docLuaChon() === 'toi');

if (nutGiaoDien) {
    nutGiaoDien.addEventListener('click', () => {
        const toi = !document.body.classList.contains('che-do-toi');

        datGiaoDien(toi);
        ghiLuaChon(toi ? 'toi' : 'sang');
    });
}


/* ===== 2. Menu thu gọn trên điện thoại ===== */

function dongMenu() {
    danhSachMenu.classList.add('an-menu-mobile');
    nutMenu.setAttribute('aria-expanded', 'false');
    nutMenu.textContent = '☰ Menu';
}

function moMenu() {
    danhSachMenu.classList.remove('an-menu-mobile');
    nutMenu.setAttribute('aria-expanded', 'true');
    nutMenu.textContent = '✖ Đóng';
}

function capNhatMenuTheoManHinh() {
    if (manHinhNho.matches) {
        dongMenu();
    } else {
        danhSachMenu.classList.remove('an-menu-mobile');
    }
}

if (thanhDieuHuong && nutMenu && danhSachMenu) {
    thanhDieuHuong.classList.add('co-js');

    capNhatMenuTheoManHinh();

    manHinhNho.addEventListener(
        'change',
        capNhatMenuTheoManHinh
    );

    nutMenu.addEventListener('click', () => {
        if (danhSachMenu.classList.contains('an-menu-mobile')) {
            moMenu();
        } else {
            dongMenu();
        }
    });

    document.addEventListener('keydown', (suKien) => {
        const dangMo =
            !danhSachMenu.classList.contains('an-menu-mobile');

        if (
            suKien.key === 'Escape' &&
            manHinhNho.matches &&
            dangMo
        ) {
            dongMenu();
            nutMenu.focus();
        }
    });

    cacLienKet.forEach((lienKet) => {
        lienKet.addEventListener('click', () => {
            if (manHinhNho.matches) {
                dongMenu();
            }
        });
    });
}