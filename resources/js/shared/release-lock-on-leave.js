document.addEventListener('DOMContentLoaded', () => {
    const marker = document.querySelector('[data-release-lock="true"]');
    if (!marker) {
        return;
    }

    const url = marker.dataset.releaseLockUrl;
    const type = marker.dataset.releaseLockType;
    const id = marker.dataset.releaseLockId;

    if (!url || !type || !id) {
        return;
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!token) {
        return;
    }

    const form = marker.closest('form');
    if (!form) {
        return;
    }

    let formSubmitted = false;
    let lockReleased = false;

    form.addEventListener('submit', () => {
        formSubmitted = true;
    });

    function releaseLock() {
        if (formSubmitted || lockReleased) {
            return;
        }
        lockReleased = true;

        const data = new FormData();
        data.append('_token', token);
        data.append('type', type);
        data.append('id', String(id));

        if (navigator.sendBeacon) {
            navigator.sendBeacon(url, data);
        } else {
            fetch(url, {
                method: 'POST',
                body: data,
                keepalive: true,
            }).catch(() => {});
        }
    }

    window.addEventListener('pagehide', releaseLock);
    window.addEventListener('beforeunload', releaseLock);
});
