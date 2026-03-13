@extends('layouts.app')
@section('title', __('view.catalog.items.create.title'))

@section('content')
    <div class="container main-container mb-auto">
        <h1>{{ __('view.catalog.items.create.heading') }}</h1>
        <p class="ms-4 fw-bold">{{ __('view.catalog.items.create.intro') }}</p>
        <div class="ms-4 mb-4">
            @foreach ($errors->all() as $error)
                <p class="error-div text-wrap fw-bold m-1 p-1"><i class="bi bi-exclamation-circle-fill mx-1 h5"></i>
                    {{ $error }}</p>
            @endforeach
            @if (session('success'))
                <div class="success-div text-wrap fw-bold m-1 p-1">
                    {{ session('success') }}
                </div>
            @endif
        </div>
        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" id="item-create-form">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div>
                        <label for="name">
                            <h5>{{ __('view.catalog.items.create.name_label') }}
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="{{ __('view.catalog.items.create.name_help') }}">
                                    <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div">
                            <input class="form-control me-2 input-form  @error('name') is-invalid @enderror" type="text"
                                name="name" id="name" autocomplete="off" placeholder="" value="{{ old('name') }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="category_id">
                                <h5>{{ __('view.catalog.items.create.category_label') }}
                                    <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                        data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                        data-bs-content="{{ __('view.catalog.items.create.category_help') }}">
                                        <i class="bi bi-info-circle-fill h4 ms-1"
                                            style="color: #ED6E38; cursor: pointer;"></i>
                                    </button>
                                </h5>
                            </label>
                            <div class="input-div rounded-top">
                                <select required class="form-select me-2 input-form  @error('category_id') is-invalid @enderror"
                                    name="category_id" id="category_id">
                                    <option selected="selected" value="">-</option>
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}"
                                            {{ old('category_id') == $itemCategory->id ? 'selected' : '' }}>{{ $itemCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="date">
                                <h5>{{ __('view.catalog.items.create.release_date') }}
                                    <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                        data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                        data-bs-content="{{ __('view.catalog.items.create.release_date_help') }}">
                                        <i class="bi bi-info-circle-fill h4 ms-1"
                                            style="color: #ED6E38; cursor: pointer;"></i>
                                    </button>
                                </h5>
                            </label>
                            <div class="input-div">
                                <input class="form-control me-2 input-form  @error('date') is-invalid @enderror"
                                    type="date" name="date" placeholder="" value="{{ old('date') }}">
                                @error('date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="description">
                            <h5>{{ __('view.catalog.items.create.short_description') }}
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="{{ __('view.catalog.items.create.short_description_help') }}">
                                    <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div">
                            <textarea class="form-control me-2 input-form  @error('description') is-invalid @enderror" type="text"
                                name="description" placeholder="" rows="6" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="detail">
                            <h5>{{ __('view.catalog.items.create.technical_details') }}
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="{{ __('view.catalog.items.create.technical_details_help') }}">
                                    <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div">
                            <textarea class="form-control me-2 input-form  @error('detail') is-invalid @enderror" type="text" name="detail"
                                placeholder="" rows="6">{{ old('detail') }}</textarea>
                            @error('detail')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="history">
                            <h5>{{ __('view.catalog.items.create.history_label') }}
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="{{ __('view.catalog.items.create.history_help') }}">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div">
                            <textarea class="form-control me-2 input-form  @error('history') is-invalid @enderror" type="text" name="history"
                                placeholder="" rows="24">{{ old('history') }}</textarea>
                            @error('history')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <h5 class="mb-2">{{ __('view.catalog.items.create.cover_label') }} <span class="text-danger">*</span>
                            <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                data-bs-content="{{ __('view.catalog.items.create.cover_help') }}">
                                <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                            </button>
                        </h5>
                        <div id="cover-drop-zone" class="upload-drop-zone rounded-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center p-4 text-center" style="min-height: 140px; border-color: #c8e6c9; background: #f1f8e9; cursor: pointer;">
                            <input type="file" name="cover_image" id="cover_image" accept="image/jpeg,image/png,image/jpg,image/webp" class="d-none">
                            <div id="cover-placeholder">
                                <i class="bi bi-image text-secondary mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted small mb-0">{{ __('view.catalog.items.create.cover_drop_here') }}</p>
                            </div>
                            <div id="cover-preview" class="d-none position-relative">
                                <img id="cover-preview-img" src="" alt="" class="rounded shadow-sm" style="max-height: 120px; max-width: 100%; object-fit: contain;">
                                <span class="badge bg-primary position-absolute top-0 start-0 m-1">{{ __('app.catalog.item_image.cover') }}</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary position-absolute bottom-0 end-0 m-1" id="cover-replace-btn">{{ __('view.catalog.items.create.replace_image') }}</button>
                            </div>
                        </div>
                        <p id="cover-required-msg" class="text-danger small mt-1 d-none">{{ __('view.catalog.items.create.cover_required') }}</p>
                        @error('cover_image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <h5 class="mb-2">{{ __('view.catalog.items.create.gallery_label') }}
                            <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                data-bs-content="{{ __('view.catalog.items.create.gallery_help') }}">
                                <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                            </button>
                        </h5>
                        <div id="gallery-drop-zone" class="upload-drop-zone rounded-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center p-4 text-center" style="min-height: 100px; border-color: #c8e6c9; background: #f1f8e9; cursor: pointer;">
                            <input type="file" name="gallery_images[]" id="gallery_input" accept="image/jpeg,image/png,image/jpg,image/webp" class="d-none" multiple>
                            <i class="bi bi-images text-secondary mb-2" style="font-size: 1.5rem;"></i>
                            <p class="text-muted small mb-0">{{ __('view.catalog.items.create.gallery_drop_here') }}</p>
                        </div>
                        @error('gallery_images.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4 rounded-3 p-3 mt-3" style="background-color: #e8f5e9;">
                        <h5 class="mb-3">{{ __('view.catalog.items.create.images_preview_title') }}</h5>
                        <div id="images-preview" class="d-flex flex-wrap gap-3 align-items-start">
                            <p class="text-muted mb-0" id="images-preview-empty">{{ __('view.catalog.items.create.images_preview_empty') }}</p>
                        </div>
                    </div>
                    <div>
                        <label for="e-mail">
                            <h5>{{ __('view.catalog.items.create.email_label') }}
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="{{ __('view.catalog.items.create.email_help') }}">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div">
                            <input class="form-control me-2 input-form  @error('contact') is-invalid @enderror"
                                type="email" name="contact" id="contact" placeholder=""
                                value="{{ old('contact') }}"
                                required>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="warning-div px-1 mx-5 mb-3" id="contact-warning" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create.contact_not_found') }}
                        </div>
                        <div class="success-div px-1 mx-5 mb-3" id="contact-success" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create.contact_found') }}
                        </div>
                    </div>
                    <div>
                        <label for="full_name">
                            <h5>{{ __('view.catalog.items.create.full_name_label') }}
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="{{ __('view.catalog.items.create.full_name_help') }}">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div">
                            <input class="form-control me-2 input-form  @error('full_name') is-invalid @enderror"
                                type="text" name="full_name" id="full_name" placeholder=""
                                value="{{ old('full_name') }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <div class="d-flex justify-content-between">
                            <h5>{{ __('view.catalog.items.create.tags_label') }}
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="{{ __('view.catalog.items.create.tags_help') }}">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                            <h4 class="me-2" id="tag-count-text">0/10</h4>
                        </div>
                        <div class="tagContainer mb-4">
                            <div class="tags ms-3" id="tags">
                                <p class="text-center p-1 empty-text" id="tag-empty-text">{{ __('view.catalog.items.create.tags_empty') }}</p>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <div class="warning-div px-1 mx-5 mb-3" id="tag-full-text" hidden>
                                    <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create.tags_limit') }}
                                </div>
                                <button class="button nav-link px-2 pb-2" data-bs-toggle="modal"
                                    data-bs-target="#addTagModal" id="add-tag-button" type="button">
                                    <i class="bi bi-plus h3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between">
                            <h5>{{ __('view.catalog.items.create.extra_label') }}
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="{{ __('view.catalog.items.create.extra_help') }}">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                            <h4 class="me-2" id="extra-count-text">0/10</h4>
                        </div>
                        <div class="extraContainer mb-4">
                            <div class="extras ms-3" id="extras">
                                <p class="text-center p-1 empty-text" id="extra-empty-text">{{ __('view.catalog.items.create.extra_empty') }}</p>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <div class="warning-div px-1 mx-5 mb-3" id="extra-full-text" hidden>
                                    <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create.extra_limit') }}
                                </div>
                                <button class="button nav-link px-2 pb-2" data-bs-toggle="modal"
                                    data-bs-target="#addExtraModal" id="add-extra-button" type="button">
                                    <i class="bi bi-plus h3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between">
                            <h5>{{ __('view.catalog.items.create.components_label') }}
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="{{ __('view.catalog.items.create.components_help') }}">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                            <h4 class="me-2" id="component-count-text">0/10</h4>
                        </div>
                        <div class="componentContainer mb-4">
                            <div class="components ms-3" id="components">
                                <p class="text-center p-1 empty-text" id="component-empty-text">{{ __('view.catalog.items.create.components_empty') }}</p>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <div class="warning-div px-1 mx-5 mb-3" id="component-full-text" hidden>
                                    <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create.components_limit') }}
                                </div>
                                <button class="button nav-link px-2 pb-2" data-bs-toggle="modal"
                                    data-bs-target="#addComponentModal" type="button" id="add-component-button">
                                    <i class="bi bi-plus h3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col d-flex align-items-center justify-content-end">
                        <button class="button nav-link py-2 px-3 fw-bold" type="submit">{{ __('view.catalog.items.create.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @php
        $createModalsI18n = [
            'component' => [
                'alert_category_required' => __('view.catalog.items.create_modals.component.alert_category_required'),
                'alert_name_required' => __('view.catalog.items.create_modals.component.alert_name_required'),
            ],
            'extra' => [
                'alert_required' => __('view.catalog.items.create_modals.extra.alert_required'),
            ],
            'tag' => [
                'alert_category_required' => __('view.catalog.items.create_modals.tag.alert_category_required'),
                'alert_name_required' => __('view.catalog.items.create_modals.tag.alert_name_required'),
            ],
        ];
    @endphp
    <script type="text/javascript">
        window.createModalsI18n = @json($createModalsI18n);
    </script>
    @include('catalog.items.create-modals.component-modal')

    @include('catalog.items.create-modals.extra-modal')

    @include('catalog.items.create-modals.tag-modal')

    <script type="text/javascript">
        (function() {
            var coverLabel = @json(__('app.catalog.item_image.cover'));
            var galleryLabel = @json(__('app.catalog.item_image.gallery'));
            var removeLabel = @json(__('view.catalog.items.create.remove_image'));
            var acceptTypes = 'image/jpeg,image/png,image/jpg,image/webp';

            var galleryFiles = [];

            function isImage(file) { return file && file.type && file.type.indexOf('image/') === 0; }

            function setCoverFromFile(file) {
                if (!isImage(file)) return;
                var input = document.getElementById('cover_image');
                if (input && typeof DataTransfer !== 'undefined') {
                    var dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                }
                var placeholder = document.getElementById('cover-placeholder');
                var preview = document.getElementById('cover-preview');
                var img = document.getElementById('cover-preview-img');
                if (placeholder && preview && img) {
                    placeholder.classList.add('d-none');
                    preview.classList.remove('d-none');
                    img.src = URL.createObjectURL(file);
                }
                renderPreviews();
            }

            function setupCover() {
                var zone = document.getElementById('cover-drop-zone');
                var input = document.getElementById('cover_image');
                var replaceBtn = document.getElementById('cover-replace-btn');
                if (!zone || !input) return;
                zone.addEventListener('click', function(e) { if (!e.target.closest('#cover-replace-btn')) input.click(); });
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
                    if (isImage(files[i])) galleryFiles.push(files[i]);
                }
                renderPreviews();
            }

            function removeGalleryIndex(i) {
                galleryFiles.splice(i, 1);
                renderPreviews();
            }

            function setupGallery() {
                var zone = document.getElementById('gallery-drop-zone');
                var input = document.getElementById('gallery_input');
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
                var container = document.getElementById('images-preview');
                var emptyEl = document.getElementById('images-preview-empty');
                if (!container || !emptyEl) return;
                var existing = container.querySelectorAll('.image-preview-thumb');
                existing.forEach(function(el) { el.remove(); });
                emptyEl.style.display = 'block';

                var coverInput = document.getElementById('cover_image');
                var coverFile = coverInput && coverInput.files && coverInput.files[0];
                var hasAny = !!coverFile || galleryFiles.length > 0;
                if (!hasAny) return;

                function thumb(file, label, onRemove) {
                    var wrap = document.createElement('div');
                    wrap.className = 'image-preview-thumb position-relative d-inline-block rounded-2 overflow-hidden shadow-sm';
                    wrap.style.width = '88px'; wrap.style.height = '88px';
                    var img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.alt = '';
                    img.style.width = '100%'; img.style.height = '100%'; img.style.objectFit = 'cover';
                    wrap.appendChild(img);
                    if (label) {
                        var badge = document.createElement('span');
                        badge.className = 'badge bg-primary position-absolute top-0 start-0 m-1';
                        badge.style.fontSize = '10px';
                        badge.textContent = label;
                        wrap.appendChild(badge);
                    }
                    if (onRemove) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-danger btn-sm position-absolute bottom-0 end-0 m-1 p-1';
                        btn.style.fontSize = '10px';
                        btn.innerHTML = '&times;';
                        btn.title = removeLabel;
                        btn.addEventListener('click', onRemove);
                        wrap.appendChild(btn);
                    }
                    container.appendChild(wrap);
                    emptyEl.style.display = 'none';
                }

                if (coverFile) thumb(coverFile, coverLabel, null);
                galleryFiles.forEach(function(file, i) {
                    (function(idx) {
                        thumb(file, galleryLabel, function() { removeGalleryIndex(idx); });
                    })(i);
                });
            }

            function injectGalleryInputs() {
                var form = document.getElementById('item-create-form');
                if (!form) return;
                form.querySelectorAll('input[name="gallery_images[]"]').forEach(function(inp) { inp.remove(); });
                galleryFiles.forEach(function(file) {
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
                var form = document.getElementById('item-create-form');
                var coverInput = document.getElementById('cover_image');
                if (!coverInput || !coverInput.files || !coverInput.files[0]) {
                    e.preventDefault();
                    var zone = document.getElementById('cover-drop-zone');
                    if (zone) {
                        zone.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        zone.classList.add('upload-drop-zone--invalid');
                        setTimeout(function() { zone.classList.remove('upload-drop-zone--invalid'); }, 2000);
                    }
                    var msg = document.getElementById('cover-required-msg');
                    if (msg) msg.classList.remove('d-none');
                    return false;
                }
                var msg = document.getElementById('cover-required-msg');
                if (msg) msg.classList.add('d-none');
                injectGalleryInputs();
            }

            function init() {
                setupCover();
                setupGallery();
                var form = document.getElementById('item-create-form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        validateAndSubmit(e);
                    });
                }
            }
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
            else init();
        })();
    </script>
    <style>
        .upload-drop-zone--over { border-color: #81c784 !important; background: #c8e6c9 !important; }
        .upload-drop-zone:hover { border-color: #a5d6a7 !important; }
        .upload-drop-zone--invalid { border-color: #e57373 !important; background: #ffebee !important; }
    </style>
    <script type="text/javascript">
        // Disponibiliza a rota para o componente checkContact.js
        window.checkContactRoute = "{{ route('check-contact') }}";

        (function() {
            function init() {
                if (typeof window.$ === 'undefined' || typeof window.jQuery === 'undefined') {
                    setTimeout(init, 50);
                    return;
                }
                
                function getSessionStorage() {
                    tagCount = parseInt(sessionStorage.getItem("tagCount"));
                    extraCount = parseInt(sessionStorage.getItem("extraCount"));
                    componentCount = parseInt(sessionStorage.getItem("componentCount"));

                    if (tagCount > 0) {
                        for (let i = 0; i < tagCount; i++) {
                            let tagCategoryText = sessionStorage.getItem("tag" + tagIds + "categoryText");
                            let tagCategoryVal = sessionStorage.getItem("tag" + tagIds + "categoryVal");
                            let tagName = sessionStorage.getItem("tag" + tagIds + "name");

                            tagBuilder(tagCategoryText, tagCategoryVal, tagName, tagIds);

                            tagIds++;
                        }
                        checkTags();
                    }

                    if (extraCount > 0) {
                        for (let i = 0; i < extraCount; i++) {
                            let extraInfo = sessionStorage.getItem("extra" + extraIds + "info");

                            extraBuilder(extraInfo, extraIds);

                            extraIds++;
                        }
                        checkExtras();
                    }

                    if (componentCount > 0) {
                        for (let i = 0; i < componentCount; i++) {
                            let componentCategoryText = sessionStorage.getItem("component" + componentIds + "categoryText");
                            let componentCategoryVal = sessionStorage.getItem("component" + componentIds + "categoryVal");
                            let componentName = sessionStorage.getItem("component" + componentIds + "name");

                            componentBuilder(componentCategoryText, componentCategoryVal, componentName, componentIds);

                            componentIds++;
                        }
                        checkComponents();
                    }
                }

                $(document).ready(function() {
                    @if (session()->has('success'))
                        sessionStorage.clear();
                    @endif

                    if (sessionStorage.getItem("itemCreateForm") === null) {
                        return;
                    } else {
                        @if (session()->has('errors'))
                            getSessionStorage();
                            return;
                        @endif

                        if (confirm(
                                @json(__('view.catalog.items.create.recover_confirm'))
                            )) {
                            getSessionStorage();
                        }
                    }
                });
            }
            init();
        })();
    </script>

@endsection
