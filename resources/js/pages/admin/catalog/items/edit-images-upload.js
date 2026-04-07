import { revokeObjectUrlIfBlob } from '../../../../shared/catalog/revoke-blob-url';

(function initAdminCatalogItemEditImages() {
    const form = document.getElementById('admin-item-edit-form');
    if (!form) {
        return;
    }

    window.__catalogItemImagesUploadInitializedForms = window.__catalogItemImagesUploadInitializedForms || {};
    if (window.__catalogItemImagesUploadInitializedForms['admin-item-edit-form']) {
        return;
    }
    window.__catalogItemImagesUploadInitializedForms['admin-item-edit-form'] = true;

    const coverLabel = form.getAttribute('data-label-cover') || '';
    const galleryLabel = form.getAttribute('data-label-gallery') || '';

    const adminGalleryFiles = [];

    async function acceptAsImage(file) {
        if (!window.__catalogUploadUtils) {
            return false;
        }
        if (window.__catalogUploadUtils.isImage(file)) {
            return true;
        }
        return (await window.__catalogUploadUtils.isImageByMagic(file)) === true;
    }

    async function setAdminCoverFromFile(file) {
        if (!(await acceptAsImage(file))) {
            return;
        }
        const input = document.getElementById('image');
        if (input && typeof DataTransfer !== 'undefined') {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
        }
        const placeholder = document.getElementById('admin-cover-placeholder');
        const preview = document.getElementById('admin-cover-preview');
        const img = document.getElementById('admin-cover-preview-img');
        if (placeholder && preview && img) {
            revokeObjectUrlIfBlob(img);
            placeholder.classList.add('d-none');
            preview.classList.remove('d-none');
            img.src = URL.createObjectURL(file);
        }
        renderAdminPreviews();
    }

    function setupAdminCover() {
        const zone = document.getElementById('admin-cover-drop-zone');
        const input = document.getElementById('image');
        const replaceBtn = document.getElementById('admin-cover-replace-btn');
        if (!zone || !input) {
            return;
        }
        zone.addEventListener('click', function (e) {
            if (!e.target.closest('#admin-cover-replace-btn')) {
                input.click();
            }
        });
        input.addEventListener('change', function () {
            if (input.files && input.files[0]) {
                void setAdminCoverFromFile(input.files[0]);
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
            void setAdminCoverFromFile(f);
        });
    }

    async function addAdminGalleryFiles(files) {
        for (let i = 0; i < files.length; i++) {
            if (await acceptAsImage(files[i])) {
                adminGalleryFiles.push(files[i]);
            }
        }
        renderAdminPreviews();
    }

    function removeAdminGalleryIndex(i) {
        adminGalleryFiles.splice(i, 1);
        renderAdminPreviews();
    }

    function setupAdminGallery() {
        const zone = document.getElementById('admin-gallery-drop-zone');
        const input = document.getElementById('admin_gallery_input');
        if (!zone || !input) {
            return;
        }
        zone.addEventListener('click', function () {
            input.click();
        });
        input.addEventListener('change', function () {
            if (input.files && input.files.length) {
                void addAdminGalleryFiles(Array.from(input.files)).finally(function () {
                    input.value = '';
                });
            }
        });
        if (window.__catalogUploadUtils) {
            window.__catalogUploadUtils.attachDropZoneState(zone);
        }
        zone.addEventListener('drop', function (e) {
            void addAdminGalleryFiles(Array.from(e.dataTransfer.files));
        });
    }

    function renderAdminPreviews() {
        const container = document.getElementById('admin-images-preview');
        if (!container) {
            return;
        }
        const coverInput = document.getElementById('image');
        const hasNewCover = coverInput && coverInput.files && coverInput.files[0];
        const currentCoverEl = document.getElementById('admin-current-cover-thumb');
        if (currentCoverEl) {
            currentCoverEl.style.display = hasNewCover ? 'none' : '';
        }
        const emptyP = container.querySelector('[data-role="gallery-empty-hint"]');
        container.querySelectorAll('.admin-image-preview-new').forEach(function (el) {
            revokeObjectUrlIfBlob(el.querySelector('img'));
            el.remove();
        });

        function addNewThumb(file, label, onRemove) {
            const wrap = document.createElement('div');
            wrap.className =
                'image-preview-thumb admin-image-preview-new position-relative d-inline-block rounded-2 overflow-hidden shadow-sm';
            wrap.style.width = '72px';
            wrap.style.height = '72px';
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = '';
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            wrap.appendChild(img);
            if (label) {
                const badge = document.createElement('span');
                badge.className = 'badge bg-info position-absolute top-0 start-0 m-1';
                badge.style.fontSize = '9px';
                badge.textContent = label;
                wrap.appendChild(badge);
            }
            if (onRemove) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-danger btn-sm position-absolute bottom-0 end-0 m-1 p-0';
                btn.style.fontSize = '10px';
                btn.style.width = '20px';
                btn.style.height = '20px';
                btn.innerHTML = '&times;';
                btn.addEventListener('click', onRemove);
                wrap.appendChild(btn);
            }
            if (emptyP) {
                emptyP.classList.add('d-none');
            }
            if (label === coverLabel) {
                container.insertBefore(wrap, container.firstChild);
            } else {
                container.appendChild(wrap);
            }
        }

        if (hasNewCover) {
            addNewThumb(coverInput.files[0], coverLabel, null);
        }
        adminGalleryFiles.forEach(function (file, i) {
            (function (idx) {
                addNewThumb(file, galleryLabel, function () {
                    removeAdminGalleryIndex(idx);
                });
            })(i);
        });
    }

    function injectAdminGalleryInputs() {
        const el = document.getElementById('admin-item-edit-form');
        if (!el || !window.__catalogUploadUtils) {
            return;
        }
        const previewImg = document.getElementById('admin-cover-preview-img');
        revokeObjectUrlIfBlob(previewImg);
        const container = document.getElementById('admin-images-preview');
        if (container) {
            container.querySelectorAll('.admin-image-preview-new').forEach(function (el) {
                revokeObjectUrlIfBlob(el.querySelector('img'));
            });
        }
        window.__catalogUploadUtils.setFileInputs(el, 'gallery_images[]', adminGalleryFiles);
    }

    function init() {
        setupAdminCover();
        setupAdminGallery();
        const f = document.getElementById('admin-item-edit-form');
        if (f) {
            f.addEventListener('submit', injectAdminGalleryInputs);
        }

        const previewRoot = document.getElementById('admin-images-preview');
        if (previewRoot) {
            previewRoot.addEventListener('click', function (e) {
                const deleteBtn = e.target.closest('.delete-image-btn');
                if (deleteBtn && deleteBtn.getAttribute('data-image-id')) {
                    if (!confirm(deleteBtn.getAttribute('data-confirm'))) {
                        return;
                    }
                    const imageId = deleteBtn.getAttribute('data-image-id');
                    const thumb = deleteBtn.closest('.admin-preview-thumb, .admin-image-preview-new');
                    if (thumb) {
                        revokeObjectUrlIfBlob(thumb.querySelector('img'));
                        thumb.remove();
                    }
                    const delContainer = document.getElementById('admin-delete-image-ids');
                    if (delContainer) {
                        const inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = 'delete_image_ids[]';
                        inp.value = imageId;
                        delContainer.appendChild(inp);
                    }
                    const setCover = document.getElementById('set_cover_image_id');
                    if (setCover) {
                        setCover.value = '';
                    }
                    return;
                }
                const setCoverBtn = e.target.closest('.set-cover-btn');
                if (setCoverBtn && setCoverBtn.getAttribute('data-image-id')) {
                    const imageId = setCoverBtn.getAttribute('data-image-id');
                    const setCoverInput = document.getElementById('set_cover_image_id');
                    if (setCoverInput) {
                        setCoverInput.value = imageId;
                    }
                    const coverInputEl = document.getElementById('image');
                    if (coverInputEl && typeof DataTransfer !== 'undefined') {
                        coverInputEl.files = new DataTransfer().files;
                    }
                    const thumb = setCoverBtn.closest('.admin-preview-thumb');
                    const img = thumb && thumb.querySelector('img');
                    const preview = document.getElementById('admin-cover-preview');
                    const previewImg = document.getElementById('admin-cover-preview-img');
                    const placeholder = document.getElementById('admin-cover-placeholder');
                    if (preview && previewImg && img && img.src) {
                        revokeObjectUrlIfBlob(previewImg);
                        placeholder.classList.add('d-none');
                        preview.classList.remove('d-none');
                        previewImg.src = img.src;
                    }
                    renderAdminPreviews();
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
