/* ==============================================================================
 * TỆP: js/canhan.js - Tương tác trang cá nhân của Nguyễn Sơn Chu (MSSV: 3120224020)
 * CHỨC NĂNG: 
 * 1. Chuyển đổi giao diện Sáng/Tối lưu vào localStorage;
 * 2. Sao chép email kèm thông báo Toast;
 * 3. Tự động đếm và lưu số lượt ghé thăm trang qua localStorage kèm nút đặt lại.
 * CÁCH THỬ:
 * 1. Bấm nút "🌙 Chế độ tối" để đổi màu giao diện, tải lại trang (F5) kiểm tra ghi nhớ.
 * 2. Bấm nút "Sao chép" cạnh email -> thông báo xanh hiện 2.5s -> dán (Ctrl+V) kiểm tra.
 * 3. F5 trang nhiều lần để thấy số lượt ghé thăm tăng dần; bấm "Đặt lại" để đưa về 1.
 * ============================================================================== */

document.addEventListener("DOMContentLoaded", () => {
    // ----------------------------------------------------
    // TƯƠNG TÁC 1: CHUYỂN ĐỔI CHẾ ĐỘ SÁNG / TỐI (DARK MODE)
    // ----------------------------------------------------
    const themeBtn = document.getElementById("btn-toggle-theme");
    const THEME_STORAGE_KEY = "theme_3120224020";
    const currentTheme = localStorage.getItem(THEME_STORAGE_KEY);

    // Khởi tạo theo cấu hình đã lưu trong localStorage
    if (currentTheme === "dark") {
        document.body.classList.add("dark-mode");
        if (themeBtn) {
            themeBtn.textContent = "☀️ Chế độ sáng";
        }
    }

    if (themeBtn) {
        themeBtn.addEventListener("click", () => {
            document.body.classList.toggle("dark-mode");
            const isDark = document.body.classList.contains("dark-mode");

            // Cập nhật giao diện nút và lưu trữ vào localStorage
            themeBtn.textContent = isDark ? "☀️ Chế độ sáng" : "🌙 Chế độ tối";
            localStorage.setItem(THEME_STORAGE_KEY, isDark ? "dark" : "light");
        });
    }

    // ----------------------------------------------------
    // TƯƠNG TÁC 2: SAO CHÉP EMAIL VÀO CLIPBOARD KÈM THÔNG BÁO TOAST
    // ----------------------------------------------------
    const copyBtn = document.getElementById("btn-copy-email");
    const toast = document.getElementById("copy-toast");
    const emailText = "nchu3808@gmail.com";
    let toastTimer = null; // Tránh lỗi ẩn sớm khi click nhiều lần

    if (copyBtn) {
        copyBtn.addEventListener("click", async () => {
            try {
                await navigator.clipboard.writeText(emailText);

                if (toast) {
                    toast.textContent = `✓ Đã sao chép email: ${emailText}`;
                    toast.classList.add("show");

                    // Xóa hẹn giờ cũ nếu người dùng nhấn liên tục
                    if (toastTimer) {
                        clearTimeout(toastTimer);
                    }

                    toastTimer = setTimeout(() => {
                        toast.classList.remove("show");
                    }, 2500);
                }
            } catch (err) {
                console.error("Lỗi khi sao chép clipboard:", err);
            }
        });
    }

    // ----------------------------------------------------
    // TƯƠNG TÁC 3: BỘ ĐẾM SỐ LƯỢT GHÉ THĂM TRANG & NÚT ĐẶT LẠI
    // ----------------------------------------------------
    const counterEl = document.getElementById("so-luot-xem");
    const resetBtn = document.getElementById("btn-reset-counter");
    const VIEW_STORAGE_KEY = "views_3120224020";

    // 3.1. Tự động tính toán và lưu số lượt xem khi vào trang
    let currentViews = parseInt(localStorage.getItem(VIEW_STORAGE_KEY), 10);

    if (isNaN(currentViews) || currentViews < 0) {
        currentViews = 1;
    } else {
        currentViews += 1;
    }

    localStorage.setItem(VIEW_STORAGE_KEY, currentViews.toString());

    if (counterEl) {
        counterEl.textContent = currentViews.toString();
    }

    // 3.2. Xử lý sự kiện đặt lại bộ đếm về 1
    if (resetBtn) {
        resetBtn.addEventListener("click", () => {
            const confirmReset = window.confirm("Bạn có chắc chắn muốn đặt lại số lượt xem trang về 1?");
            if (confirmReset) {
                localStorage.setItem(VIEW_STORAGE_KEY, "1");
                if (counterEl) {
                    counterEl.textContent = "1";
                }
            }
        });
    }
});