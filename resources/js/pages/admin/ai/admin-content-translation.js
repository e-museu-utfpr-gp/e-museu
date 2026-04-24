import $ from 'jquery';

const TRANSLATION_FIELD_RE = /^translations\[([^\]]+)\]\[([^\]]+)\]$/;

function readMeta() {
    const root = document.documentElement;
    const enabled = root.getAttribute('data-admin-ai-enabled') === '1';
    const url = root.getAttribute('data-admin-ai-translate-url') || '';
    const networkMsg = root.getAttribute('data-admin-ai-msg-network') || '';
    const invalidResponseMsg = root.getAttribute('data-admin-ai-msg-invalid-response') || '';
    const sessionMsg = root.getAttribute('data-admin-ai-msg-session') || '';
    const forbiddenMsg = root.getAttribute('data-admin-ai-msg-forbidden') || '';
    const validationMsg = root.getAttribute('data-admin-ai-msg-validation') || '';
    const busyMsg = root.getAttribute('data-admin-ai-msg-busy') || '';
    const closeAria = root.getAttribute('data-admin-ai-msg-close-aria') || '';

    return {
        enabled,
        url,
        networkMsg,
        invalidResponseMsg,
        sessionMsg,
        forbiddenMsg,
        validationMsg,
        busyMsg,
        closeAria,
    };
}

/**
 * @param {HTMLFormElement} form
 * @returns {Record<string, Record<string, string>>}
 */
function collectTranslationsFromForm(form) {
    /** @type {Record<string, Record<string, string>>} */
    const out = {};
    const elements = form.querySelectorAll('input[name], textarea[name]');
    elements.forEach(el => {
        if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement)) {
            return;
        }
        const name = el.name;
        const m = name.match(TRANSLATION_FIELD_RE);
        if (!m) {
            return;
        }
        const locale = m[1];
        const field = m[2];
        if (!out[locale]) {
            out[locale] = {};
        }
        out[locale][field] = el.value;
    });

    return out;
}

/**
 * @param {Record<string, Record<string, string>>} data
 * @param {string} targetLocale
 * @param {string[]} fieldKeys
 */
function fieldHasSourceElsewhere(data, targetLocale, fieldKeys) {
    /** @type {string[]} */
    const withSource = [];
    fieldKeys.forEach(field => {
        let hit = false;
        Object.keys(data).forEach(loc => {
            if (loc === targetLocale) {
                return;
            }
            const chunk = String((data[loc] && data[loc][field]) || '').trim();
            if (chunk !== '') {
                hit = true;
            }
        });
        if (hit) {
            withSource.push(field);
        }
    });

    return withSource;
}

/**
 * @param {Record<string, Record<string, string>>} data
 * @param {string} targetLocale
 * @param {string[]} fieldsWithSource
 */
function targetFieldEmpty(data, targetLocale, field) {
    return String((data[targetLocale] && data[targetLocale][field]) || '').trim() === '';
}

/**
 * @param {HTMLFormElement} form
 * @param {string} targetLocale
 * @param {string[]} fieldKeys
 */
function refreshButtonStates(form, targetLocale, fieldKeys) {
    const data = collectTranslationsFromForm(form);
    const withSource = fieldHasSourceElsewhere(data, targetLocale, fieldKeys);

    form.querySelectorAll('.js-admin-ai-translate[data-ai-target-locale="' + targetLocale + '"]').forEach(btn => {
        if (!(btn instanceof HTMLButtonElement)) {
            return;
        }
        const mode = btn.getAttribute('data-ai-mode') || '';
        let enabled = false;
        if (mode === 'fill') {
            enabled = withSource.some(f => targetFieldEmpty(data, targetLocale, f));
        } else if (mode === 'regenerate') {
            enabled = withSource.some(f => !targetFieldEmpty(data, targetLocale, f));
        }
        btn.disabled = !enabled || btn.getAttribute('data-ai-busy') === '1';
    });
}

