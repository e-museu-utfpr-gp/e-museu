import { lsGetJson, lsRemove, lsSetJson } from '../../../../shared/storage/local-storage';

const DRAFT_KEY_SUFFIX = 'catalog.itemContribution.formDraft';
const DRAFT_VERSION = 1;

/** @type {readonly string[]} */
const FIELD_NAMES = [
    'content_locale',
    'name',
    'category_id',
    'date',
    'description',
    'detail',
    'history',
    'contact',
    'full_name',
];

/** After this age the saved draft is ignored and removed (keeps localStorage from growing stale forever). */
const DRAFT_TTL_MS = 3 * 24 * 60 * 60 * 1000;

const SAVE_DEBOUNCE_MS = 400;

/**
 * `form.elements.namedItem('date')` (and similar) can fail — `elements` exposes legacy
 * named properties that shadow real controls. Query by name explicitly.
 *
 * @param {HTMLFormElement} form
 * @param {string} name
 * @returns {Element | null}
 */
function getNamedControl(form, name) {
    if (typeof CSS !== 'undefined' && typeof CSS.escape === 'function') {
        return form.querySelector(`[name="${CSS.escape(name)}"]`);
    }
    return form.querySelector(`[name="${name}"]`);
}

/**
 * @param {HTMLFormElement} form
 * @returns {Record<string, string>}
 */
function collectDraftFields(form) {
    /** @type {Record<string, string>} */
    const fields = {};
    if (!form) {
        return fields;
    }
    for (const name of FIELD_NAMES) {
        const el = getNamedControl(form, name);
        if (!el || el instanceof RadioNodeList) {
            continue;
        }
        if (el instanceof HTMLInputElement && el.type === 'file') {
            continue;
        }
        if (el instanceof HTMLInputElement && el.type === 'date') {
            // Some engines keep the chosen date only in sync with the `value` attribute; reading
            // `.value` alone can stay "" and would make the draft look empty → lsRemove in save.
            fields[name] = String(el.value || el.getAttribute('value') || '');
            continue;
        }
        if ('value' in el && typeof el.value === 'string') {
            fields[name] = el.value;
        }
    }
    return fields;
}

/**
 * @param {Record<string, string>} fields
 * @returns {boolean}
 */
function draftHasAnyContent(fields) {
    return Object.values(fields).some(v => v != null && String(v).trim() !== '');
}

/**
 * @param {unknown} raw
 * @returns {{ fields: Record<string, string> } | null}
 */
function readValidDraftPayload(raw) {
    if (!raw || typeof raw !== 'object' || raw === null) {
        return null;
    }
    const o = /** @type {{ v?: unknown; savedAt?: unknown; fields?: unknown }} */ (raw);
    if (o.v !== DRAFT_VERSION || typeof o.savedAt !== 'number' || typeof o.fields !== 'object' || o.fields === null) {
        return null;
    }
    if (Date.now() - o.savedAt > DRAFT_TTL_MS) {
        return null;
    }
    return { fields: /** @type {Record<string, string>} */ (o.fields) };
}

/**
 * @param {HTMLFormElement} form
 */
export function saveContributionFormDraft(form) {
    if (!form) {
        return;
    }
    const fields = collectDraftFields(form);
    if (!draftHasAnyContent(fields)) {
        lsRemove(DRAFT_KEY_SUFFIX);
        return;
    }
    lsSetJson(DRAFT_KEY_SUFFIX, { v: DRAFT_VERSION, savedAt: Date.now(), fields });
}

/**
 * @returns {boolean}
 */
export function hasContributionFormDraft() {
    const raw = lsGetJson(DRAFT_KEY_SUFFIX, null);
    const payload = readValidDraftPayload(raw);
    if (!payload) {
        if (raw !== null) {
            lsRemove(DRAFT_KEY_SUFFIX);
        }
        return false;
    }
    return draftHasAnyContent(payload.fields);
}

/**
 * @param {HTMLFormElement} form
 */
export function restoreContributionFormDraft(form) {
    const raw = lsGetJson(DRAFT_KEY_SUFFIX, null);
    const payload = readValidDraftPayload(raw);
    if (!payload) {
        if (raw !== null) {
            lsRemove(DRAFT_KEY_SUFFIX);
        }
        return;
    }
    const { fields } = payload;
    for (const name of FIELD_NAMES) {
        const value = fields[name];
        if (value === undefined || value === null) {
            continue;
        }
        const el = getNamedControl(form, name);
        if (!el || el instanceof RadioNodeList) {
            continue;
        }
        if (el instanceof HTMLInputElement && el.type === 'file') {
            continue;
        }
        if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement || el instanceof HTMLSelectElement) {
            const s = String(value);
            el.value = s;
            if (el instanceof HTMLInputElement && el.type === 'date' && s) {
                // Keeps WebKit/Safari in sync so `collectDraftFields` sees the date; "Limpar
                // formulário" still clears via `clearContributionReleaseDateDefault` after reset.
                el.setAttribute('value', s);
            }
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
}

export function clearContributionFormDraft() {
    lsRemove(DRAFT_KEY_SUFFIX);
}

/**
 * @param {(fn: () => void) => void} debounce
 * @param {HTMLFormElement} form
 */
function syncDateInputValueAttribute(el) {
    if (!(el instanceof HTMLInputElement) || el.type !== 'date' || el.name !== 'date') {
        return;
    }
    if (el.value) {
        el.setAttribute('value', el.value);
    } else {
        el.removeAttribute('value');
    }
}

function attachAutosave(debounce, form) {
    const scheduleSave = debounce(function () {
        saveContributionFormDraft(form);
    }, SAVE_DEBOUNCE_MS);

    function flushSave() {
        saveContributionFormDraft(form);
    }

    function onDateMaybeSync(e) {
        const t = e.target;
        if (t instanceof HTMLInputElement && t.type === 'date' && t.name === 'date') {
            syncDateInputValueAttribute(t);
        }
    }

    form.addEventListener('input', onDateMaybeSync);
    form.addEventListener('change', onDateMaybeSync);
    form.addEventListener('input', scheduleSave);
    form.addEventListener('change', scheduleSave);
    window.addEventListener('pagehide', flushSave);
    document.addEventListener('visibilitychange', function onVis() {
        if (document.visibilityState === 'hidden') {
            flushSave();
        }
    });
}

/**
 * @template TArgs extends unknown[]
 * @param {(...args: TArgs) => void} fn
 * @param {number} waitMs
 * @returns {(...args: TArgs) => void}
 */
function debounce(fn, waitMs) {
    /** @type {ReturnType<typeof setTimeout> | null} */
    let t = null;
    return function (...args) {
        if (t !== null) {
            clearTimeout(t);
        }
        t = setTimeout(function () {
            t = null;
            fn.apply(null, args);
        }, waitMs);
    };
}

/**
 * @param {HTMLFormElement | null} form
 */
export function initContributionFormDraftAutosave(form) {
    if (!form) {
        return;
    }
    attachAutosave(debounce, form);
}
