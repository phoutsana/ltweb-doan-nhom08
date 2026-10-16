/**
 * File: trang-danh-sach.js
 * Chức năng:
 * - Tải danh sách sản phẩm từ file JSON.
 * - Tìm kiếm sản phẩm không phân biệt dấu.
 * - Lọc theo danh mục.
 * - Sắp xếp theo giá hoặc tên.
 * - Hiển thị trạng thái loading, lỗi và không có kết quả.
 * - Xử lý yêu thích bằng Event Delegation.
 */

import { taiJSON } from './api.js';
import {
    doiTrangThaiYeuThich,
    kiemTraYeuThich,
    capNhatSoLuongYeuThich
} from './yeu-thich.js';

const khungTrangThai = document.querySelector('#trang-thai-tai');
const thanBang = document.querySelector('#than-bang-san-pham');
const oTimKiem = document.querySelector('#tim-kiem');
const oLocDanhMuc = document.querySelector('#loc-danh-muc');
const oSapXep = document.querySelector('#sap-xep');
const boLocSanPham = document.querySelector('#bo-loc-san-pham');

let duLieuGoc = [];

/**
 * Chuyển chuỗi tiếng Việt thành chuỗi không dấu
 * để hỗ trợ tìm kiếm dễ dàng hơn.
 */
function xoaDau(chuoi) {
    return chuoi
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase();
}

/**
 * Hiển thị một sản phẩm vào bảng.
 */
function taoDongSanPham(sp, index) {
    const theTr = document.createElement('tr');

    // STT
    const tdSTT = document.createElement('td');
    tdSTT.textContent = index + 1;

    // Hình ảnh
    const tdAnh = document.createElement('td');

    const img = document.createElement('img');
    img.src = sp.hinhAnh;
    img.alt = sp.ten;
    img.classList.add('anh-san-pham');

    tdAnh.append(img);

    // Tên sản phẩm
    const tdTen = document.createElement('td');
    tdTen.textContent = sp.ten;

    // Danh mục
    const tdLoai = document.createElement('td');
    tdLoai.textContent = sp.danhMuc;

    // Giá
    const tdGia = document.createElement('td');
    tdGia.textContent = `${sp.gia.toLocaleString('vi-VN')} VNĐ`;

    // Cột Chi tiết
    const tdChiTiet = document.createElement('td');

    const nutChiTiet = document.createElement('a');
    nutChiTiet.href = `chi-tiet.html?id=${encodeURIComponent(sp.id)}`;
    nutChiTiet.textContent = 'Xem chi tiết';
    nutChiTiet.classList.add('nut-bam', 'nut-bam--chinh');

    tdChiTiet.append(nutChiTiet);

    // Cột Yêu thích
    const tdYeuThich = document.createElement('td');

    const nutYeuThich = document.createElement('button');
    nutYeuThich.type = 'button';
    nutYeuThich.classList.add('nut-bam');

    nutYeuThich.dataset.yeuThich = String(sp.id);
    nutYeuThich.setAttribute(
        'aria-label',
        `Thêm hoặc bỏ yêu thích sản phẩm ${sp.ten}`
    );

    capNhatNutYeuThich(nutYeuThich, sp.id);

    tdYeuThich.append(nutYeuThich);

    // Thêm tất cả ô vào dòng
    theTr.append(
        tdSTT,
        tdAnh,
        tdTen,
        tdLoai,
        tdGia,
        tdChiTiet,
        tdYeuThich
    );

    return theTr;
}

/**
 * Cập nhật nội dung của nút yêu thích.
 */
function capNhatNutYeuThich(nutYeuThich, idSanPham) {
    const dangYeuThich = kiemTraYeuThich(idSanPham);

    if (dangYeuThich) {
        nutYeuThich.textContent = '♥ Bỏ thích';
    } else {
        nutYeuThich.textContent = '♡ Yêu thích';
    }
}

/**
 * Hiển thị danh sách sản phẩm.
 */
function renderDanhSach(danhSach) {
    thanBang.textContent = '';

    if (danhSach.length === 0) {
        khungTrangThai.textContent =
            'Không tìm thấy sản phẩm nào phù hợp.';
        return;
    }

    khungTrangThai.textContent =
        `Đã tìm thấy ${danhSach.length} sản phẩm.`;

    const fragment = document.createDocumentFragment();

    danhSach.forEach((sp, index) => {
        const dongSanPham = taoDongSanPham(sp, index);
        fragment.append(dongSanPham);
    });

    thanBang.append(fragment);
}

/**
 * Lọc và sắp xếp danh sách sản phẩm.
 */
function xuLyHienThi() {
    const tuKhoa = xoaDau(oTimKiem.value.trim());
    const danhMuc = oLocDanhMuc.value;
    const kieuSapXep = oSapXep.value;

    let ketQua = duLieuGoc.filter((sp) => {
        const tenKhongDau = xoaDau(sp.ten);

        const trungTuKhoa = tenKhongDau.includes(tuKhoa);

        const trungDanhMuc =
            danhMuc === '' || sp.danhMuc === danhMuc;

        return trungTuKhoa && trungDanhMuc;
    });

    if (kieuSapXep === 'gia-tang') {
        ketQua.sort((a, b) => a.gia - b.gia);
    }

    if (kieuSapXep === 'gia-giam') {
        ketQua.sort((a, b) => b.gia - a.gia);
    }

    if (kieuSapXep === 'ten-az') {
        ketQua.sort((a, b) => {
            return a.ten.localeCompare(b.ten, 'vi');
        });
    }

    renderDanhSach(ketQua);
}

/**
 * Xử lý sự kiện click bằng Event Delegation.
 */
function xuLyYeuThich(suKien) {
    const nutYeuThich = suKien.target.closest(
        '[data-yeu-thich]'
    );

    if (!nutYeuThich || !thanBang.contains(nutYeuThich)) {
        return;
    }

    const idSanPham = Number(nutYeuThich.dataset.yeuThich);

    doiTrangThaiYeuThich(idSanPham);

    capNhatNutYeuThich(
        nutYeuThich,
        idSanPham
    );

}

/**
 * Khởi tạo trang danh sách.
 */
async function khoiTao() {
    try {
        khungTrangThai.textContent =
            'Đang tải danh sách sản phẩm...';

        duLieuGoc = await taiJSON(
            'data/san-pham.json'
        );

        if (!Array.isArray(duLieuGoc)) {
            throw new Error(
                'Dữ liệu sản phẩm không phải là một mảng.'
            );
        }

        if (boLocSanPham) {
            boLocSanPham.hidden = false;
        }

        xuLyHienThi();
        capNhatSoLuongYeuThich();
    } catch (loi) {
        khungTrangThai.textContent =
            'Không thể tải danh sách sản phẩm. Vui lòng thử lại sau.';

        console.error(
            'Lỗi tải danh sách sản phẩm:',
            loi
        );
    }
}

/**
 * Sự kiện tìm kiếm.
 */
oTimKiem.addEventListener(
    'input',
    xuLyHienThi
);

/**
 * Sự kiện lọc danh mục.
 */
oLocDanhMuc.addEventListener(
    'change',
    xuLyHienThi
);

/**
 * Sự kiện sắp xếp.
 */
oSapXep.addEventListener(
    'change',
    xuLyHienThi
);

/**
 * Event Delegation cho nút yêu thích.
 */
thanBang.addEventListener(
    'click',
    xuLyYeuThich
);

khoiTao();