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
        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
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
                            <label for="section_id">
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
                                <select required class="form-select me-2 input-form  @error('section') is-invalid @enderror"
                                    name="section_id" id="section_id">
                                    <option selected="selected" value="">-</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}
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
                    <div>
                        <label for="image">
                            <h5>{{ __('view.catalog.items.create.image_label') }}
                                <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                    data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                    data-bs-content="{{ __('view.catalog.items.create.image_help') }}">
                                    <i class="bi bi-info-circle-fill h4 ms-1"
                                        style="color: #ED6E38; cursor: pointer;"></i>
                                </button>
                            </h5>
                        </label>
                        <div class="input-div nav-link">
                            <input
                                class="form-control me-2 image-form input-form p-2  @error('image') is-invalid @enderror"
                                type="file" name="image" placeholder="" required>
                            @error('image')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
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
