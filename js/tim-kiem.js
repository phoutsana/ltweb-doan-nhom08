document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('input[name="q"]');
    const formBoLoc = document.querySelector('form'); // Bắt trực tiếp thẻ form trên trang
    const vungDanhSach = document.querySelector('#than-bang-san-pham');

    if (!searchInput) return;

    let timeoutId = null;
    let resultsContainer = document.createElement('div');
    resultsContainer.className = 'ket-qua-tim-kiem-nhanh';
    resultsContainer.style.cssText = 'position: absolute; background: white; border: 1px solid #d1d5db; width: 100%; max-height: 300px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
    searchInput.parentNode.style.position = 'relative';
    searchInput.parentNode.appendChild(resultsContainer);

    // 1. Gợi ý tìm kiếm nhanh khi gõ phím (Autocomplete)
    searchInput.addEventListener('input', function () {
        clearTimeout(timeoutId);
        const query = this.value.trim();

        if (query.length === 0) {
            resultsContainer.innerHTML = '';
            return;
        }

        timeoutId = setTimeout(() => {
            fetch(`api/san-pham.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    if (data.length === 0) {
                        let div = document.createElement('div');
                        div.style.padding = '10px';
                        div.textContent = 'Không tìm thấy sản phẩm phù hợp.';
                        resultsContainer.appendChild(div);
                        return;
                    }

                    data.forEach(item => {
                        let a = document.createElement('a');
                        a.href = `chi-tiet.php?id=${item.id}`;
                        a.style.cssText = 'display: flex; align-items: center; padding: 8px 12px; text-decoration: none; color: #333; border-bottom: 1px solid #f3f4f6;';
                        
                        let span = document.createElement('span');
                        span.textContent = `${item.ten} - ${Number(item.gia).toLocaleString()} đ`;
                        a.appendChild(span);

                        resultsContainer.appendChild(a);
                    });
                })
                .catch(err => console.error('Lỗi tìm kiếm API:', err));
        }, 300);
    });

    // 2. Chặn load lại trang khi bấm Lọc hoặc Submit form (Đáp ứng Ca kiểm thử 13)
    if (formBoLoc && vungDanhSach) {
        formBoLoc.addEventListener('submit', function (e) {
            e.preventDefault(); // CHẶN HOÀN TOÀN VIỆC RELOAD TRANG
            resultsContainer.innerHTML = '';

            const formData = new FormData(formBoLoc);
            const queryString = new URLSearchParams(formData).toString();
            const url = `danh-sach.php?${queryString}`;

            // Cập nhật URL trên thanh địa chỉ không cần load lại
            window.history.pushState({}, '', url);

            // Gọi fetch ngầm để lấy kết quả HTML mới
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const noiDungMoi = doc.querySelector('#than-bang-san-pham');
                
                if (noiDungMoi) {
                    vungDanhSach.innerHTML = noiDungMoi.innerHTML;
                }
            })
            .catch(err => console.error('Lỗi AJAX lọc sản phẩm:', err));
        });
    }

    // Ẩn bảng gợi ý khi click ra ngoài
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.innerHTML = '';
        }
    });
});