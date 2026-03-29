/**
 * Shared cover + gallery drop zones, previews, and submit validation for catalog item forms.
 */

import { revokeObjectUrlIfBlob } from './revoke-blob-url';

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
        if (options.requireCoverOnSubmit === false) {
            injectGalleryInputs();
            return;
        }
        const coverInput = document.getElementById(c.inputId);
        if (!coverInput || !coverInput.files || !coverInput.files[0]) {
            e.preventDefault();
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
        revokeCoverPreviewBlob();
        const container = document.getElementById(p.containerId);
        if (container) {
            container.querySelectorAll('.' + p.thumbClass).forEach(function (el) {
                revokeObjectUrlIfBlob(el.querySelector('img'));
            });
        }
    }

    function init() {
        setupCover();
        setupGallery();
        form.addEventListener('submit', validateAndSubmit);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}
