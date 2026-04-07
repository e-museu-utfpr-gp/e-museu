/**
 * Tags / extras / components for public item contribution: persisted in namespaced localStorage
 * so data survives browser restarts (unlike sessionStorage). Main form text fields use
 * {@see contribution-form-draft.js} separately.
 */
import {
    lsGetJson,
    lsGetString,
    lsIsAvailable,
    lsRemove,
    lsSetJson,
    lsSetString,
    storageKey,
} from '../storage/local-storage';

const WIZARD_KEY_PREFIX = 'catalog.itemContribution.wizard.';
const WIZARD_META_SUFFIX = 'catalog.itemContribution.wizardMeta';

/** Match previous UX copy: drafts expire after a week. */
const WIZARD_TTL_MS = 7 * 24 * 60 * 60 * 1000;

const ORDER_KEYS = ['tagIdOrder', 'extraIdOrder', 'componentIdOrder'];

/**
 * @param {string} legacyKey short key as historically used (e.g. tagCount, tag1categoryText)
 */
function wizardSuffix(legacyKey) {
    return WIZARD_KEY_PREFIX + legacyKey;
}

/**
 * @param {string} legacyKey
 * @returns {string | null}
 */
export function wizardGetItem(legacyKey) {
    return lsGetString(wizardSuffix(legacyKey));
}

/**
 * @param {string} legacyKey
 * @param {string} value
 */
export function wizardSetItem(legacyKey, value) {
    touchWizardMeta();
    lsSetString(wizardSuffix(legacyKey), value);
}

/**
 * @param {string} legacyKey
 */
export function wizardRemoveItem(legacyKey) {
    lsRemove(wizardSuffix(legacyKey));
}

function touchWizardMeta() {
    lsSetJson(WIZARD_META_SUFFIX, { savedAt: Date.now() });
}

/**
 * One-time copy from legacy sessionStorage keys (older builds) into localStorage.
 */
function migrateLegacySessionWizardToLocal() {
    if (typeof sessionStorage === 'undefined') {
        return;
    }
    const fixed = ['itemCreateForm', 'tagCount', 'extraCount', 'componentCount', ...ORDER_KEYS];
    let any = false;
    for (const k of fixed) {
        const v = sessionStorage.getItem(k);
        if (v !== null) {
            lsSetString(wizardSuffix(k), v);
            sessionStorage.removeItem(k);
            any = true;
        }
    }
    const dynRe =
        /^(tag\d+(categoryText|categoryVal|name)|extra\d+info|component\d+(categoryText|categoryVal|name|itemId))$/;
    const keys = [];
    for (let i = 0; i < sessionStorage.length; i++) {
        const k = sessionStorage.key(i);
        if (k && dynRe.test(k)) {
            keys.push(k);
        }
    }
    for (const k of keys) {
        const v = sessionStorage.getItem(k);
        if (v !== null) {
            lsSetString(wizardSuffix(k), v);
            sessionStorage.removeItem(k);
            any = true;
        }
    }
    if (any) {
        touchWizardMeta();
    }
}

/**
 * Drop wizard data if older than {@see WIZARD_TTL_MS}. Call once before restore.
 */
export function ensureWizardStorageFresh() {
    migrateLegacySessionWizardToLocal();
    const meta = lsGetJson(WIZARD_META_SUFFIX, null);
    if (!meta || typeof meta.savedAt !== 'number') {
        return;
    }
    if (Date.now() - meta.savedAt > WIZARD_TTL_MS) {
        clearItemCreateWizardStorage();
    }
}

function removeWizardKeysMatchingLegacy(regex) {
    if (!lsIsAvailable()) {
        return;
    }
    const base = storageKey(WIZARD_KEY_PREFIX);
    const toRemove = [];
    for (let i = 0; i < localStorage.length; i++) {
        const full = localStorage.key(i);
        if (full && full.startsWith(base)) {
            const legacy = full.slice(base.length);
            if (regex.test(legacy)) {
                toRemove.push(wizardSuffix(legacy));
            }
        }
    }
    for (const suffix of toRemove) {
        lsRemove(suffix);
    }
}

/**
 * When every wizard section is empty, drop the flag so restore does not expect wizard rows.
 */
