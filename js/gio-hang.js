document.addEventListener('DOMContentLoaded', function () {
    const addToCartForms = document.querySelectorAll('.form-them-gio-hang');

    addToCartForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Chặn form load lại trang

            const formData = new FormData(this);
            const sanPhamId = formData.get('san_pham_id');
            const soLuong = formData.get('so_luong') || 1;

            fetch('api/gio-hang.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    san_pham_id: parseInt(sanPhamId),
                    so_luong: parseInt(soLuong)
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Lỗi thêm vào giỏ hàng');
                return response.json();
            })
            .then(data => {
                if (data.thanh_cong) {
                    // Cập nhật số lượng món trên header ngay lập tức
                    const badge = document.querySelector('.so-luong-gio-hang');
                    if (badge) {
                        badge.textContent = `(${data.tong_so_mon})`;
                    }
                    alert('Đã thêm sản phẩm vào giỏ hàng thành công!');
                } else {
                    alert(data.loi || 'Không thể thêm vào giỏ hàng.');
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                // Fallback: nếu lỗi fetch thì cho phép form submit truyền thống
                this.submit();
            });
        });
    });
});
