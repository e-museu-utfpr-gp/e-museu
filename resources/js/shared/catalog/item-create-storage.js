/**
 * Clears only keys used by the public catalog item contribution wizard.
 * Avoids sessionStorage.clear() which wipes unrelated origin data.
 */
const ORDER_KEYS = ['tagIdOrder', 'extraIdOrder', 'componentIdOrder'];

export function appendWizardOrder(orderKey, numericId) {
    const arr = JSON.parse(sessionStorage.getItem(orderKey) || '[]');
    arr.push(Number(numericId));
    sessionStorage.setItem(orderKey, JSON.stringify(arr));
}

export function removeWizardOrderId(orderKey, numericId) {
    const arr = JSON.parse(sessionStorage.getItem(orderKey) || '[]').filter(function (x) {
        return Number(x) !== Number(numericId);
    });
    sessionStorage.setItem(orderKey, JSON.stringify(arr));
}

/**
 * Ordered wizard entry ids. Uses stored JSON array when present; otherwise 1..count (legacy).
 */
export function readWizardOrder(orderKey, count) {
    const raw = sessionStorage.getItem(orderKey);
    if (raw) {
        try {
            const arr = JSON.parse(raw);
            if (Array.isArray(arr) && arr.length > 0) {
                return arr.map(Number);
            }
        } catch {
            /* fall through */
        }
    }
    const n = Number(count) || 0;
    if (n <= 0) {
        return [];
    }
    return Array.from({ length: n }, (_, i) => i + 1);
}

export function nextWizardIdAfterOrder(orderKey, count) {
    const order = readWizardOrder(orderKey, count);
    if (order.length === 0) {
        return 1;
    }
    return Math.max.apply(null, order) + 1;
}

export function clearItemCreateWizardStorage() {
    const fixedKeys = ['itemCreateForm', 'tagCount', 'extraCount', 'componentCount', ...ORDER_KEYS];
    for (const k of fixedKeys) {
        sessionStorage.removeItem(k);
    }

    const dynamicPattern =
        /^(tag\d+(categoryText|categoryVal|name))$|^(extra\d+info)$|^component\d+(categoryText|categoryVal|name|itemId)$/;

    for (let i = sessionStorage.length - 1; i >= 0; i--) {
        const key = sessionStorage.key(i);
        if (key && dynamicPattern.test(key)) {
            sessionStorage.removeItem(key);
        }
    }
}
