document.addEventListener('DOMContentLoaded', () => {

    // --- TƯƠNG TÁC 1: ĐỒNG HỒ ĐẾM NGƯỢC THI CUỐI KỲ ---
    // Đặt ngày thi mục tiêu (Ví dụ: 15/11/2026)
    const targetDate = new Date('2026-11-15T08:00:00').getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const difference = targetDate - now;

        if (difference > 0) {
            const days = Math.floor(difference / (1000 * 60 * 60 * 24));
            const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((difference % (1000 * 60)) / 1000);

            document.getElementById('days').textContent = days < 10 ? '0' + days : days;
            document.getElementById('hours').textContent = hours < 10 ? '0' + hours : hours;
            document.getElementById('minutes').textContent = minutes < 10 ? '0' + minutes : minutes;
            document.getElementById('seconds').textContent = seconds < 10 ? '0' + seconds : seconds;
        } else {
            const timerContainer = document.getElementById('countdown-timer');
            if (timerContainer) {
                timerContainer.textContent = "🎉 Môn học đã hoàn thành!";
            }
        }
    }

    // Chạy đếm ngược mỗi giây
    setInterval(updateCountdown, 1000);
    updateCountdown();


    // --- TƯƠNG TÁC 2: FORM GỬI LỜI NHẮN VÀ VALIDATE ---
    const feedbackForm = document.getElementById('feedback-form');
    const formResponse = document.getElementById('form-response');

    if (feedbackForm) {
        feedbackForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Ngăn load lại trang

            const userName = document.getElementById('user-name').value.trim();
            const userMsg = document.getElementById('user-msg').value.trim();

            if (userName !== "" && userMsg !== "") {
                formResponse.style.display = "block";
                formResponse.style.color = "#15803d";
                formResponse.style.backgroundColor = "#dcfce7";
                formResponse.innerHTML = `Cảm ơn <strong>${userName}</strong> đã gửi lời nhắn! Mạnh sẽ phản hồi sớm nhé.`;

                // Reset form
                feedbackForm.reset();

                // Ẩn thông báo sau 5 giây
                setTimeout(() => {
                    formResponse.style.display = "none";
                }, 5000);
            }
        });
    }
});