import defaultOptions from './defaultOptions.js';

const title = document.querySelector('.dashboard__title');

if (title) {
    fetch('/api/health', defaultOptions())
        .then((response) => (response.ok ? response.json() : Promise.reject(response)))
        .then(() => {
            title.classList.add('text-bg-success');
        })
        .catch(() => {
            title.classList.add('text-bg-danger');
        });
}
