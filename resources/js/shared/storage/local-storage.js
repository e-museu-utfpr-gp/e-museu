/**
 * Namespaced localStorage access with availability checks and safe JSON helpers.
 * Fails quietly when storage is unavailable (private mode, quota, disabled).
 */
const STORAGE_PREFIX = 'emuseu.v1.';

/** @type {boolean | null} */
let lsProbeResult = null;

/**
 * @param {string} suffix Logical key (no prefix); avoid collisions via descriptive suffixes.
 * @returns {string}
 */
export function storageKey(suffix) {
    return STORAGE_PREFIX + suffix;
}

/**
 * @returns {boolean}
 */
export function lsIsAvailable() {
    if (lsProbeResult !== null) {
        return lsProbeResult;
    }
    try {
        const k = storageKey('__probe__');
        localStorage.setItem(k, '1');
        localStorage.removeItem(k);
        lsProbeResult = true;
        return true;
    } catch {
        lsProbeResult = false;
        return false;
    }
}

/**
 * @param {string} suffix
 * @returns {string | null}
 */
export function lsGetString(suffix) {
    if (!lsIsAvailable()) {
        return null;
    }
    try {
        return localStorage.getItem(storageKey(suffix));
    } catch {
        return null;
    }
}

/**
 * @param {string} suffix
 * @param {string} value
 * @returns {boolean}
 */
export function lsSetString(suffix, value) {
    if (!lsIsAvailable()) {
        return false;
    }
    try {
        localStorage.setItem(storageKey(suffix), value);
        return true;
    } catch {
        return false;
    }
}

/**
 * @param {string} suffix
 */
export function lsRemove(suffix) {
    if (!lsIsAvailable()) {
        return;
    }
    try {
        localStorage.removeItem(storageKey(suffix));
    } catch {
        /* ignore */
    }
}

/**
 * @param {string} suffix
 * @param {unknown} fallback
 * @returns {unknown}
 */
export function lsGetJson(suffix, fallback = null) {
    const raw = lsGetString(suffix);
    if (raw === null || raw === '') {
        return fallback;
    }
    try {
        return JSON.parse(raw);
    } catch {
        return fallback;
    }
}

/**
 * @param {string} suffix
 * @param {unknown} value Serializable to JSON.
 * @returns {boolean}
 */
export function lsSetJson(suffix, value) {
    try {
        return lsSetString(suffix, JSON.stringify(value));
    } catch {
        return false;
    }
}
