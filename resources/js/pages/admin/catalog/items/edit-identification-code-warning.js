document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('admin-item-edit-form');
    const codeInput = document.getElementById('identification_code');
    const modalEl = document.getElementById('identification-code-change-confirm-modal');
    const confirmBtn = document.getElementById('identification-code-change-confirm-submit');

    if (!form || !codeInput || !modalEl || !confirmBtn || !window.bootstrap) {
        return;
    }

    const original = String(form.getAttribute('data-original-identification-code') || '');
    const modal = new window.bootstrap.Modal(modalEl);
    let confirmed = false;

    form.addEventListener('submit', event => {
        const changed = String(codeInput.value || '') !== original;
        if (!changed || confirmed) {
            return;
        }
        event.preventDefault();
        modal.show();
    });

    confirmBtn.addEventListener('click', () => {
        confirmed = true;
        modal.hide();
        form.submit();
    });
});

