import { devWarn } from '../dev-warn';

/**
 * Clears public contribution verification session on the server (same endpoint as item create “clear form”).
 *
 * @param {HTMLFormElement} form
 * @returns {Promise<void>}
 */
function notifyClearSessionFailed(form) {
    const net = form.querySelector('.js-email-check-contact-network-error');
    if (net) {
        net.hidden = false;
    }
}

export function clearContributionSessionOnServer(form) {
    const url = form.getAttribute('data-route-clear-contribution-session');
    if (!url) {
        return Promise.resolve();
    }
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    return fetch(url, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ _token: token }),
        credentials: 'same-origin',
    })
        .then(response => {
            if (!response.ok) {
                notifyClearSessionFailed(form);
            }
            return response;
        })
        .catch(err => {
            notifyClearSessionFailed(form);
            devWarn('[clear-contribution-session] request failed', err);
        });
}
