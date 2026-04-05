/**
 * Admin collaborator form: set "email confirmed at" to the current local date/time.
 */
function pad2(n) {
    return String(n).padStart(2, '0');
}

function localDateTimeForDatetimeLocalInput(date) {
    return (
        date.getFullYear() +
        '-' +
        pad2(date.getMonth() + 1) +
        '-' +
        pad2(date.getDate()) +
        'T' +
        pad2(date.getHours()) +
        ':' +
        pad2(date.getMinutes())
    );
}

function initMarkEmailVerifiedAtNow() {
    const btn = document.getElementById('js-mark-email-verified-at-now');
    const input = document.getElementById('last_email_verification_at');
    if (!btn || !(input instanceof HTMLInputElement) || input.type !== 'datetime-local') {
        return;
    }
    btn.addEventListener('click', function () {
        input.value = localDateTimeForDatetimeLocalInput(new Date());
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });
}

if (document.getElementById('js-mark-email-verified-at-now')) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMarkEmailVerifiedAtNow);
    } else {
        initMarkEmailVerifiedAtNow();
    }
}
