/**
 * Shared cover + gallery drop zones, previews, and submit validation for catalog item forms.
 */

import { revokeObjectUrlIfBlob } from './revoke-blob-url';

/**
 * Laravel validation keys use dot notation (e.g. tags.0.name).
 * @param {string} key
 * @returns {string}
 */
function validationErrorKeyToHtmlName(key) {
    if (typeof key !== 'string' || !key.includes('.')) {
        return key;
    }
    const parts = key.split('.');
    let name = parts[0];
    for (let i = 1; i < parts.length; i++) {
        name += `[${parts[i]}]`;
    }
    return name;
}

function clearCatalogAsyncFieldValidation(form) {
    form.querySelectorAll('[data-catalog-async-validation-field="1"]').forEach(function (el) {
        el.classList.remove('is-invalid');
        el.removeAttribute('data-catalog-async-validation-field');
    });
    form.querySelectorAll('[data-catalog-async-validation-feedback="1"]').forEach(function (el) {
        el.remove();
    });
}

function clearCatalogAsyncGeneralValidation(root) {
    if (!root) {
        return;
    }
    root.querySelectorAll('[data-catalog-async-validation-general="1"]').forEach(function (el) {
        el.remove();
    });
}

/**
 * @param {HTMLFormElement} form
 * @param {Record<string, string | string[]>} errors
 * @returns {string[]}
 */
function applyCatalogAsyncValidationErrors(form, errors) {
    const general = [];
    for (const [key, raw] of Object.entries(errors)) {
        const msg = Array.isArray(raw) && raw.length ? raw[0] : String(raw ?? '');
        const name = validationErrorKeyToHtmlName(key);
        /** @type {Element | null} */
        let el = null;
        if (typeof CSS !== 'undefined' && typeof CSS.escape === 'function') {
            el = form.querySelector(`[name="${CSS.escape(name)}"]`);
        } else {
            el = form.querySelector(`[name="${name}"]`);
        }
        if (!el) {
            general.push(msg);
            continue;
        }
        if (el instanceof RadioNodeList) {
            general.push(msg);
            continue;
        }
        el.classList.add('is-invalid');
        el.setAttribute('data-catalog-async-validation-field', '1');
        const wrap = el.closest('.input-div') || el.parentElement;
        if (!wrap) {
            general.push(msg);
            continue;
        }
        let fb = wrap.querySelector('.invalid-feedback[data-catalog-async-validation-feedback="1"]');
        if (!fb) {
            fb = document.createElement('div');
            fb.className = 'invalid-feedback d-block';
            fb.setAttribute('data-catalog-async-validation-feedback', '1');
            wrap.appendChild(fb);
        }
        fb.textContent = msg;
    }
    return general;
}

/**
 * @param {HTMLElement} host
 * @param {string[]} messages
 */
function showCatalogAsyncGeneralErrors(host, messages) {
    if (!host || messages.length === 0) {
        return;
    }
    const insertBefore = host.firstChild;
    for (let i = messages.length - 1; i >= 0; i--) {
        const msg = messages[i];
        const p = document.createElement('p');
        p.className = 'error-div text-wrap fw-bold m-1 mb-2 p-1';
        p.setAttribute('data-catalog-async-validation-general', '1');
        const icon = document.createElement('i');
        icon.className = 'bi bi-exclamation-circle-fill mx-1 h5';
        p.appendChild(icon);
        p.appendChild(document.createTextNode(' ' + msg));
        host.insertBefore(p, insertBefore);
    }
}

/**
 * @param {HTMLFormElement} form
 * @param {HTMLElement | null} messagesHost
 */
