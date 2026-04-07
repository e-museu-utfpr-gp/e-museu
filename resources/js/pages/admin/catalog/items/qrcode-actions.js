function copyTextToClipboard(text) {
    if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
        return navigator.clipboard.writeText(text);
    }
    return Promise.reject(new Error('Clipboard API unavailable'));
}

function copyImageToClipboard(imageUrl) {
    if (
        !navigator.clipboard ||
        typeof navigator.clipboard.write !== 'function' ||
        typeof window.ClipboardItem === 'undefined'
    ) {
        return Promise.reject(new Error('Clipboard image API unavailable'));
    }

    return fetch(imageUrl)
        .then(res => {
            if (!res.ok) {
                throw new Error('Could not fetch QRCode image');
            }
            return res.blob();
        })
        .then(blob => {
            const item = new window.ClipboardItem({ [blob.type || 'image/png']: blob });
            return navigator.clipboard.write([item]);
        });
}

function printImage(imageUrl) {
    const w = window.open('', '_blank', 'width=700,height=700');
    if (!w) {
        return;
    }
    w.document.write(
        `<html><head><title>QRCode</title></head><body style="margin:0;display:flex;justify-content:center;align-items:center;height:100vh;"><img src="${imageUrl}" style="max-width:90vw;max-height:90vh;" /></body></html>`
    );
    w.document.close();
    w.focus();
    w.print();
}

document.addEventListener('DOMContentLoaded', () => {
    const copyLinkBtn = document.querySelector('.js-admin-qrcode-copy-link');
    const copyImageBtn = document.querySelector('.js-admin-qrcode-copy-image');
    const printBtn = document.querySelector('.js-admin-qrcode-print');
    const urlEl = document.querySelector('.js-admin-qrcode-target-url');
    const imageEl = document.querySelector('.js-admin-qrcode-image');

    if (!copyLinkBtn || !urlEl) {
        return;
    }

    copyLinkBtn.addEventListener('click', () => {
        copyTextToClipboard(urlEl.href).catch(() => {});
    });

    if (copyImageBtn && imageEl) {
        copyImageBtn.addEventListener('click', () => {
            copyImageToClipboard(imageEl.src).catch(() => {});
        });
    }

    if (printBtn && imageEl) {
        printBtn.addEventListener('click', () => {
            printImage(imageEl.src);
        });
    }
});