/**
 * @param {HTMLElement} root
 * @returns {string[]}
 */
function fieldKeysFromRoot(root) {
    const raw = root.getAttribute('data-ai-field-keys') || '';
    return raw
        .split(',')
        .map(s => s.trim())
        .filter(Boolean);
}

/**
 * @param {HTMLElement} root
 * @returns {Record<string, number>}
 */
function fieldLimitsFromRoot(root) {
    const raw = root.getAttribute('data-ai-field-limits') || '{}';
    try {
        const parsed = JSON.parse(raw);
        if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
            /** @type {Record<string, number>} */
            const out = {};
            Object.keys(parsed).forEach(k => {
                const n = Number(parsed[k]);
                if (Number.isFinite(n) && n > 0) {
                    out[k] = n;
                }
            });

            return out;
        }
    } catch {
        /* ignore */
    }

    return {};
}

/**
 * @param {string} str
 * @param {number} maxChars
 */
function truncateUnicode(str, maxChars) {
    if (maxChars <= 0) {
        return '';
    }
    const chars = Array.from(str);

    return chars.length <= maxChars ? str : chars.slice(0, maxChars).join('');
}

/**
 * @param {HTMLFormElement} form
 */
function refreshAllAiButtons(form) {
    const root = form.querySelector('[data-admin-ai-translation-root="1"]');
    if (!root) {
        return;
    }
    const fieldKeys = fieldKeysFromRoot(root);
    if (fieldKeys.length === 0) {
        return;
    }
    const locales = new Set();
    form.querySelectorAll('.js-admin-ai-translate[data-ai-target-locale]').forEach(btn => {
        const loc = btn.getAttribute('data-ai-target-locale');
        if (loc) {
            locales.add(loc);
        }
    });
    locales.forEach(loc => refreshButtonStates(form, loc, fieldKeys));
}

function showAdminAiToast(message, variant, closeAria) {
    const v = variant === 'success' ? 'success' : 'danger';
    const aria = closeAria && String(closeAria).trim() !== '' ? String(closeAria) : 'Close';
    const $alert = $('<div>', {
        class: 'alert alert-' + v + ' alert-dismissible fade show position-fixed shadow',
        role: 'alert',
        css: { top: '1rem', right: '1rem', zIndex: 2000, maxWidth: 'min(420px, 92vw)' },
    });
    $alert.append($('<div>').text(message));
    $alert.append(
        $('<button>', {
            type: 'button',
            class: 'btn-close',
            'data-bs-dismiss': 'alert',
            'aria-label': aria,
        })
    );
    $('body').append($alert);
    window.setTimeout(() => {
        $alert.remove();
    }, 8000);
}

/**
 * @param {HTMLButtonElement[]} buttons
 * @param {HTMLButtonElement|null} clickedBtn
 * @param {HTMLElement|null} progressEl
 */
function setTranslatingUi(form, buttons, clickedBtn, progressEl, busy, busyMsg) {
    if (busy) {
        form.classList.add('admin-ai-translate-form--waiting');
        if (progressEl) {
            progressEl.classList.remove('d-none');
            progressEl.classList.add('d-flex');
            progressEl.setAttribute('aria-busy', 'true');
        }
        buttons.forEach(b => {
            if (!(b instanceof HTMLButtonElement)) {
                return;
            }
            b.setAttribute('data-ai-busy', '1');
            b.disabled = true;
        });
        if (clickedBtn && busyMsg !== '') {
            clickedBtn.setAttribute('data-ai-original-html', clickedBtn.innerHTML);
            clickedBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
                '<span>' +
                escapeForHtml(busyMsg) +
                '</span>';
        } else if (clickedBtn) {
            clickedBtn.setAttribute('data-ai-original-html', clickedBtn.innerHTML);
            clickedBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        }

        return;
    }

    form.classList.remove('admin-ai-translate-form--waiting');
    if (progressEl) {
        progressEl.classList.add('d-none');
        progressEl.classList.remove('d-flex');
        progressEl.setAttribute('aria-busy', 'false');
    }
    buttons.forEach(b => {
        if (!(b instanceof HTMLButtonElement)) {
            return;
        }
        const orig = b.getAttribute('data-ai-original-html');
        if (orig !== null) {
            b.innerHTML = orig;
            b.removeAttribute('data-ai-original-html');
        }
        b.removeAttribute('data-ai-busy');
    });
}

