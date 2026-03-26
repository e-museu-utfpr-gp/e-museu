<x-layouts.admin :title="__('view.admin.catalog.items.create.title')"
    :heading="__('view.admin.catalog.items.create.heading')">
            <form action="{{ route('admin.catalog.items.store') }}" method="POST" enctype="multipart/form-data" id="admin-item-create-form">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <x-ui.inputs.admin.text
                            name="name"
                            id="name"
                            :label="__('view.admin.catalog.items.create.name')"
                        />
                        <x-ui.inputs.admin.textarea
                            name="description"
                            id="description"
                            :rows="5"
                            :label="__('view.admin.catalog.items.create.description')"
                        />
                        <x-ui.inputs.admin.textarea
                            name="detail"
                            id="detail"
                            :rows="7"
                            :label="__('view.admin.catalog.items.create.detail')"
                        />
                        <div class="row">
                            <div class="col-md-6">
                                <x-ui.inputs.admin.select
                                    name="category_id"
                                    id="category_id"
                                    :label="__('view.admin.catalog.items.create.item_category')"
                                    required
                                >
                                    <option value="" @selected(old('category_id') === null || old('category_id') === '')>-</option>
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}" @selected(old('category_id') == $itemCategory->id)>
                                            {{ $itemCategory->name }}
                                        </option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                                <x-ui.inputs.admin.select
                                    name="collaborator_id"
                                    id="collaborator_id"
                                    :label="__('view.admin.catalog.items.create.collaborator')"
                                    required
                                >
                                    <option value="" @selected(old('collaborator_id') === null || old('collaborator_id') === '')>-</option>
                                    @foreach ($collaborators as $collaborator)
                                        <option value="{{ $collaborator->id }}" @selected(old('collaborator_id') == $collaborator->id)>
                                            {{ $collaborator->contact }} - {{ $collaborator->full_name }}
                                        </option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                                <x-ui.inputs.admin.text
                                    name="date"
                                    id="date"
                                    type="date"
                                    :label="__('view.admin.catalog.items.create.date')"
                                />
                            </div>
                            <div class="col-md-6">
                            @include('pages.admin.catalog.items._partials.create.images-upload')
                                <x-ui.inputs.admin.select
                                    name="validation"
                                    id="validation"
                                    :label="__('view.admin.catalog.items.create.validation')"
                                >
                                    <option value="0" @selected(old('validation') == 0)>{{ __('view.admin.catalog.items.create.no') }}</option>
                                    <option value="1" @selected(old('validation') == 1)>{{ __('view.admin.catalog.items.create.yes') }}</option>
                                </x-ui.inputs.admin.select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-ui.inputs.admin.textarea
                            name="history"
                            id="history"
                            :rows="46"
                            :label="__('view.admin.catalog.items.create.history')"
                        />
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">{{ __('view.admin.catalog.items.create.submit') }}</x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
            </form>
        <x-ui.images.catalog.upload-assets />
        <script>
            (function() {
                if (window.__catalogItemImagesUploadInitializedForms && window.__catalogItemImagesUploadInitializedForms['admin-item-create-form']) return;
                var coverLabel = @json(__('app.catalog.item_image.cover'));
                var galleryLabel = @json(__('app.catalog.item_image.gallery'));
                var removeLabel = @json(__('view.catalog.items.create.remove_image'));
                var adminGalleryFiles = [];

                function isImage(file) { return window.__catalogUploadUtils && window.__catalogUploadUtils.isImage(file); }

                function setCoverFromFile(file) {
                    if (!isImage(file)) return;
                    var input = document.getElementById('admin_create_cover_image');
                    if (input && typeof DataTransfer !== 'undefined') {
                        var dt = new DataTransfer();
                        dt.items.add(file);
                        input.files = dt.files;
                    }
                    var placeholder = document.getElementById('admin-create-cover-placeholder');
                    var preview = document.getElementById('admin-create-cover-preview');
                    var img = document.getElementById('admin-create-cover-preview-img');
                    if (placeholder && preview && img) {
                        placeholder.classList.add('d-none');
                        preview.classList.remove('d-none');
                        img.src = URL.createObjectURL(file);
                    }
                    renderPreviews();
                }

                function setupCover() {
                    var zone = document.getElementById('admin-create-cover-drop-zone');
                    var input = document.getElementById('admin_create_cover_image');
                    var replaceBtn = document.getElementById('admin-create-cover-replace-btn');
                    if (!zone || !input) return;
                    zone.addEventListener('click', function(e) { if (!e.target.closest('#admin-create-cover-replace-btn')) input.click(); });
                    input.addEventListener('change', function() {
                        if (input.files && input.files[0]) setCoverFromFile(input.files[0]);
                    });
                    if (replaceBtn) replaceBtn.addEventListener('click', function(e) { e.stopPropagation(); input.click(); });
                    window.__catalogUploadUtils && window.__catalogUploadUtils.attachDropZoneState(zone);
                    zone.addEventListener('drop', function(e) {
                        var f = e.dataTransfer.files[0];
                        if (isImage(f)) setCoverFromFile(f);
                    });
                }

                function addGalleryFiles(files) {
                    for (var i = 0; i < files.length; i++) {
                        if (isImage(files[i])) adminGalleryFiles.push(files[i]);
                    }
                    renderPreviews();
                }

                function removeGalleryIndex(i) {
                    adminGalleryFiles.splice(i, 1);
                    renderPreviews();
                }

                function setupGallery() {
                    var zone = document.getElementById('admin-create-gallery-drop-zone');
                    var input = document.getElementById('admin_create_gallery_input');
                    if (!zone || !input) return;
                    zone.addEventListener('click', function() { input.click(); });
                    input.addEventListener('change', function() {
                        if (input.files && input.files.length) {
                            addGalleryFiles(Array.from(input.files));
                            input.value = '';
                        }
                    });
                    window.__catalogUploadUtils && window.__catalogUploadUtils.attachDropZoneState(zone);
                    zone.addEventListener('drop', function(e) {
                        addGalleryFiles(Array.from(e.dataTransfer.files));
                    });
                }

                function renderPreviews() {
                    var container = document.getElementById('admin-create-images-preview');
                    var emptyEl = document.getElementById('admin-create-images-preview-empty');
                    if (!container || !emptyEl) return;
                    var existing = container.querySelectorAll('.admin-create-image-thumb');
                    existing.forEach(function(el) { el.remove(); });
                    emptyEl.style.display = 'block';

                    var coverInput = document.getElementById('admin_create_cover_image');
                    var coverFile = coverInput && coverInput.files && coverInput.files[0];
                    var hasAny = !!coverFile || adminGalleryFiles.length > 0;
                    if (!hasAny) return;

                    function thumb(file, label, onRemove) {
                        var wrap = document.createElement('div');
                        wrap.className = 'admin-create-image-thumb position-relative d-inline-block rounded-2 overflow-hidden shadow-sm';
                        wrap.style.width = '72px'; wrap.style.height = '72px';
                        var img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.alt = '';
                        img.style.width = '100%'; img.style.height = '100%'; img.style.objectFit = 'cover';
                        wrap.appendChild(img);
                        if (label) {
                            var badge = document.createElement('span');
                            badge.className = 'badge bg-primary position-absolute top-0 start-0 m-1';
                            badge.style.fontSize = '9px';
                            badge.textContent = label;
                            wrap.appendChild(badge);
                        }
                        if (onRemove) {
                            var btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'btn btn-danger btn-sm position-absolute bottom-0 end-0 m-1 p-0';
                            btn.style.fontSize = '10px'; btn.style.width = '20px'; btn.style.height = '20px';
                            btn.innerHTML = '&times;';
                            btn.title = removeLabel;
                            btn.addEventListener('click', onRemove);
                            wrap.appendChild(btn);
                        }
                        container.appendChild(wrap);
                        emptyEl.style.display = 'none';
                    }

                    if (coverFile) thumb(coverFile, coverLabel, null);
                    adminGalleryFiles.forEach(function(file, i) {
                        (function(idx) {
                            thumb(file, galleryLabel, function() { removeGalleryIndex(idx); });
                        })(i);
                    });
                }

                function injectGalleryInputs() {
                    var form = document.getElementById('admin-item-create-form');
                    if (!form) return;
                    if (window.__catalogUploadUtils) {
                        window.__catalogUploadUtils.setFileInputs(form, 'gallery_images[]', adminGalleryFiles);
                    }
                }

                function validateAndSubmit(e) {
                    var form = document.getElementById('admin-item-create-form');
                    var coverInput = document.getElementById('admin_create_cover_image');
                    if (!coverInput || !coverInput.files || !coverInput.files[0]) {
                        e.preventDefault();
                        var zone = document.getElementById('admin-create-cover-drop-zone');
                        if (zone) {
                            zone.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            zone.classList.add('upload-drop-zone--invalid');
                            setTimeout(function() { zone.classList.remove('upload-drop-zone--invalid'); }, 2000);
                        }
                        var msg = document.getElementById('admin-create-cover-required-msg');
                        if (msg) msg.classList.remove('d-none');
                        return false;
                    }
                    var msg = document.getElementById('admin-create-cover-required-msg');
                    if (msg) msg.classList.add('d-none');
                    injectGalleryInputs();
                }

                function init() {
                    setupCover();
                    setupGallery();
                    var form = document.getElementById('admin-item-create-form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            validateAndSubmit(e);
                        });
                    }
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', init);
                } else {
                    init();
                }
            })();
        </script>
</x-layouts.admin>