function pruneItemCreateFormWhenAllSectionsEmpty() {
    const tc = parseInt(wizardGetItem('tagCount') ?? '', 10) || 0;
    const ec = parseInt(wizardGetItem('extraCount') ?? '', 10) || 0;
    const cc = parseInt(wizardGetItem('componentCount') ?? '', 10) || 0;
    if (tc === 0 && ec === 0 && cc === 0) {
        wizardRemoveItem('itemCreateForm');
    }
}

/** Clear only tag keys (counter, order, tagN*). Other sections untouched. */
export function clearTagWizardSectionStorage() {
    wizardRemoveItem('tagCount');
    wizardRemoveItem('tagIdOrder');
    removeWizardKeysMatchingLegacy(/^tag\d+(categoryText|categoryVal|name)$/);
    pruneItemCreateFormWhenAllSectionsEmpty();
}

/** Clear only extra keys. */
export function clearExtraWizardSectionStorage() {
    wizardRemoveItem('extraCount');
    wizardRemoveItem('extraIdOrder');
    removeWizardKeysMatchingLegacy(/^extra\d+info$/);
    pruneItemCreateFormWhenAllSectionsEmpty();
}

/** Clear only component keys. */
export function clearComponentWizardSectionStorage() {
    wizardRemoveItem('componentCount');
    wizardRemoveItem('componentIdOrder');
    removeWizardKeysMatchingLegacy(/^component\d+(categoryText|categoryVal|name|itemId)$/);
    pruneItemCreateFormWhenAllSectionsEmpty();
}

export function appendWizardOrder(orderKey, numericId) {
    const arr = JSON.parse(wizardGetItem(orderKey) || '[]');
    arr.push(Number(numericId));
    wizardSetItem(orderKey, JSON.stringify(arr));
}

export function removeWizardOrderId(orderKey, numericId) {
    const arr = JSON.parse(wizardGetItem(orderKey) || '[]').filter(function (x) {
        return Number(x) !== Number(numericId);
    });
    wizardSetItem(orderKey, JSON.stringify(arr));
}

/**
 * Infer numeric ids from stored row keys when order JSON is missing (avoids wrong 1..n after inconsistent state).
 */
function inferOrderFromStoredKeys(orderKey) {
    let re;
    if (orderKey === 'tagIdOrder') {
        re = /^tag(\d+)(categoryText|categoryVal|name)$/;
    } else if (orderKey === 'extraIdOrder') {
        re = /^extra(\d+)info$/;
    } else if (orderKey === 'componentIdOrder') {
        re = /^component(\d+)(categoryText|categoryVal|name|itemId)$/;
    } else {
        return [];
    }
    const ids = new Set();
    if (!lsIsAvailable()) {
        return [];
    }
    const base = storageKey(WIZARD_KEY_PREFIX);
    for (let i = 0; i < localStorage.length; i++) {
        const full = localStorage.key(i);
        if (!full || !full.startsWith(base)) {
            continue;
        }
        const legacy = full.slice(base.length);
        const m = legacy.match(re);
        if (m) {
            ids.add(Number(m[1]));
        }
    }
    return Array.from(ids).sort((a, b) => a - b);
}

/**
 * Ordered wizard entry ids. Uses stored JSON array when present; else ids inferred from keys; else 1..count (legacy).
 */
export function readWizardOrder(orderKey, count) {
    const raw = wizardGetItem(orderKey);
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
    const inferred = inferOrderFromStoredKeys(orderKey);
    if (inferred.length > 0) {
        return inferred;
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

/**
 * Clears all wizard keys for this form. Use for full reset (clear button, successful submit).
 */
export function clearItemCreateWizardStorage() {
    const fixedKeys = ['itemCreateForm', 'tagCount', 'extraCount', 'componentCount', ...ORDER_KEYS];
    for (const k of fixedKeys) {
        lsRemove(wizardSuffix(k));
    }

    removeWizardKeysMatchingLegacy(
        /^(tag\d+(categoryText|categoryVal|name))$|^(extra\d+info)$|^component\d+(categoryText|categoryVal|name|itemId)$/
    );

    lsRemove(WIZARD_META_SUFFIX);
}
