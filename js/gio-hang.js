document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.form-them-gio-hang').forEach(form => {
        form.addEventListener('submit', async event => {
            event.preventDefault();

            const data = new FormData(form);
            const id = Number(data.get('id'));
            const soLuong = Number(data.get('so_luong') || 1);
            const token = String(data.get('csrf_token') || '');

            if (!Number.isInteger(id) || id <= 0 || !token) {
                alert('Du lieu san pham hoac ma CSRF khong hop le.');
                return;
            }

            const button = form.querySelector('button[type="submit"]');
            if (button) button.disabled = true;

            try {
                const response = await fetch('api/gio-hang.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': token
                    },
                    body: JSON.stringify({
                        san_pham_id: id,
                        so_luong: soLuong
                    })
                });

                const result = await response.json().catch(() => null);

                if (!response.ok || !result?.thanh_cong) {
                    throw new Error(result?.loi || `HTTP ${response.status}`);
                }

                const badge = document.querySelector('.so-luong-gio-hang');
                if (badge) {
                    badge.textContent = String(result.tong_so_mon);
                }

                alert('Da them san pham vao gio hang!');
            } catch (error) {
                alert(error instanceof Error ? error.message : 'Loi ket noi');
            } finally {
                if (button) button.disabled = false;
            }
        });
    });
});