/**
 * @param {string} text
 */
function escapeForHtml(text) {
    const d = document.createElement('div');
    d.textContent = text;

    return d.innerHTML;
}

/**
 * @param {HTMLFormElement} form
 * @param {string} targetLocale
 * @param {Record<string, unknown>} payload
 * @param {Record<string, number>} limits
 */
function applyTranslationsToForm(form, targetLocale, payload, limits) {
    Object.keys(payload).forEach(field => {
        const name = 'translations[' + targetLocale + '][' + field + ']';
        const raw = form.elements.namedItem(name);
        const el = raw && typeof raw === 'object' && 'length' in raw && raw.length > 0 ? raw[0] : raw;
        if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement) {
            let v = payload[field];
            if (typeof v !== 'string') {
                v = v == null ? '' : String(v);
            }
            const max = limits[field] ?? 65535;
            el.value = truncateUnicode(v, max);
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
}

function renderMetaText(template, providerLabel, model) {
    if (!template) {
        return '';
    }

    return template
        .replace(':provider', String(providerLabel || 'AI'))
        .replace(':model', String(model || '').trim() || '-');
}

function clearMetaInPane(pane) {
    if (!(pane instanceof HTMLElement)) {
        return;
    }
    const disclaimer = pane.querySelector('.admin-ai-translate-disclaimer');
    if (!(disclaimer instanceof HTMLElement)) {
        return;
    }
    const metaEl = disclaimer.querySelector('.admin-ai-translate-disclaimer__meta');
    if (metaEl instanceof HTMLElement) {
        metaEl.textContent = '';
        metaEl.classList.add('d-none');
    }
}

function updateMetaInPane(pane, providerLabel, model) {
    if (!(pane instanceof HTMLElement)) {
        return;
    }
    const disclaimer = pane.querySelector('.admin-ai-translate-disclaimer');
    if (!(disclaimer instanceof HTMLElement)) {
        return;
    }
    const metaEl = disclaimer.querySelector('.admin-ai-translate-disclaimer__meta');
    const template = disclaimer.getAttribute('data-ai-meta-label') || '';
    const metaText = renderMetaText(template, providerLabel, model);
    if (metaEl instanceof HTMLElement && metaText !== '') {
        metaEl.textContent = metaText;
        metaEl.classList.remove('d-none');
    }
}

$(document).ready(function () {
    const meta = readMeta();
    if (!meta.enabled || meta.url === '') {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    $(document).on('input change', '.admin-layout input, .admin-layout textarea', function (e) {
        const t = e.target;
        if (!(t instanceof HTMLElement)) {
            return;
        }
        const root = t.closest('[data-admin-ai-translation-root="1"]');
        if (!root) {
            return;
        }
        const form = root.closest('form');
        if (form instanceof HTMLFormElement) {
            refreshAllAiButtons(form);
        }
    });

    document.querySelectorAll('form').forEach(form => {
        if (form.querySelector('[data-admin-ai-translation-root="1"]')) {
            refreshAllAiButtons(form);
        }
    });

    $(document).on('shown.bs.tab', '[data-bs-toggle="tab"]', function (e) {
        const trigger = e.target;
        if (!(trigger instanceof HTMLElement)) {
            return;
        }
        const paneSelector = trigger.getAttribute('data-bs-target');
        if (!paneSelector) {
            return;
        }
        const pane = document.querySelector(paneSelector);
        clearMetaInPane(pane);
        const form = pane && pane.closest('form');
        if (form instanceof HTMLFormElement) {
            refreshAllAiButtons(form);
        }
    });

    $(document).on('change', '.js-admin-ai-provider', function (e) {
        const select = e.currentTarget;
        if (!(select instanceof HTMLSelectElement)) {
            return;
        }
        clearMetaInPane(select.closest('.tab-pane'));
    });

    $(document).on('click', '.js-admin-ai-translate', function (e) {
        const btn = e.currentTarget;
        if (!(btn instanceof HTMLButtonElement) || btn.disabled) {
            return;
        }
        const mode = btn.getAttribute('data-ai-mode') || '';
        const targetLocale = btn.getAttribute('data-ai-target-locale') || '';
        const resource = btn.getAttribute('data-ai-resource') || '';
        const form = btn.closest('form');
        if (!(form instanceof HTMLFormElement) || !targetLocale || !resource || !mode) {
            return;
        }

        const translations = collectTranslationsFromForm(form);
        const root = form.querySelector('[data-admin-ai-translation-root="1"]');
        const fieldKeys = root ? fieldKeysFromRoot(root) : [];
        const fieldLimits = root instanceof HTMLElement ? fieldLimitsFromRoot(root) : {};
        const withSource = fieldHasSourceElsewhere(translations, targetLocale, fieldKeys);
        if (withSource.length === 0) {
            return;
        }

        const buttons = Array.from(form.querySelectorAll('.js-admin-ai-translate')).filter(
            b => b instanceof HTMLButtonElement
        );
        const pane = btn.closest('.tab-pane');
        const providerSelect =
            pane instanceof HTMLElement
                ? pane.querySelector('.js-admin-ai-provider[data-ai-target-locale="' + targetLocale + '"]')
                : null;
        const selectedProvider = providerSelect instanceof HTMLSelectElement ? providerSelect.value || 'auto' : 'auto';
        /** @type {HTMLElement|null} */
        let progressBlock = null;
        if (pane instanceof HTMLElement) {
            const found = pane.querySelector('.admin-ai-translate-progress');
            progressBlock = found instanceof HTMLElement ? found : null;
        }
        clearMetaInPane(pane);

        setTranslatingUi(form, buttons, btn, progressBlock, true, meta.busyMsg);

        $.ajax({
            url: meta.url,
            method: 'POST',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            data: {
                _token: csrfToken,
                resource,
                target_locale: targetLocale,
                mode,
                provider: selectedProvider,
                translations,
            },
        })
            .done(function (resp) {
                if (resp && typeof resp === 'object' && resp.translations && typeof resp.translations === 'object') {
                    applyTranslationsToForm(form, targetLocale, resp.translations, fieldLimits);
                    updateMetaInPane(pane, resp.provider_label || resp.provider, resp.model);
                    refreshAllAiButtons(form);
                } else {
                    const inv = meta.invalidResponseMsg || meta.networkMsg;
                    showAdminAiToast(inv, 'danger', meta.closeAria);
                }
            })
            .fail(function (xhr) {
                let msg = meta.networkMsg;
                const st = xhr.status;
                if (st === 419) {
                    msg = meta.sessionMsg || msg;
                } else if (st === 403) {
                    msg = meta.forbiddenMsg || msg;
                } else if (st === 422) {
                    if (
                        xhr.responseJSON &&
                        typeof xhr.responseJSON.message === 'string' &&
                        xhr.responseJSON.message !== ''
                    ) {
                        msg = xhr.responseJSON.message;
                    } else {
                        msg = meta.validationMsg || msg;
                    }
                } else if (
                    xhr.responseJSON &&
                    typeof xhr.responseJSON.message === 'string' &&
                    xhr.responseJSON.message !== ''
                ) {
                    msg = xhr.responseJSON.message;
                }
                showAdminAiToast(msg, 'danger', meta.closeAria);
            })
            .always(function () {
                setTranslatingUi(form, buttons, btn, progressBlock, false, meta.busyMsg);
                refreshAllAiButtons(form);
            });
    });
});
