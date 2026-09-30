import { taiJSON } from './api.js';
import { doiTrangThaiYeuThich, kiemTraYeuThich } from './yeu-thich.js';

const khungTrangThai = document.querySelector('#trang-thai-chi-tiet');
const khungNoiDung = document.querySelector('#noi-dung-chi-tiet');
const theSection = document.querySelector('#khung-chi-tiet');

function anCacSanPhamTinh() {
    const cacSanPhamTinh = document.querySelectorAll('main.noi-dung-chinh > article:not(#khung-chi-tiet)');
    cacSanPhamTinh.forEach(sp => {
        sp.style.display = 'none';
    });
}

async function taiChiTiet() {
    const id = Number(new URLSearchParams(location.search).get('id'));

    if (!id) return; // Không có ID thì giữ nguyên giao diện cũ

    anCacSanPhamTinh();
    theSection.style.display = 'block';
    khungTrangThai.textContent = 'Đang tải thông tin sản phẩm...';

    try {
        const danhSach = await taiJSON('data/san-pham.json');
        const sanPham = danhSach.find((sp) => sp.id === id);

        if (!sanPham) {
            khungTrangThai.textContent = 'Không tìm thấy sản phẩm.';
            return;
        }

        document.title = `${sanPham.ten} – Fashion Store`;
        document.querySelector('#ct-ten').textContent = sanPham.ten;

        const anh = document.querySelector('#ct-anh');
        anh.src = sanPham.hinhAnh;
        anh.alt = sanPham.ten;

        document.querySelector('#ct-figcaption').textContent = sanPham.ten;
        document.querySelector('#ct-mo-ta').textContent = sanPham.moTa;
        document.querySelector('#ct-caption-bang').textContent = `Thông số ${sanPham.ten}`;
        document.querySelector('#ct-danh-muc').textContent = sanPham.danhMuc;
        document.querySelector('#ct-chat-lieu').textContent = sanPham.chatLieu || 'N/A';
        document.querySelector('#ct-mau-sac').textContent = sanPham.mauSac || 'N/A';
        document.querySelector('#ct-kich-thuoc').textContent = sanPham.kichThuoc || 'N/A';
        document.querySelector('#ct-gia').textContent = `${sanPham.gia.toLocaleString('vi-VN')} VNĐ`;
        const videoSanPham = {
            1: 'https://www.youtube.com/embed/Q4T5uVfQ03w',
            2: 'https://www.youtube.com/embed/Hf_dMGhF7zM',
            3: 'https://www.youtube.com/embed/yUdXs2msI_s',
            4: 'https://www.youtube.com/embed/7IO8IB0DCvY',
            5: 'https://www.youtube.com/embed/CRd_dkot4BM?si=XNjcglVwoKsfcnN4',
            6: 'https://www.youtube.com/embed/1at4WHmWhzA?si=oY-qAgdjuLnLYGux',
            7: 'https://www.youtube.com/embed/ABYuNdnU7Jk?si=v4j2jiIHc-M6HOrG',
            8: 'https://www.youtube.com/embed/U08_Wu7wPn8?si=imH9DASAIDKMcKSP',
            9: 'https://www.youtube.com/embed/C8373DrkI0w?si=BDVWzNU5Fe57z0c3',
            10: 'https://www.youtube.com/embed/g1aicLHOiks?si=qB_2YpZ0JTyFlWNv',
            11: 'https://www.youtube.com/embed/lwOYguJR95Y?si=acGZD6ajZKHAKmp4',
            12: 'https://www.youtube.com/embed/Mpii6eYHSqk?si=Mkt3-09k-iAwNHdU'
        };

        const video = document.querySelector('#ct-video');

        if (video && videoSanPham[id]) {
            video.src = videoSanPham[id];
        } else if (video) {
            video.remove();
        }
        const nutYeuThich = document.querySelector('#nut-yeu-thich-ct');
        nutYeuThich.textContent = kiemTraYeuThich(id) ? '♥ Bỏ thích' : '♡ Yêu thích';

        nutYeuThich.addEventListener('click', () => {
            doiTrangThaiYeuThich(id);
            nutYeuThich.textContent = kiemTraYeuThich(id) ? '♥ Bỏ thích' : '♡ Yêu thích';
        });

        khungTrangThai.style.display = 'none';
        khungNoiDung.style.display = 'block';

    } catch (loi) {
        khungTrangThai.textContent = 'Không tải được dữ liệu, vui lòng thử lại.';
        console.error(loi);
    }
}

taiChiTiet();