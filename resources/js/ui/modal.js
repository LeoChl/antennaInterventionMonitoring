export function setupModal(dialog, onSubmit) {
    const form = dialog.querySelector('form');
    const alert = dialog.querySelector('[data-error]');

    const showError = (message) => {
        alert.textContent = message ?? '';
        alert.hidden = !message;
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        showError(null);
        try {
            await onSubmit(Object.fromEntries(new FormData(form)));
            dialog.close();
        } catch (error) {
            showError(Object.values(error.errors ?? {}).flat().join(' ') || error.message);
        }
    });

    dialog.querySelector('[data-cancel]')?.addEventListener('click', () => dialog.close());

    return (values = {}) => {
        form.reset();
        showError(null);
        Object.entries(values).forEach(([name, value]) => (form.elements[name].value = value));
        dialog.showModal();
    };
}