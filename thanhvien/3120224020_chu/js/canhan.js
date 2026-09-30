/* ==============================================================================
 * TỆP: js/canhan.js - Tương tác trang cá nhân của Nguyễn Sơn Chu (MSSV: 3120224020)
 * CHỨC NĂNG: Chuyển đổi giao diện Sáng/Tối lưu vào localStorage; Sao chép email kèm Toast.
 * CÁCH THỬ:
 * 1. Bấm nút "🌙 Chế độ tối" để đổi màu giao diện, sau đó tải lại trang (F5) để kiểm tra ghi nhớ.
 * 2. Bấm nút "Sao chép" cạnh email -> hộp thông báo xanh hiện 2.5s -> dán (Ctrl+V) để kiểm tra.
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
});