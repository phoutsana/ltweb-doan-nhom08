document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-bt7-confirm-delete]').forEach(form => {
        form.addEventListener('submit', event => {
            if (!window.confirm('Bạn có chắc muốn xóa kỹ năng này?')) {
                event.preventDefault();
            }
        });
    });
});