<x-layouts.admin :title="__('view.admin.catalog.items.edit.title') . ' ' . $item->id">
    <div class="mb-auto container-fluid">
        <x-ui.flash-messages />
        <form action="{{ route('admin.items.update', $item->id) }}" method="POST" enctype="multipart/form-data" id="admin-item-edit-form">
            @csrf
            @method('PATCH')
            <div id="admin-delete-image-ids"></div>
            <input type="hidden" name="set_cover_image_id" id="set_cover_image_id" value="">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">{{ __('view.admin.catalog.items.edit.heading', ['id' => $item->id, 'name' => $item->name]) }}</h2>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('view.admin.catalog.items.edit.name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ $item->name }}">
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('view.admin.catalog.items.edit.description') }}</label>
                        <textarea type="text" class="form-control @error('description') is-invalid @enderror" id="description"
                            name="description" rows="5">{{ $item->description }}</textarea>
                        @error('description')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="detail" class="form-label">{{ __('view.admin.catalog.items.edit.detail') }}</label>
                        <textarea type="text" class="form-control @error('detail') is-invalid @enderror" id="detail" name="detail"
                            rows="7">{{ $item->detail }}</textarea>
                        @error('detail')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">{{ __('view.admin.catalog.items.edit.item_category') }}</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id">
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}"
                                            @if ($item->category_id == $itemCategory->id) selected @endif>{{ $itemCategory->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="collaborator_id" class="form-label">{{ __('view.admin.catalog.items.edit.collaborator') }}</label>
                                <select class="form-select @error('collaborator_id') is-invalid @enderror"
                                    id="collaborator_id" name="collaborator_id">
                                    @foreach ($collaborators as $collaborator)
                                        <option value="{{ $collaborator->id }}"
                                            @if ($item->collaborator_id == $collaborator->id) selected @endif>{{ $collaborator->contact }} -
                                            {{ $collaborator->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('collaborator_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">{{ __('view.admin.catalog.items.edit.date') }}</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                    id="date" name="date" value="{{ $item->date?->format('Y-m-d') }}">
                                @error('date')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="identification_code" class="form-label">{{ __('view.admin.catalog.items.edit.identification_code') }}</label>
                                <input type="text"
                                    class="form-control @error('identification_code') is-invalid @enderror"
                                    id="identification_code" name="identification_code"
                                    value="{{ $item->identification_code }}">
                                @error('identification_code')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="validation" class="form-label">{{ __('view.admin.catalog.items.edit.validation') }}</label>
                                <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                                    name="validation">
                                    <option value="0" @if ($item->validation == 0) selected @endif>{{ __('view.admin.catalog.items.edit.no') }}</option>
                                    <option value="1" @if ($item->validation == 1) selected @endif>{{ __('view.admin.catalog.items.edit.yes') }}</option>
                                </select>
                                @error('validation')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            @php
                                $currentCoverImage = $item->coverImage ?? $item->images->sortBy('sort_order')->first();
                            @endphp
                            <div class="mb-3">
                                <label class="form-label">{{ __('view.admin.catalog.items.edit.cover_image') }}</label>
                                <div id="admin-cover-drop-zone" class="upload-drop-zone rounded-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center p-3 text-center" style="min-height: 120px; border-color: #c8e6c9; background: #f1f8e9; cursor: pointer;">
                                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/webp" class="d-none">
                                    <div id="admin-cover-placeholder" @if ($currentCoverImage) class="d-none" @endif>
                                        <i class="bi bi-image text-secondary mb-2" style="font-size: 1.5rem;"></i>
                                        <p class="text-muted small mb-0">{{ __('view.catalog.items.create.cover_drop_here') }}</p>
                                    </div>
                                    <div id="admin-cover-preview" class="position-relative @if (!$currentCoverImage) d-none @endif">
                                        <img id="admin-cover-preview-img" src="{{ $currentCoverImage?->image_url ?? '' }}" alt="" class="rounded shadow-sm" style="max-height: 100px; max-width: 100%; object-fit: contain;">
                                        <span class="badge bg-primary position-absolute top-0 start-0 m-1">{{ __('app.catalog.item_image.cover') }}</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary position-absolute bottom-0 end-0 m-1" id="admin-cover-replace-btn">{{ __('view.catalog.items.create.replace_image') }}</button>
                                    </div>
                                </div>
                                @error('image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('view.admin.catalog.items.edit.gallery_images') }}</label>
                                <div id="admin-gallery-drop-zone" class="upload-drop-zone rounded-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center p-3 text-center" style="min-height: 90px; border-color: #c8e6c9; background: #f1f8e9; cursor: pointer;">
                                    <input type="file" name="gallery_images[]" id="admin_gallery_input" accept="image/jpeg,image/png,image/jpg,image/webp" class="d-none" multiple>
                                    <i class="bi bi-images text-secondary mb-2" style="font-size: 1.25rem;"></i>
                                    <p class="text-muted small mb-0">{{ __('view.catalog.items.create.gallery_drop_here') }}</p>
                                </div>
                                @error('gallery_images.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 rounded-3 p-3" style="background-color: #e8f5e9;">
                                <h6 class="mb-2">{{ __('view.admin.catalog.items.edit.current_images') }}</h6>
                                <div id="admin-images-preview" class="d-flex flex-wrap gap-2 align-items-start">
                                    @if ($item->images->isEmpty())
                                        <p class="text-muted small mb-0">{{ __('view.admin.catalog.items.show.no_images') }}</p>
                                    @else
                                        @php
                                            $sortedImages = $item->images->sortBy('sort_order')->values();
                                            $coverShown = false;
                                        @endphp
                                        @foreach ($sortedImages as $img)
                                            @php
                                                $showAsCover = !$coverShown && $img->type->value === 'cover';
                                                if ($showAsCover) { $coverShown = true; }
                                            @endphp
                                            <div class="position-relative d-inline-block admin-preview-thumb" @if ($showAsCover) id="admin-current-cover-thumb" @endif data-image-id="{{ $img->id }}">
                                                <img src="{{ $img->image_url }}" class="img-thumbnail clickable-image" alt="" style="width: 72px; height: 72px; object-fit: cover;">
                                                <span class="badge bg-{{ $showAsCover ? 'primary' : 'secondary' }} position-absolute top-0 start-0 m-1" style="font-size: 9px;">{{ $showAsCover ? __('app.catalog.item_image.cover') : __('app.catalog.item_image.gallery') }}</span>
                                                @if (!$showAsCover)
                                                    <button type="button" class="btn btn-outline-primary btn-sm position-absolute top-0 end-0 m-1 p-0 set-cover-btn" style="width: 22px; height: 22px; font-size: 11px; line-height: 1;" title="{{ __('view.admin.catalog.items.edit.set_as_cover') }}" data-image-id="{{ $img->id }}"><i class="bi bi-image-fill"></i></button>
                                                @endif
                                                <button type="button" class="btn btn-danger btn-sm position-absolute bottom-0 end-0 m-1 p-0 delete-image-btn" style="width: 22px; height: 22px; font-size: 12px; line-height: 1;" title="{{ __('view.admin.catalog.items.edit.delete_image') }}"
                                                    data-image-id="{{ $img->id }}"
                                                    data-confirm="{{ __('view.admin.catalog.items.edit.delete_image_confirm') }}"><i class="bi bi-trash"></i></button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="history" class="form-label">{{ __('view.admin.catalog.items.edit.history') }}</label>
                        <textarea type="text" class="form-control @error('history') is-invalid @enderror" id="history" name="history"
                            rows="46">{{ $item->history }}</textarea>
                        @error('history')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-pencil-fill"></i> {{ __('view.admin.catalog.items.edit.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <x-ui.image-modal />
    <x-release-lock-on-leave type="items" :id="$item->id" />

    <script>
        (function() {
            var coverLabel = @json(__('app.catalog.item_image.cover'));
            var galleryLabel = @json(__('app.catalog.item_image.gallery'));
            var adminGalleryFiles = [];

            function isImage(file) { return file && file.type && file.type.indexOf('image/') === 0; }

            function setAdminCoverFromFile(file) {
                if (!isImage(file)) return;
                var input = document.getElementById('image');
                if (input && typeof DataTransfer !== 'undefined') {
                    var dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                }
                var placeholder = document.getElementById('admin-cover-placeholder');
                var preview = document.getElementById('admin-cover-preview');
                var img = document.getElementById('admin-cover-preview-img');
                if (placeholder && preview && img) {
                    placeholder.classList.add('d-none');
                    preview.classList.remove('d-none');
                    img.src = URL.createObjectURL(file);
                }
                renderAdminPreviews();
            }

            function setupAdminCover() {
                var zone = document.getElementById('admin-cover-drop-zone');
                var input = document.getElementById('image');
                var replaceBtn = document.getElementById('admin-cover-replace-btn');
                if (!zone || !input) return;
                zone.addEventListener('click', function(e) { if (!e.target.closest('#admin-cover-replace-btn')) input.click(); });
                input.addEventListener('change', function() {
                    if (input.files && input.files[0]) setAdminCoverFromFile(input.files[0]);
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
                    if (isImage(f)) setAdminCoverFromFile(f);
                });
            }

            function addAdminGalleryFiles(files) {
                for (var i = 0; i < files.length; i++) {
                    if (isImage(files[i])) adminGalleryFiles.push(files[i]);
                }
                renderAdminPreviews();
            }

            function removeAdminGalleryIndex(i) {
                adminGalleryFiles.splice(i, 1);
                renderAdminPreviews();
            }

            function setupAdminGallery() {
                var zone = document.getElementById('admin-gallery-drop-zone');
                var input = document.getElementById('admin_gallery_input');
                if (!zone || !input) return;
                zone.addEventListener('click', function() { input.click(); });
                input.addEventListener('change', function() {
                    if (input.files && input.files.length) {
                        addAdminGalleryFiles(Array.from(input.files));
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
                    addAdminGalleryFiles(Array.from(e.dataTransfer.files));
                });
            }

            function renderAdminPreviews() {
                var container = document.getElementById('admin-images-preview');
                if (!container) return;
                var coverInput = document.getElementById('image');
                var hasNewCover = coverInput && coverInput.files && coverInput.files[0];
                var currentCoverEl = document.getElementById('admin-current-cover-thumb');
                if (currentCoverEl) {
                    currentCoverEl.style.display = hasNewCover ? 'none' : '';
                }
                var emptyP = container.querySelector('.text-muted.small');
                var newThumbs = container.querySelectorAll('.admin-image-preview-new');
                newThumbs.forEach(function(el) { el.remove(); });
                function addNewThumb(file, label, onRemove) {
                    var wrap = document.createElement('div');
                    wrap.className = 'image-preview-thumb admin-image-preview-new position-relative d-inline-block rounded-2 overflow-hidden shadow-sm';
                    wrap.style.width = '72px'; wrap.style.height = '72px';
                    var img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.alt = '';
                    img.style.width = '100%'; img.style.height = '100%'; img.style.objectFit = 'cover';
                    wrap.appendChild(img);
                    if (label) {
                        var badge = document.createElement('span');
                        badge.className = 'badge bg-info position-absolute top-0 start-0 m-1';
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
                        btn.addEventListener('click', onRemove);
                        wrap.appendChild(btn);
                    }
                    if (emptyP) emptyP.classList.add('d-none');
                    if (label === coverLabel) {
                        container.insertBefore(wrap, container.firstChild);
                    } else {
                        container.appendChild(wrap);
                    }
                }
                if (hasNewCover) addNewThumb(coverInput.files[0], coverLabel, null);
                adminGalleryFiles.forEach(function(file, i) {
                    (function(idx) {
                        addNewThumb(file, galleryLabel, function() { removeAdminGalleryIndex(idx); });
                    })(i);
                });
            }

            function injectAdminGalleryInputs() {
                var form = document.querySelector('form[action*="admin/catalog/items"]');
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

            document.getElementById('admin-images-preview') && document.getElementById('admin-images-preview').addEventListener('click', function(e) {
                var deleteBtn = e.target.closest('.delete-image-btn');
                if (deleteBtn && deleteBtn.getAttribute('data-image-id')) {
                    if (!confirm(deleteBtn.getAttribute('data-confirm'))) return;
                    var imageId = deleteBtn.getAttribute('data-image-id');
                    var thumb = deleteBtn.closest('.admin-preview-thumb, .admin-image-preview-new');
                    if (thumb) thumb.remove();
                    var container = document.getElementById('admin-delete-image-ids');
                    if (container) {
                        var inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = 'delete_image_ids[]';
                        inp.value = imageId;
                        container.appendChild(inp);
                    }
                    document.getElementById('set_cover_image_id').value = '';
                    return;
                }
                var setCoverBtn = e.target.closest('.set-cover-btn');
                if (setCoverBtn && setCoverBtn.getAttribute('data-image-id')) {
                    var imageId = setCoverBtn.getAttribute('data-image-id');
                    document.getElementById('set_cover_image_id').value = imageId;
                    var coverInput = document.getElementById('image');
                    if (coverInput && typeof DataTransfer !== 'undefined') { coverInput.files = new DataTransfer().files; }
                    var thumb = setCoverBtn.closest('.admin-preview-thumb');
                    var img = thumb && thumb.querySelector('img');
                    var preview = document.getElementById('admin-cover-preview');
                    var previewImg = document.getElementById('admin-cover-preview-img');
                    var placeholder = document.getElementById('admin-cover-placeholder');
                    if (preview && previewImg && img && img.src) {
                        placeholder.classList.add('d-none');
                        preview.classList.remove('d-none');
                        previewImg.src = img.src;
                    }
                    renderAdminPreviews();
                }
            });

            setupAdminCover();
            setupAdminGallery();
            var form = document.querySelector('form[action*="admin/catalog/items"]');
            if (form) form.addEventListener('submit', injectAdminGalleryInputs);
        })();
    </script>
    <style>
        .upload-drop-zone--over { border-color: #81c784 !important; background: #c8e6c9 !important; }
        .upload-drop-zone:hover { border-color: #a5d6a7 !important; }
    </style>
</x-layouts.admin>
