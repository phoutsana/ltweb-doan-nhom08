/*
 * Tệp canhan.js tạo các tương tác cho trang cá nhân của Phaeva.
 * Chức năng 1: lọc danh sách kỹ năng theo từng nhóm.
 * Chức năng 2: thu gọn và mở rộng các mục nội dung.
 * Cách thử: nhấn các nút lọc hoặc nhấn tiêu đề mục bằng chuột/bàn phím.
 */

// =====================================================
// CHỨC NĂNG 1: LỌC DANH SÁCH KỸ NĂNG
// =====================================================

const cacNutLoc = document.querySelectorAll(".nut-loc");
const cacKyNang = document.querySelectorAll(".ky-nang-item");
const thongBaoLoc = document.getElementById("thong-bao-loc");

cacNutLoc.forEach(function (nut) {
    nut.addEventListener("click", function () {
        const boLoc = nut.dataset.filter;
        let soLuongHienThi = 0;

        // Cập nhật trạng thái của các nút lọc
        cacNutLoc.forEach(function (nutKhac) {
            nutKhac.classList.remove("active");
            nutKhac.setAttribute("aria-pressed", "false");
        });

        nut.classList.add("active");
        nut.setAttribute("aria-pressed", "true");

        // Lọc danh sách kỹ năng
        cacKyNang.forEach(function (kyNang) {
            const nhomKyNang = kyNang.dataset.category;

            if (boLoc === "tat-ca" || nhomKyNang === boLoc) {
                kyNang.classList.remove("an-ky-nang");
                soLuongHienThi++;
            } else {
                kyNang.classList.add("an-ky-nang");
            }
        });

        // Hiển thị thông báo kết quả bằng textContent
        if (boLoc === "tat-ca") {
            thongBaoLoc.textContent =
                "Đang hiển thị tất cả kỹ năng.";
        } else {
            thongBaoLoc.textContent =
                "Đang hiển thị " + soLuongHienThi + " kỹ năng thuộc nhóm đã chọn.";
        }
    });
});


// =====================================================
// CHỨC NĂNG 2: THU GỌN / MỞ RỘNG NỘI DUNG
// =====================================================

const cacMucThuGon = document.querySelectorAll("[data-thu-gon]");

cacMucThuGon.forEach(function (muc) {
    const tieuDe = muc.querySelector("h2");

    if (!tieuDe) {
        return;
    }

    // Cho phép sử dụng bằng bàn phím
    tieuDe.setAttribute("tabindex", "0");
    tieuDe.setAttribute("role", "button");
    tieuDe.setAttribute("aria-expanded", "true");

    // Click chuột
    tieuDe.addEventListener("click", function () {
        const dangThuGon = muc.classList.toggle("thu-gon");

        tieuDe.setAttribute(
            "aria-expanded",
            String(!dangThuGon)
        );
    });

    // Enter hoặc Space để thao tác bằng bàn phím
    tieuDe.addEventListener("keydown", function (event) {
        if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            tieuDe.click();
        }
    });
});