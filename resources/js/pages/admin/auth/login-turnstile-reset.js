/**
 * After a failed admin login with Turnstile, the token is consumed; reset the widget once api.js is ready.
 */
function resetAdminLoginTurnstile() {
    const el = document.querySelector('form[data-admin-login-reset-turnstile] .cf-turnstile');
    if (!el) {
        return;
    }
    let n = 0;
    const id = window.setInterval(() => {
        n += 1;
        if (typeof window.turnstile !== 'undefined' && typeof window.turnstile.reset === 'function') {
            try {
                window.turnstile.reset(el);
            } catch {
                /* ignore */
            }
            window.clearInterval(id);
        }
        if (n >= 40) {
            window.clearInterval(id);
        }
    }, 100);
}

resetAdminLoginTurnstile();
