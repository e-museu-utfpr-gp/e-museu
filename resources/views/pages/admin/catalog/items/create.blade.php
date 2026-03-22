<x-layouts.admin :title="__('view.admin.catalog.items.create.title')"
    :heading="__('view.admin.catalog.items.create.heading')">
            <form action="{{ route('admin.catalog.items.store') }}" method="POST" enctype="multipart/form-data" id="admin-item-create-form">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('view.admin.catalog.items.create.name') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('view.admin.catalog.items.create.description') }}</label>
                            <textarea type="text" class="form-control @error('description') is-invalid @enderror" id="description"
                                name="description" rows="5">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="detail" class="form-label">{{ __('view.admin.catalog.items.create.detail') }}</label>
                            <textarea type="text" class="form-control @error('detail') is-invalid @enderror" id="detail" name="detail"
                                rows="7">{{ old('detail') }}</textarea>
                            @error('detail')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">{{ __('view.admin.catalog.items.create.item_category') }}</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                        name="category_id">
                                        <option selected="selected" value="">-</option>
                                        @foreach ($itemCategories as $itemCategory)
                                            <option value="{{ $itemCategory->id }}"
                                                {{ old('category_id') == $itemCategory->id ? 'selected' : '' }}>{{ $itemCategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="collaborator_id" class="form-label">{{ __('view.admin.catalog.items.create.collaborator') }}</label>
                                    <select class="form-select @error('collaborator_id') is-invalid @enderror"
                                        id="collaborator_id" name="collaborator_id">
                                        <option selected="selected" value="">-</option>
                                        @foreach ($collaborators as $collaborator)
                                            <option value="{{ $collaborator->id }}"
                                                {{ old('collaborator_id') == $collaborator->id ? 'selected' : '' }}>
                                                {{ $collaborator->contact }} - {{ $collaborator->full_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('collaborator_id')
                                        <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="date" class="form-label">{{ __('view.admin.catalog.items.create.date') }}</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                                        id="date" name="date" value="{{ old('date') }}">
                                    @error('date')
                                        <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('view.admin.catalog.items.edit.cover_image') }}</label>
                                    <div id="admin-create-cover-drop-zone" class="upload-drop-zone rounded-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center p-3 text-center" style="min-height: 120px; border-color: #c8e6c9; background: #f1f8e9; cursor: pointer;">
                                        <input type="file" name="cover_image" id="admin_create_cover_image" accept="image/jpeg,image/png,image/jpg,image/webp" class="d-none">
                                        <div id="admin-create-cover-placeholder">
                                            <i class="bi bi-image text-secondary mb-2" style="font-size: 1.5rem;"></i>
                                            <p class="text-muted small mb-0">{{ __('view.catalog.items.create.cover_drop_here') }}</p>
                                        </div>
                                        <div id="admin-create-cover-preview" class="d-none position-relative">
                                            <img id="admin-create-cover-preview-img" src="" alt="" class="rounded shadow-sm" style="max-height: 100px; max-width: 100%; object-fit: contain;">
                                            <span class="badge bg-primary position-absolute top-0 start-0 m-1">{{ __('app.catalog.item_image.cover') }}</span>
                                            <x-ui.buttons.default type="button" variant="outline-secondary" size="sm"
                                                class="position-absolute bottom-0 end-0 m-1" id="admin-create-cover-replace-btn">{{ __('view.catalog.items.create.replace_image') }}</x-ui.buttons.default>
                                        </div>
                                    </div>
                                    <p id="admin-create-cover-required-msg" class="text-danger small mt-1 d-none">{{ __('view.catalog.items.create.cover_required') }}</p>
                                    @error('cover_image')
                                        <div class="invalid-feedback d-block"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('view.admin.catalog.items.edit.gallery_images') }}</label>
                                    <div id="admin-create-gallery-drop-zone" class="upload-drop-zone rounded-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center p-3 text-center" style="min-height: 90px; border-color: #c8e6c9; background: #f1f8e9; cursor: pointer;">
                                        <input type="file" name="gallery_images[]" id="admin_create_gallery_input" accept="image/jpeg,image/png,image/jpg,image/webp" class="d-none" multiple>
                                        <i class="bi bi-images text-secondary mb-2" style="font-size: 1.25rem;"></i>
                                        <p class="text-muted small mb-0">{{ __('view.catalog.items.create.gallery_drop_here') }}</p>
                                    </div>
                                    @error('gallery_images.*')
                                        <div class="invalid-feedback d-block"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="mb-3 rounded-3 p-3" style="background-color: #e8f5e9;">
                                    <h6 class="mb-2">{{ __('view.catalog.items.create.images_preview_title') }}</h6>
                                    <div id="admin-create-images-preview" class="d-flex flex-wrap gap-2 align-items-start">
                                        <p class="text-muted small mb-0" id="admin-create-images-preview-empty">{{ __('view.catalog.items.create.images_preview_empty') }}</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="validation" class="form-label">{{ __('view.admin.catalog.items.create.validation') }}</label>
                                    <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                                        name="validation">
                                        <option value="0" {{ old('validation') == 0 ? 'selected' : '' }}>{{ __('view.admin.catalog.items.create.no') }}</option>
                                        <option value="1" {{ old('validation') == 1 ? 'selected' : '' }}>{{ __('view.admin.catalog.items.create.yes') }}</option>
                                    </select>
                                    @error('validation')
                                        <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="history" class="form-label">{{ __('view.admin.catalog.items.create.history') }}</label>
                            <textarea type="text" class="form-control @error('history') is-invalid @enderror" id="history" name="history"
                                rows="46">{{ old('history') }}</textarea>
                            @error('history')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">{{ __('view.admin.catalog.items.create.submit') }}</x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
            </form>
        <script>
            (function() {
                var coverLabel = @json(__('app.catalog.item_image.cover'));
                var galleryLabel = @json(__('app.catalog.item_image.gallery'));
                var removeLabel = @json(__('view.catalog.items.create.remove_image'));
                var adminGalleryFiles = [];

                function isImage(file) { return file && file.type && file.type.indexOf('image/') === 0; }

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
                    ['dragenter', 'dragover'].forEach(function(ev) {
                        zone.addEventListener(ev, function(e) { e.preventDefault(); zone.classList.add('upload-drop-zone--over'); });
                    });
                    ['dragleave', 'drop'].forEach(function(ev) {
                        zone.addEventListener(ev, function(e) { e.preventDefault(); zone.classList.remove('upload-drop-zone--over'); });
                    });
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
                    ['dragenter', 'dragover'].forEach(function(ev) {
                        zone.addEventListener(ev, function(e) { e.preventDefault(); zone.classList.add('upload-drop-zone--over'); });
                    });
                    ['dragleave', 'drop'].forEach(function(ev) {
                        zone.addEventListener(ev, function(e) { e.preventDefault(); zone.classList.remove('upload-drop-zone--over'); });
                    });
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
                    form.querySelectorAll('input[name="gallery_images[]"]').forEach(function(inp) { inp.remove(); });
                    adminGalleryFiles.forEach(function(file) {
                        if (typeof DataTransfer === 'undefined') return;
                        var inp = document.createElement('input');
                        inp.type = 'file';
                        inp.name = 'gallery_images[]';
                        inp.className = 'd-none';
                        var dt = new DataTransfer();
                        dt.items.add(file);
                        inp.files = dt.files;
                        form.appendChild(inp);
                    });
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
        <style>
            .upload-drop-zone--over { border-color: #81c784 !important; background: #c8e6c9 !important; }
            .upload-drop-zone:hover { border-color: #a5d6a7 !important; }
            .upload-drop-zone--invalid { border-color: #e57373 !important; background: #ffebee !important; }
        </style>
</x-layouts.admin>
