import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';
import axios from 'axios';

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.plugin(intersect);
Alpine.start();

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.loginAsClient = function loginAsClient(id, element) {
    axios
        .post(
            '/ajax/login-as-client',
            {
                id: id,
                type: 'client',
            },
            {
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                },
            }
        )
        .then(function (response) {
            if (response.data.status == 0) {
                window.location.href = '/client/customer-satisfaction';
            }
        })
        .catch(function (error) {
            console.log(error);
        });
};

window.loginAsDeloitteClient = function loginAsDeloitteClient(id) {
    axios
        .post(
            '/ajax/login-as-deloitte-client',
            {
                id: id,
                path: window.location.pathname,
            },
            {
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                },
            }
        )
        .then(function (response) {
            if (response.data.status == 0) {
                window.location.href = response.data.redirect;
            }
        })
        .catch(function (error) {
            console.log(error);
        });
};

window.backToAdmin = function backToAdmin() {
    axios
        .get('/ajax/login-back-as-admin', {
            header: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
            },
        })
        .then(function (response) {
            if (response.data.status == 0) {
                window.location.href = response.data.redirect;
            }
        })
        .catch(function (error) {
            console.log(error);
        });
};

document.querySelectorAll('.put-loader-on-click').forEach((element) => {
    element.addEventListener('click', (e) => {
        const width = element.getBoundingClientRect().width;
        const height = element.getBoundingClientRect().height;
        const url = element.getAttribute('url');
        element.style.width = `${width}px`;
        element.classList.add('flex', 'justify-center', 'items-center');
        element.innerHTML = `<?xml version="1.0" fill="currentColor" encoding="UTF-8"?><svg  style="height: ${height}px"  id="Réteg_1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.85 31.7"><defs><style>.cls-1{stroke: currentColor; fill:currentColor; stroke-width:1.85px;}.cls-1,.cls-2{fill:none; stroke: currentColor; stroke-linecap:round;stroke-linejoin:round;}.cls-2{stroke: currentColor; stroke-width:1.75px;}</style></defs><path class="cls-2" d="m17.2,8.83c2.07-3.13,0-7.91,0-7.91H2.65s-1.72,4.74,0,7.91c1.6,2.96,5.58,3.77,5.58,7.02,0,3.01-4.15,3.98-5.58,7.02-1.72,3.67,0,7.91,0,7.91h14.56s1.72-4.01,0-7.91-5.18-4.1-5.18-7.02c0-3.89,3.32-4.22,5.18-7.02Z"/><path class="cls-1" d="m.92.92h18"/><path class="cls-1" d="m.92,30.78h18"/><path  style="fill: currentColor;" d="m9.8,11.27c1.27,0,3.73-2.27,3.73-2.27h-7.81s2.81,2.27,4.08,2.27Z"/><path style="fill: currentColor;" d="m15.37,24.12s-3.97,2.03-7.74,0c-.83-.45-2.98,0-2.98,0-.12.26-.22.53-.31.81-.63,2.08.28,5.2.28,5.2h10.7s.87-2.89.27-5.36c-.06-.23-.14-.44-.22-.65Z"/></svg>`;

        setTimeout(() => {
            if (url) {
                window.location.replace(url);
            }
        }, 200);
    });
});
