<x-layouts.admin :title="__('view.admin.catalog.items.edit.title') . ' ' . $item->
    id" :heading="__('view.admin.catalog.items.edit.heading', ['id' => $item->id, 'name' => $item->name])">
            <form action="{{ route('admin.catalog.items.update', $item->id) }}" method="POST" enctype="multipart/form-data" id="admin-item-edit-form">
                @csrf
                @method('PATCH')
                <div id="admin-delete-image-ids"></div>
                <input type="hidden" name="set_cover_image_id" id="set_cover_image_id" value="">
                <div class="row">
                    <div class="col-md-6">
                        <x-ui.inputs.admin.text
                            name="name"
                            id="name"
                            :label="__('view.admin.catalog.items.edit.name')"
                            :value="$item->name"
                        />
                        <x-ui.inputs.admin.textarea
                            name="description"
                            id="description"
                            :rows="5"
                            :label="__('view.admin.catalog.items.edit.description')"
                            :value="$item->description"
                        />
                        <x-ui.inputs.admin.textarea
                            name="detail"
                            id="detail"
                            :rows="7"
                            :label="__('view.admin.catalog.items.edit.detail')"
                            :value="$item->detail"
                        />
                        <div class="row">
                            <div class="col-md-6">
                                <x-ui.inputs.admin.select
                                    name="category_id"
                                    id="category_id"
                                    :label="__('view.admin.catalog.items.edit.item_category')"
                                    required
                                >
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}" @selected(old('category_id', $item->category_id) == $itemCategory->id)>
                                            {{ $itemCategory->name }}
                                        </option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                                <x-ui.inputs.admin.select
                                    name="collaborator_id"
                                    id="collaborator_id"
                                    :label="__('view.admin.catalog.items.edit.collaborator')"
                                    required
                                >
                                    @foreach ($collaborators as $collaborator)
                                        <option value="{{ $collaborator->id }}" @selected(old('collaborator_id', $item->collaborator_id) == $collaborator->id)>
                                            {{ $collaborator->contact }} - {{ $collaborator->full_name }}
                                        </option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                                <x-ui.inputs.admin.text
                                    name="date"
                                    id="date"
                                    type="date"
                                    :label="__('view.admin.catalog.items.edit.date')"
                                    :value="$item->date?->format('Y-m-d')"
                                />
                                <x-ui.inputs.admin.text
                                    name="identification_code"
                                    id="identification_code"
                                    :label="__('view.admin.catalog.items.edit.identification_code')"
                                    :value="$item->identification_code"
                                />
                                <x-ui.inputs.admin.select
                                    name="validation"
                                    id="validation"
                                    :label="__('view.admin.catalog.items.edit.validation')"
                                >
                                    <option value="0" @selected(old('validation', $item->validation) == 0)>{{ __('view.admin.catalog.items.edit.no') }}</option>
                                    <option value="1" @selected(old('validation', $item->validation) == 1)>{{ __('view.admin.catalog.items.edit.yes') }}</option>
                                </x-ui.inputs.admin.select>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $currentCoverImage = $item->coverImage ?? $item->images->sortBy('sort_order')->first();
                                @endphp
                                @include('pages.admin.catalog.items._partials.edit.cover-upload')
                                @include('pages.admin.catalog.items._partials.edit.gallery-upload')
                                @include('pages.admin.catalog.items._partials.edit.current-images-preview')
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-ui.inputs.admin.textarea
                            name="history"
                            id="history"
                            :rows="46"
                            :label="__('view.admin.catalog.items.edit.history')"
                            :value="$item->history"
                        />
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">{{ __('view.admin.catalog.items.edit.submit') }}</x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
            </form>

        <x-ui.image-modal />
        <x-release-lock-on-leave type="items" :id="$item->id" />
        <x-ui.images.catalog.upload-assets />

        <script>
            (function() {
                var coverLabel = @json(__('app.catalog.item_image.cover'));
                var galleryLabel = @json(__('app.catalog.item_image.gallery'));
                var adminGalleryFiles = [];

                function isImage(file) { return window.__catalogUploadUtils && window.__catalogUploadUtils.isImage(file); }

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
                    window.__catalogUploadUtils && window.__catalogUploadUtils.attachDropZoneState(zone);
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
                    window.__catalogUploadUtils && window.__catalogUploadUtils.attachDropZoneState(zone);
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
                    var form = document.getElementById('admin-item-edit-form');
                    if (!form) return;
                    if (window.__catalogUploadUtils) {
                        window.__catalogUploadUtils.setFileInputs(form, 'gallery_images[]', adminGalleryFiles);
                    }
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
                var form = document.getElementById('admin-item-edit-form');
                if (form) form.addEventListener('submit', injectAdminGalleryInputs);
            })();
        </script>
</x-layouts.admin>
