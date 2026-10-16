/**
 * File: main.js
 * Chức năng: Nạp ở mọi trang, xử lý menu mobile, phím Esc và cập nhật số lượng yêu thích.
 */

import { capNhatSoLuongYeuThich } from './yeu-thich.js';

document.documentElement.classList.add('js');

const nutMenu = document.querySelector('.nut-menu');
const menu = document.querySelector('.menu');

if (nutMenu && menu) {
    nutMenu.addEventListener('click', () => {
        const dangMo = menu.classList.toggle('mo');

        nutMenu.setAttribute(
            'aria-expanded',
            String(dangMo)
        );
    });

    document.addEventListener('keydown', (suKien) => {
        if (
            suKien.key === 'Escape' &&
            menu.classList.contains('mo')
        ) {
            menu.classList.remove('mo');

            nutMenu.setAttribute(
                'aria-expanded',
                'false'
            );

            nutMenu.focus();
        }
    });
}

capNhatSoLuongYeuThich();