function scrollToCatalogAsyncValidationError(form, messagesHost) {
    const firstField = form.querySelector('[data-catalog-async-validation-field="1"]');
    if (firstField) {
        firstField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    const root = messagesHost || form;
    const firstGen = root.querySelector('[data-catalog-async-validation-general="1"]');
    if (firstGen) {
        firstGen.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

/**
 * @param {HTMLFormElement} form
 * @param {{ messagesHost?: HTMLElement | null }} [opts]
 */
async function submitCatalogItemFormViaFetch(form, opts) {
    const messagesHost = opts?.messagesHost ?? null;
    const generalRoot = messagesHost || form;
    clearCatalogAsyncFieldValidation(form);
    clearCatalogAsyncGeneralValidation(generalRoot);

    const submitControls = form.querySelectorAll('button[type="submit"], input[type="submit"]');
    submitControls.forEach(function (btn) {
        btn.disabled = true;
    });

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
        const fd = new FormData(form);
        const res = await fetch(form.action, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin',
            redirect: 'manual',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(token ? { 'X-CSRF-TOKEN': token } : {}),
            },
        });

        if (res.status === 422) {
            const data = await res.json().catch(function () {
                return {};
            });
            const bag = data.errors && typeof data.errors === 'object' ? data.errors : {};
            const general = applyCatalogAsyncValidationErrors(form, bag);
            if (general.length) {
                showCatalogAsyncGeneralErrors(generalRoot, general);
            }
            scrollToCatalogAsyncValidationError(form, messagesHost);
            return;
        }

        if (res.status === 419) {
            window.location.reload();
            return;
        }

        if (res.status >= 300 && res.status < 400) {
            const loc = res.headers.get('Location');
            if (loc) {
                window.location.href = loc;
                return;
            }
        }

        if (res.ok && res.redirected) {
            window.location.href = res.url;
            return;
        }

        if (res.ok) {
            window.location.href = res.url || form.action;
            return;
        }

        window.location.reload();
    } catch {
        window.location.reload();
    } finally {
        submitControls.forEach(function (btn) {
            btn.disabled = false;
        });
    }
}

/**
 * @param {object} options
 * @param {string} options.formId
 * @param {string} options.initRegistryKey
 * @param {object} options.cover
 * @param {string} options.cover.inputId
 * @param {string} options.cover.zoneId
 * @param {string} [options.cover.replaceBtnSelector]
 * @param {string} options.cover.placeholderId
 * @param {string} options.cover.previewWrapId
 * @param {string} options.cover.previewImgId
 * @param {string} [options.cover.requiredMsgId]
 * @param {string} [options.cover.invalidHighlightZoneId]
 * @param {object} options.gallery
 * @param {string} options.gallery.zoneId
 * @param {string} options.gallery.inputId
 * @param {object} options.preview
 * @param {string} options.preview.containerId
 * @param {string} options.preview.emptyId
 * @param {string} options.preview.thumbClass
 * @param {string|number} options.preview.thumbWidthPx
 * @param {string|number} options.preview.thumbHeightPx
 * @param {string} [options.preview.badgeFontSize]
 * @param {string} options.preview.removeBtnClassName
 * @param {object} [options.preview.removeBtnInlineStyle]
 * @param {boolean} [options.requireCoverOnSubmit]
 * @param {boolean} [options.asyncSubmit] When true, submit via fetch + JSON errors so file inputs stay filled on validation failure.
 * @param {string} [options.asyncValidationMessagesHostId] Element id for inline general error messages (e.g. blocked).
 */
