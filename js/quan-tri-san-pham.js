document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-confirm-delete]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                event.preventDefault();
            }
        });
    });
});