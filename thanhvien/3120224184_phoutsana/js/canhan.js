/**
 * File: canhan.js - tương tác cho trang cá nhân của Phoutsana.
 * Gồm: đổi giao diện sáng/tối (lưu bằng localStorage), sao chép email, menu thu gọn, tô sáng mục đang xem.
 * Cách thử: bấm "Chế độ tối" rồi tải lại trang; bấm "Sao chép email" rồi dán thử;
 * thu cửa sổ dưới 768px để dùng nút ☰ Menu (nhấn Esc để đóng).
 */

const nutGiaoDien = document.querySelector('#nut-giao-dien');
const nutSaoChepEmail = document.querySelector('#nut-sao-chep-email');
const thongBao = document.querySelector('#thong-bao-tuong-tac');
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
        return null; // trình duyệt chặn lưu trữ thì dùng giao diện sáng
    }
}

function ghiLuaChon(giaTri) {
    try {
        localStorage.setItem(KHOA_GIAO_DIEN, giaTri);
    } catch (loi) {
        // bỏ qua, chỉ là không nhớ được lựa chọn
    }
}

function datGiaoDien(toi) {
    document.body.classList.toggle('che-do-toi', toi);
    if (nutGiaoDien) {
        nutGiaoDien.textContent = toi ? '☀️ Chế độ sáng' : '🌙 Chế độ tối';
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

/* ===== 2. Sao chép email ===== */
function hienThongBao(noiDung) {
    if (thongBao) {
        thongBao.textContent = noiDung;
    }
}

if (nutSaoChepEmail) {
    nutSaoChepEmail.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(nutSaoChepEmail.dataset.email);
            hienThongBao('Đã sao chép email vào bộ nhớ tạm.');
        } catch (loi) {
            hienThongBao('Không thể sao chép email. Vui lòng thử lại.');
        }
    });
}

/* ===== 3. Menu thu gọn trên điện thoại ===== */
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
    thanhDieuHuong.classList.add('co-js'); // báo cho CSS biết JS đang chạy
    capNhatMenuTheoManHinh();
    manHinhNho.addEventListener('change', capNhatMenuTheoManHinh);

    nutMenu.addEventListener('click', () => {
        if (danhSachMenu.classList.contains('an-menu-mobile')) {
            moMenu();
        } else {
            dongMenu();
        }
    });

    document.addEventListener('keydown', (suKien) => {
        const dangMo = !danhSachMenu.classList.contains('an-menu-mobile');
        if (suKien.key === 'Escape' && manHinhNho.matches && dangMo) {
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

/* ===== 4. Tô sáng mục đang xem ===== */
if ('IntersectionObserver' in window && cacLienKet.length > 0) {
    const quanSat = new IntersectionObserver((cacMuc) => {
        cacMuc.forEach((muc) => {
            if (!muc.isIntersecting) {
                return;
            }
            cacLienKet.forEach((lienKet) => {
                const dangXem = lienKet.getAttribute('href') === '#' + muc.target.id;
                lienKet.classList.toggle('dang-xem', dangXem);
                if (dangXem) {
                    lienKet.setAttribute('aria-current', 'true');
                } else {
                    lienKet.removeAttribute('aria-current');
                }
            });
        });
    }, { rootMargin: '-40% 0px -55% 0px' });

    cacLienKet.forEach((lienKet) => {
        const muc = document.querySelector(lienKet.getAttribute('href'));
        if (muc) {
            quanSat.observe(muc);
        }
    });
}