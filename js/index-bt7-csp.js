document.addEventListener('DOMContentLoaded', async () => {
    const trangThai = document.getElementById('trang-thai-thoi-tiet');
    const nhietDo = document.getElementById('nhiet-do');

    if (!trangThai || !nhietDo) return;

    try {
        const url = 'https://api.open-meteo.com/v1/forecast?latitude=16.0678&longitude=108.2208&current_weather=true';

        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();
        const temperature = data.current_weather?.temperature;

        if (typeof temperature !== 'number') {
            throw new Error('Invalid weather data');
        }

        trangThai.textContent = 'Dữ liệu thời tiết đã được tải thành công!';
        trangThai.classList.add('bt7-weather-ok');

        nhietDo.textContent = temperature + ' °C';
        nhietDo.classList.add('bt7-weather-value');
    } catch (error) {
        trangThai.textContent = 'Không tải được dữ liệu thời tiết.';
        trangThai.classList.add('bt7-weather-error');
    }
});