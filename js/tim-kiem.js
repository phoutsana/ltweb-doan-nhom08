document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('input[name="q"]');
    if (!searchInput) return;

    let timeoutId = null;
    let resultsContainer = document.createElement('div');
    resultsContainer.className = 'ket-qua-tim-kiem-nhanh';
    resultsContainer.style.cssText = 'position: absolute; background: white; border: 1px solid #d1d5db; width: 100%; max-height: 300px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
    searchInput.parentNode.style.position = 'relative';
    searchInput.parentNode.appendChild(resultsContainer);

    searchInput.addEventListener('input', function () {
        clearTimeout(timeoutId);
        const query = this.value.trim();

        if (query.length === 0) {
            resultsContainer.innerHTML = '';
            return;
        }

        // Debounce chống gọi liên tục
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

                        a.addEventListener('mouseenter', () => a.style.background = '#f9fafb');
                        a.addEventListener('mouseleave', () => a.style.background = 'white');

                        resultsContainer.appendChild(a);
                    });
                })
                .catch(err => console.error('Lỗi tìm kiếm API:', err));
        }, 300);
    });

    // Ẩn kết quả khi click ra ngoài
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.innerHTML = '';
        }
    });
});