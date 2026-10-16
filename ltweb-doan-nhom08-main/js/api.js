/**
 * File: api.js
 * Chức năng: Cung cấp hàm tải dữ liệu JSON dùng chung cho toàn website.
 * Xử lý fetch, kiểm tra lỗi mạng và trả về dữ liệu.
 */

export async function taiJSON(url) {
    const res = await fetch(url);

    if (!res.ok) {
        throw new Error(`HTTP lỗi: ${res.status} khi tải ${url}`);
    }

    return res.json();
}