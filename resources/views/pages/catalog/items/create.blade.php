<x-layouts.app :title="__('view.catalog.items.create.title')">
    <div class="container main-container mb-auto">
        <h1>{{ __('view.catalog.items.create.heading') }}</h1>
        <p class="ms-4 fw-bold">{{ __('view.catalog.items.create.intro') }}</p>
        <div class="ms-4 mb-4">
            <x-ui.flash-messages variant="app" />
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
        <form action="{{ route('catalog.items.store') }}" method="POST" enctype="multipart/form-data" id="item-create-form"
            data-modals-i18n='@json($createModalsI18n)'
            data-route-tags-autocomplete="{{ route('catalog.tags.autocomplete') }}"
            data-route-tags-check-name="{{ route('catalog.tags.check-name') }}"
            data-route-items-by-category="{{ route('catalog.items.byCategory') }}"
            data-check-contact-route="{{ route('catalog.collaborators.check-contact') }}"
            data-label-cover="{{ __('app.catalog.item_image.cover') }}"
            data-label-gallery="{{ __('app.catalog.item_image.gallery') }}"
            data-label-remove-image="{{ __('view.catalog.items.create.remove_image') }}"
            data-recover-confirm='@json(__('view.catalog.items.create.recover_confirm'))'
            data-session-flash='@json(['hasSuccess' => session()->has('success'), 'hasErrors' => session()->has('errors')])'
        >
            @csrf
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="mb-4">
                        <h5 class="mb-2" id="contribution_content_locale_heading">
                            {{ __('view.catalog.items.create.content_language_label') }}
                            <x-ui.info-popover :content="__('view.catalog.items.create.content_language_help')" />
                        </h5>
                        <select
                            name="content_locale"
                            id="contribution_content_locale"
                            class="form-select w-100"
                            required
                            aria-labelledby="contribution_content_locale_heading"
                        >
                            @foreach ($contributionLanguages as $lang)
                                <option value="{{ $lang->code }}" @selected(old('content_locale', $defaultContributionContentLocale) === $lang->code)>
                                    @if ($lang->code === \App\Enums\Content\ContentLanguage::NEUTRAL->value)
                                        {{ __('view.catalog.items.create.content_language_option_neutral') }}
                                    @else
                                        {{ $lang->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('content_locale')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
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

    @include('pages.catalog.items._partials.create.component-modal')

    @include('pages.catalog.items._partials.create.extra-modal')

    @include('pages.catalog.items._partials.create.tag-modal')
    <x-ui.images.catalog.upload-assets />

</x-layouts.app>
