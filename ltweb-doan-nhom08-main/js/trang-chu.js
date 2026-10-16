/**
 * File: trang-chu.js
 * Chức năng: Gọi API thời tiết Đà Nẵng từ Open-Meteo và hiển thị.
 */

import { taiJSON } from './api.js';

const trangThai = document.querySelector('#trang-thai-thoi-tiet');
const nhietDo = document.querySelector('#nhiet-do');

const thamSo = new URLSearchParams({
    latitude: '16.05', 
    longitude: '108.2', 
    current: 'temperature_2m', 
    timezone: 'auto'
});

async function taiThoiTiet() {
    try {
        trangThai.textContent = 'Đang tải dữ liệu thời tiết...';
        
        const duLieu = await taiJSON('https://api.open-meteo.com/v1/forecast?' + thamSo);
        
        if (!duLieu.current || typeof duLieu.current.temperature_2m !== 'number') {
            throw new Error('Dữ liệu thời tiết trả về bị lỗi.');
        }

        nhietDo.textContent = `Nhiệt độ hiện tại: ${duLieu.current.temperature_2m} °C`;
        trangThai.textContent = 'Đã cập nhật dữ liệu thời tiết.';

    } catch (loi) {
        trangThai.textContent = 'Không tải được thông tin thời tiết. Vui lòng thử lại sau.';
        console.error(loi);
    }
}

taiThoiTiet();