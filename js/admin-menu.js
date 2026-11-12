document.addEventListener('DOMContentLoaded', () => {
    // Menu chinh tren thiet bi di dong
    const mobileButton = document.querySelector('.nut-menu');
    const mainMenu = document.getElementById('menu-chinh');

    if (mobileButton && mainMenu) {
        mobileButton.setAttribute('aria-expanded', 'false');

        mobileButton.addEventListener('click', () => {
            const isOpen =
                mobileButton.getAttribute('aria-expanded') === 'true';

            mobileButton.setAttribute(
                'aria-expanded',
                String(!isOpen)
            );

            mainMenu.classList.toggle('bt7-menu-open', !isOpen);
        });
    }

    // Menu quan tri vien
    const adminButton = document.getElementById('btn-toggle-admin');
    const adminMenu = document.getElementById('submenu-admin');

    if (adminButton && adminMenu) {
        adminMenu.hidden = true;
        adminButton.setAttribute('aria-expanded', 'false');

        const closeAdmin = () => {
            adminMenu.hidden = true;
            adminButton.setAttribute('aria-expanded', 'false');
        };

        adminButton.addEventListener('click', () => {
            const isOpen =
                adminButton.getAttribute('aria-expanded') === 'true';

            adminMenu.hidden = isOpen;
            adminButton.setAttribute(
                'aria-expanded',
                String(!isOpen)
            );
        });

        document.addEventListener('click', event => {
            if (
                !adminButton.contains(event.target) &&
                !adminMenu.contains(event.target)
            ) {
                closeAdmin();
            }
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                closeAdmin();
                adminButton.focus();
            }
        });
    }
});