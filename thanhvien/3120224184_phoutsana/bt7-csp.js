document.addEventListener('DOMContentLoaded', () => {
    const main = document.querySelector('[data-bt7-theme]');

    if (main && main.dataset.bt7Theme === 'dark') {
        document.body.classList.add('che-do-toi');
    }

    document.querySelectorAll(
        'form[data-bt7-confirm-delete]'
    ).forEach(form => {
        form.addEventListener('submit', event => {
            if (!window.confirm(
                'Bạn có chắc chắn muốn xóa dự án này không?'
            )) {
                event.preventDefault();
            }
        });
    });
});