export function initCatalogItemImageUploadForm(options) {
    const form = document.getElementById(options.formId);
    if (!form) {
        return;
    }

    window.__catalogItemImagesUploadInitializedForms = window.__catalogItemImagesUploadInitializedForms || {};
    if (window.__catalogItemImagesUploadInitializedForms[options.initRegistryKey]) {
        return;
    }
    window.__catalogItemImagesUploadInitializedForms[options.initRegistryKey] = true;

    const coverLabel = form.getAttribute('data-label-cover') || '';
    const galleryLabel = form.getAttribute('data-label-gallery') || '';
    const removeLabel = form.getAttribute('data-label-remove-image') || '';

    const c = options.cover;
    const g = options.gallery;
    const p = options.preview;

    const galleryFiles = [];

    function isImage(file) {
        return window.__catalogUploadUtils && window.__catalogUploadUtils.isImage(file);
    }

    function setCoverFromFile(file) {
        if (!isImage(file)) {
            return;
        }
        const input = document.getElementById(c.inputId);
        if (input && typeof DataTransfer !== 'undefined') {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
        }
        const placeholder = document.getElementById(c.placeholderId);
        const preview = document.getElementById(c.previewWrapId);
        const img = document.getElementById(c.previewImgId);
        if (placeholder && preview && img) {
            revokeObjectUrlIfBlob(img);
            placeholder.classList.add('d-none');
            preview.classList.remove('d-none');
            img.src = URL.createObjectURL(file);
        }
        renderPreviews();
    }

    function setupCover() {
        const zone = document.getElementById(c.zoneId);
        const input = document.getElementById(c.inputId);
        const replaceBtn = c.replaceBtnSelector ? document.querySelector(c.replaceBtnSelector) : null;
        if (!zone || !input) {
            return;
        }
        zone.addEventListener('click', function (e) {
            if (!c.replaceBtnSelector || !e.target.closest(c.replaceBtnSelector)) {
                input.click();
            }
        });
        input.addEventListener('change', function () {
            if (input.files && input.files[0]) {
                setCoverFromFile(input.files[0]);
            }
        });
        if (replaceBtn) {
            replaceBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                input.click();
            });
        }
        if (window.__catalogUploadUtils) {
            window.__catalogUploadUtils.attachDropZoneState(zone);
        }
        zone.addEventListener('drop', function (e) {
            const f = e.dataTransfer.files[0];
            if (isImage(f)) {
                setCoverFromFile(f);
            }
        });
    }

    function addGalleryFiles(files) {
        for (let i = 0; i < files.length; i++) {
            if (isImage(files[i])) {
                galleryFiles.push(files[i]);
            }
        }
        renderPreviews();
    }

    function removeGalleryIndex(i) {
        galleryFiles.splice(i, 1);
        renderPreviews();
    }

    function setupGallery() {
        const zone = document.getElementById(g.zoneId);
        const input = document.getElementById(g.inputId);
        if (!zone || !input) {
            return;
        }
        zone.addEventListener('click', function () {
            input.click();
        });
        input.addEventListener('change', function () {
            if (input.files && input.files.length) {
                addGalleryFiles(Array.from(input.files));
                input.value = '';
            }
        });
        if (window.__catalogUploadUtils) {
            window.__catalogUploadUtils.attachDropZoneState(zone);
        }
        zone.addEventListener('drop', function (e) {
            addGalleryFiles(Array.from(e.dataTransfer.files));
        });
    }

    function renderPreviews() {
        const container = document.getElementById(p.containerId);
        const emptyEl = document.getElementById(p.emptyId);
        if (!container || !emptyEl) {
            return;
        }
        container.querySelectorAll('.' + p.thumbClass).forEach(function (el) {
            const im = el.querySelector('img');
            revokeObjectUrlIfBlob(im);
            el.remove();
        });
        emptyEl.style.display = 'block';

        const coverInput = document.getElementById(c.inputId);
        const coverFile = coverInput && coverInput.files && coverInput.files[0];
        const hasAny = !!coverFile || galleryFiles.length > 0;
        if (!hasAny) {
            return;
        }

        function thumb(file, label, onRemove) {
            const wrap = document.createElement('div');
            wrap.className = p.thumbClass + ' position-relative d-inline-block rounded-2 overflow-hidden shadow-sm';
            wrap.style.width = String(p.thumbWidthPx) + 'px';
            wrap.style.height = String(p.thumbHeightPx) + 'px';
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = '';
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            wrap.appendChild(img);
            if (label) {
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary position-absolute top-0 start-0 m-1';
                if (p.badgeFontSize) {
                    badge.style.fontSize = p.badgeFontSize;
                }
                badge.textContent = label;
                wrap.appendChild(badge);
            }
            if (onRemove) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = p.removeBtnClassName;
                if (p.removeBtnInlineStyle) {
                    Object.assign(btn.style, p.removeBtnInlineStyle);
                }
                btn.innerHTML = '&times;';
                btn.title = removeLabel;
                btn.addEventListener('click', onRemove);
                wrap.appendChild(btn);
            }
            container.appendChild(wrap);
            emptyEl.style.display = 'none';
        }

        if (coverFile) {
            thumb(coverFile, coverLabel, null);
        }
        galleryFiles.forEach(function (file, i) {
            (function (idx) {
                thumb(file, galleryLabel, function () {
                    removeGalleryIndex(idx);
                });
            })(i);
        });
    }

    function injectGalleryInputs() {
        if (!window.__catalogUploadUtils) {
            return;
        }
        window.__catalogUploadUtils.setFileInputs(form, 'gallery_images[]', galleryFiles);
    }

    function revokeCoverPreviewBlob() {
        const img = document.getElementById(c.previewImgId);
        revokeObjectUrlIfBlob(img);
    }

    function validateAndSubmit(e) {
        const asyncSubmit = options.asyncSubmit === true;
        if (asyncSubmit) {
            e.preventDefault();
        }

        const messagesHostEl = options.asyncValidationMessagesHostId
            ? document.getElementById(options.asyncValidationMessagesHostId)
            : null;

        if (options.requireCoverOnSubmit === false) {
            injectGalleryInputs();
            if (asyncSubmit) {
                submitCatalogItemFormViaFetch(form, { messagesHost: messagesHostEl }).catch(function () {
                    window.location.reload();
                });
            }
            return;
        }

        const coverInput = document.getElementById(c.inputId);
        if (!coverInput || !coverInput.files || !coverInput.files[0]) {
            if (!asyncSubmit) {
                e.preventDefault();
            }
            const zoneId = c.invalidHighlightZoneId || c.zoneId;
            const zone = document.getElementById(zoneId);
            if (zone) {
                zone.scrollIntoView({ behavior: 'smooth', block: 'center' });
                zone.classList.add('upload-drop-zone--invalid');
                setTimeout(function () {
                    zone.classList.remove('upload-drop-zone--invalid');
                }, 2000);
            }
            if (c.requiredMsgId) {
                const msg = document.getElementById(c.requiredMsgId);
                if (msg) {
                    msg.classList.remove('d-none');
                }
            }
            return false;
        }
        if (c.requiredMsgId) {
            const msg = document.getElementById(c.requiredMsgId);
            if (msg) {
                msg.classList.add('d-none');
            }
        }
        injectGalleryInputs();

        if (asyncSubmit) {
            // Do not revoke preview blob URLs here: on 422 the page stays open and previews must
            // remain valid; on success we navigate away and the tab is torn down.
            submitCatalogItemFormViaFetch(form, { messagesHost: messagesHostEl }).catch(function () {
                window.location.reload();
            });
            return;
        }

        revokeCoverPreviewBlob();
        const container = document.getElementById(p.containerId);
        if (container) {
            container.querySelectorAll('.' + p.thumbClass).forEach(function (el) {
                revokeObjectUrlIfBlob(el.querySelector('img'));
            });
        }
    }

    function resetImageUploadState() {
        galleryFiles.length = 0;
        const coverInput = document.getElementById(c.inputId);
        const galleryInput = document.getElementById(g.inputId);
        if (coverInput && typeof DataTransfer !== 'undefined') {
            coverInput.files = new DataTransfer().files;
        } else if (coverInput) {
            coverInput.value = '';
        }
        if (galleryInput) {
            galleryInput.value = '';
        }
        revokeCoverPreviewBlob();
        const placeholder = document.getElementById(c.placeholderId);
        const preview = document.getElementById(c.previewWrapId);
        const img = document.getElementById(c.previewImgId);
        if (img) {
            img.removeAttribute('src');
        }
        if (placeholder && preview) {
            preview.classList.add('d-none');
            placeholder.classList.remove('d-none');
        }
        if (c.requiredMsgId) {
            const msg = document.getElementById(c.requiredMsgId);
            if (msg) {
                msg.classList.add('d-none');
            }
        }
        renderPreviews();
    }

    function init() {
        setupCover();
        setupGallery();
        form.addEventListener('submit', validateAndSubmit);
        window.__catalogItemImageUploadReset = window.__catalogItemImageUploadReset || {};
        window.__catalogItemImageUploadReset[options.initRegistryKey] = resetImageUploadState;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}
