<x-layouts.app :title="__('view.catalog.items.create.title')">
    <div class="container main-container mb-auto">
        <h1>{{ __('view.catalog.items.create.heading') }}</h1>
        <p class="ms-4 fw-bold">{{ __('view.catalog.items.create.intro') }}</p>
        <div class="ms-4 mb-4">
            <x-ui.flash-messages variant="app" />
        </div>
        <form action="{{ route('catalog.items.store') }}" method="POST" enctype="multipart/form-data" id="item-create-form">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <x-ui.inputs.text
                        name="name"
                        id="name"
                        autocomplete="off"
                        :label="__('view.catalog.items.create.name_label')"
                        :help="__('view.catalog.items.create.name_help')"
                        required
                    />
                    <div class="row">
                        <div class="col-md-6">
                            <x-ui.inputs.select
                                name="category_id"
                                id="category_id"
                                :label="__('view.catalog.items.create.category_label')"
                                :help="__('view.catalog.items.create.category_help')"
                                required
                                :roundedTop="true"
                            >
                                <option value="" @selected(old('category_id') === null || old('category_id') === '')>-</option>
                                @foreach ($itemCategories as $itemCategory)
                                    <option value="{{ $itemCategory->id }}" @selected(old('category_id') == $itemCategory->id)>
                                        {{ $itemCategory->name }}
                                    </option>
                                @endforeach
                            </x-ui.inputs.select>
                        </div>
                        <div class="col-md-6">
                            <x-ui.inputs.text
                                name="date"
                                id="date"
                                type="date"
                                :label="__('view.catalog.items.create.release_date')"
                                :help="__('view.catalog.items.create.release_date_help')"
                            />
                        </div>
                    </div>
                    <x-ui.inputs.textarea
                        name="description"
                        id="description"
                        :rows="6"
                        :label="__('view.catalog.items.create.short_description')"
                        :help="__('view.catalog.items.create.short_description_help')"
                        required
                    />
                    <x-ui.inputs.textarea
                        name="detail"
                        id="detail"
                        :rows="6"
                        :label="__('view.catalog.items.create.technical_details')"
                        :help="__('view.catalog.items.create.technical_details_help')"
                    />
                    <x-ui.inputs.textarea
                        name="history"
                        id="history"
                        :rows="24"
                        :label="__('view.catalog.items.create.history_label')"
                        :help="__('view.catalog.items.create.history_help')"
                    />
                    @include('pages.catalog.items._partials.create.images-upload')
                    <div>
                        <x-ui.inputs.text
                            name="contact"
                            id="contact"
                            type="email"
                            :label="__('view.catalog.items.create.email_label')"
                            :help="__('view.catalog.items.create.email_help')"
                            required
                        />
                        <div class="warning-div px-1 mx-5 mb-3" id="contact-warning" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create.contact_not_found') }}
                        </div>
                        <div class="success-div px-1 mx-5 mb-3" id="contact-success" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create.contact_found') }}
                        </div>
                    </div>
                    <x-ui.inputs.text
                        name="full_name"
                        id="full_name"
                        :label="__('view.catalog.items.create.full_name_label')"
                        :help="__('view.catalog.items.create.full_name_help')"
                        required
                    />
                </div>
                <div class="col-md-6">
                    @include('pages.catalog.items._partials.create.tags-extras-components')
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
    @include('pages.catalog.items._partials.create.component-modal')

    @include('pages.catalog.items._partials.create.extra-modal')

    @include('pages.catalog.items._partials.create.tag-modal')
    <x-ui.images.catalog.upload-assets />

    <script type="text/javascript">
        (function() {
            if (window.__catalogItemImagesUploadInitializedForms && window.__catalogItemImagesUploadInitializedForms['item-create-form']) return;
            var coverLabel = @json(__('app.catalog.item_image.cover'));
            var galleryLabel = @json(__('app.catalog.item_image.gallery'));
            var removeLabel = @json(__('view.catalog.items.create.remove_image'));
            var acceptTypes = 'image/jpeg,image/png,image/jpg,image/webp';

            var galleryFiles = [];

            function isImage(file) { return window.__catalogUploadUtils && window.__catalogUploadUtils.isImage(file); }

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
                window.__catalogUploadUtils && window.__catalogUploadUtils.attachDropZoneState(zone);
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
                window.__catalogUploadUtils && window.__catalogUploadUtils.attachDropZoneState(zone);
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
                if (window.__catalogUploadUtils) {
                    window.__catalogUploadUtils.setFileInputs(form, 'gallery_images[]', galleryFiles);
                }
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
    <script type="text/javascript">
        // Exposes the route for the checkContact.js component.
        window.checkContactRoute = "{{ route('catalog.collaborators.check-contact') }}";

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

</x-layouts.app>
