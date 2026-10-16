/*
 * Tệp: canhan.js - Tương tác trang cá nhân của Ngô Hương Giang.
 * Chức năng 1: Tăng/giảm cỡ chữ toàn trang (dùng classList trên thẻ <html>, có ghi nhớ bằng localStorage).
 * Chức năng 2: Lời chào tự động đổi theo thời gian thực (dùng textContent + setInterval).
 * Cách thử 1: Bấm "Phóng to" / "Thu nhỏ" để đổi cỡ chữ toàn trang, tải lại trang vẫn giữ lựa chọn.
 * Cách thử 2: Quan sát hộp lời chào: đồng hồ chạy từng giây, thông điệp đổi theo từng buổi trong ngày.
 */

document.addEventListener('DOMContentLoaded', () => {
    // =====================================================
    // CHỨC NĂNG 1: TĂNG / GIẢM CỠ CHỮ TOÀN TRANG
    // =====================================================
    const increaseBtn = document.getElementById('increase-font');
    const decreaseBtn = document.getElementById('decrease-font');
    const resetBtn = document.getElementById('reset-font');       // tùy chọn
    const levelLabel = document.getElementById('font-size-label'); // tùy chọn
    const rootEl = document.documentElement; // đổi ở <html> để mọi giá trị rem co giãn theo

    // Các mức cỡ chữ: cls là class gắn vào <html> (định nghĩa trong style-canhan.css)
    const FONT_LEVELS = [
        { cls: 'font-small', label: 'Nhỏ' },
        { cls: '', label: 'Vừa' },
        { cls: 'font-large', label: 'Lớn' },
        { cls: 'font-xlarge', label: 'Rất lớn' }
    ];
    const DEFAULT_LEVEL = 1;
    const STORAGE_KEY = 'giang_font_level';

    // Đọc mức đã lưu (nếu hợp lệ), nếu không dùng mặc định
    let level = parseInt(localStorage.getItem(STORAGE_KEY), 10);
    if (isNaN(level) || level < 0 || level >= FONT_LEVELS.length) {
        level = DEFAULT_LEVEL;
    }

    function applyFontLevel() {
        // Gỡ tất cả class cỡ chữ cũ rồi thêm class của mức hiện tại
        FONT_LEVELS.forEach((item) => {
            if (item.cls) rootEl.classList.remove(item.cls);
        });
        if (FONT_LEVELS[level].cls) {
            rootEl.classList.add(FONT_LEVELS[level].cls);
        }

        if (levelLabel) levelLabel.textContent = FONT_LEVELS[level].label;

        // Khóa nút khi chạm giới hạn
        if (increaseBtn) increaseBtn.disabled = level === FONT_LEVELS.length - 1;
        if (decreaseBtn) decreaseBtn.disabled = level === 0;

        localStorage.setItem(STORAGE_KEY, String(level));
    }

    if (increaseBtn) {
        increaseBtn.addEventListener('click', () => {
            if (level < FONT_LEVELS.length - 1) {
                level++;
                applyFontLevel();
            }
        });
    }

    if (decreaseBtn) {
        decreaseBtn.addEventListener('click', () => {
            if (level > 0) {
                level--;
                applyFontLevel();
            }
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            level = DEFAULT_LEVEL;
            applyFontLevel();
        });
    }

    applyFontLevel(); // áp dụng ngay khi tải trang

    // =====================================================
    // CHỨC NĂNG 2: LỜI CHÀO TỰ ĐỘNG THEO THỜI GIAN THỰC
    // =====================================================
    const greetingEl = document.getElementById('realtime-greeting');
    const clockEl = document.getElementById('realtime-clock'); // tùy chọn
    const dateEl = document.getElementById('realtime-date');   // tùy chọn

    function getGreeting(hour) {
        if (hour >= 5 && hour < 11) {
            return '☀️ Chào buổi sáng! Chúc bạn một ngày học tập thật hiệu quả.';
        }
        if (hour >= 11 && hour < 14) {
            return '🌤️ Chào buổi trưa! Đừng quên nghỉ ngơi và ăn uống đầy đủ nhé.';
        }
        if (hour >= 14 && hour < 18) {
            return '☕ Chào buổi chiều! Cùng khám phá trang cá nhân của Giang nhé.';
        }
        if (hour >= 18 && hour < 22) {
            return '🌙 Chào buổi tối! Chúc bạn có những phút giây thư giãn tuyệt vời.';
        }
        return '😴 Khuya rồi, bạn nhớ nghỉ ngơi để giữ sức khỏe nhé!';
    }

    function pad(n) {
        return n < 10 ? '0' + n : String(n);
    }

    function updateGreeting() {
        const now = new Date();

        if (greetingEl) {
            greetingEl.textContent = getGreeting(now.getHours());
        }
        if (clockEl) {
            clockEl.textContent =
                pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        }
        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString('vi-VN', {
                weekday: 'long', day: '2-digit', month: '2-digit', year: 'numeric'
            });
        }
    }

    if (greetingEl) {
        updateGreeting();                 // chạy ngay lần đầu
        setInterval(updateGreeting, 1000); // cập nhật mỗi giây -> "thời gian thực"
    }
});