/**
 * Tệp JavaScript cá nhân: thanhvien/3120224094_manh/js/canhan.js
 * Tác giả: Lê Văn Mạnh - MSSV: 3120224094
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. ĐỔI GIAO DIỆN SÁNG / TỐI
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const currentTheme = localStorage.getItem('theme');

    if (currentTheme === 'dark') {
        document.body.classList.add('dark-theme');
        if (themeToggleBtn) themeToggleBtn.textContent = '☀️ Chế độ Sáng';
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            let theme = 'light';
            
            if (document.body.classList.contains('dark-theme')) {
                theme = 'dark';
                themeToggleBtn.textContent = '☀️ Chế độ Sáng';
            } else {
                themeToggleBtn.textContent = '🌙 Chế độ Tối';
            }
            localStorage.setItem('theme', theme);
        });
    }

    // 2. SAO CHÉP EMAIL VÀO CLIPBOARD
    const copyEmailBtn = document.getElementById('copy-email-btn');
    const userEmail = 'manh.levan.3120224094@example.com'; 
    const toastMessage = document.getElementById('toast-msg');

    if (copyEmailBtn) {
        copyEmailBtn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(userEmail);
                if (toastMessage) {
                    toastMessage.textContent = 'Đã sao chép email của Lê Văn Mạnh!';
                    toastMessage.classList.add('show');
                    setTimeout(() => {
                        toastMessage.classList.remove('show');
                    }, 2500);
                }
            } catch (err) {
                console.error('Lỗi khi sao chép email:', err);
            }
        });
    }
});