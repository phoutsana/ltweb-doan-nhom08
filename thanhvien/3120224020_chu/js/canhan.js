/* ==========================================================
   TƯƠNG TÁC JAVASCRIPT TRANG CÁ NHÂN (MSSV: 3120224020_chu)
   1. Chuyển đổi giao diện Sáng / Tối & ghi nhớ qua localStorage
   2. Nút sao chép Email vào clipboard kèm thông báo trực quan
   ========================================================== */

document.addEventListener("DOMContentLoaded", () => {
    // ----------------------------------------------------
    // TƯƠNG TÁC 1: CHUYỂN ĐỔI CHẾ ĐỘ SÁNG / TỐI (DARK MODE)
    // ----------------------------------------------------
    const themeBtn = document.getElementById("btn-toggle-theme");
    const currentTheme = localStorage.getItem("theme");

    // Khởi tạo theo cấu hình đã lưu
    if (currentTheme === "dark") {
        document.body.classList.add("dark-mode");
        if (themeBtn) themeBtn.textContent = "☀️ Chế độ sáng";
    }

    if (themeBtn) {
        themeBtn.addEventListener("click", () => {
            document.body.classList.toggle("dark-mode");
            const isDark = document.body.classList.contains("dark-mode");

            // Lưu trạng thái vào localStorage
            localStorage.setItem("theme", isDark ? "dark" : "light");
            themeBtn.textContent = isDark ? "☀️ Chế độ sáng" : "🌙 Chế độ tối";
        });
    }

    // ----------------------------------------------------
    // TƯƠNG TÁC 2: SAO CHÉP EMAIL VÀO CLIPBOARD KÈM THÔNG BÁO
    // ----------------------------------------------------
    const copyBtn = document.getElementById("btn-copy-email");
    const emailText = "nchu3808@gmail.com";
    const toast = document.getElementById("copy-toast");

    if (copyBtn) {
        copyBtn.addEventListener("click", async () => {
            try {
                await navigator.clipboard.writeText(emailText);
                
                // Hiển thị thông báo toast
                if (toast) {
                    toast.textContent = `✓ Đã sao chép email: ${emailText}`;
                    toast.classList.add("show");

                    setTimeout(() => {
                        toast.classList.remove("show");
                    }, 2500);
                }
            } catch (err) {
                alert("Không thể sao chép: " + err);
            }
        });
    }
});