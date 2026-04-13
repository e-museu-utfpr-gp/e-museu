<x-layouts.app :title="__('view.catalog.items.create.title')">
    <div class="container main-container mb-auto">
        <h1>{{ __('view.catalog.items.create.heading') }}</h1>
        <p class="ms-4 fw-bold">{{ __('view.catalog.items.create.intro') }}</p>
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
            data-route-clear-contribution-session="{{ route('catalog.collaborators.clear-contribution-session') }}"
            data-route-request-verification-code="{{ route('catalog.collaborators.request-verification-code') }}"
            data-route-confirm-verification-code="{{ route('catalog.collaborators.confirm-verification-code') }}"
            data-msg-email-required="{{ __('view.catalog.items.create.email_verification_email_required') }}"
            data-msg-code-required="{{ __('view.catalog.items.create.email_verification_code_required') }}"
            data-msg-full-name-required="{{ __('view.catalog.items.create.email_verification_full_name_required') }}"
            data-msg-full-name-required-before-code="{{ __('view.catalog.items.create.email_verification_full_name_required_before_code') }}"
            data-msg-name-differs-warning="{{ __('view.catalog.items.create.email_verification_name_differs_warning') }}"
            data-msg-antibot-before-email-code="{{ __('antibot.complete_before_email_code') }}"
            data-label-cover="{{ __('app.catalog.item_image.cover') }}"
            data-label-gallery="{{ __('app.catalog.item_image.gallery') }}"
            data-label-remove-image="{{ __('view.catalog.items.create.remove_image') }}"
            data-session-flash='@json(['hasSuccess' => session()->has('success'), 'hasErrors' => session()->has('errors')])'
        >
            @csrf
            <div class="row mb-3">
                <div class="col-12 ms-4" id="item-create-flash-client-host">
                    <x-ui.flash-messages variant="app" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-12 mb-4">
                    <x-ui.inputs.select
                        name="content_locale"
                        id="contribution_content_locale"
                        :label="__('view.catalog.items.create.content_language_label')"
                        :help="__('view.catalog.items.create.content_language_help')"
                        :roundedTop="true"
                        :enhanced="false"
                        required
                    >
                        @foreach ($contributionLanguages as $lang)
                            <option value="{{ $lang->code }}" @selected(old('content_locale', $defaultContributionContentLocale) === $lang->code)>
                                {{ $lang->name }}
                            </option>
                        @endforeach
                    </x-ui.inputs.select>
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
                    <div class="row">
                        <div class="col-md-6">
                            <x-ui.inputs.select
                                name="location_id"
                                id="location_id"
                                :label="__('view.catalog.items.create.location_label')"
                                :help="__('view.catalog.items.create.location_help')"
                                required
                                :roundedTop="true"
                                :enhanced="false"
                            >
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" @selected((string) old('location_id', $defaultCatalogLocationId) === (string) $location->id)>
                                        {{ $location->localized_label }}
                                    </option>
                                @endforeach
                            </x-ui.inputs.select>
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
                        name="email"
                        id="email"
                            type="email"
                            :label="__('view.catalog.items.create.email_label')"
                            :help="__('view.catalog.items.create.email_help')"
                            required
                        />
                        <div class="warning-div px-1 mx-5 mb-3 js-email-check-contact-network-error" hidden role="alert">
                            <i class="bi bi-wifi-off mx-1 h5" aria-hidden="true"></i>{{ __('view.catalog.items.create.contact_check_network_error') }}
                        </div>
                        <div class="warning-div px-1 mx-5 mb-3" id="email-warning" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create.contact_not_found') }}
                        </div>
                        <div class="success-div px-1 mx-5 mb-3" id="email-success" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create.contact_found') }}
                        </div>
                        <div class="warning-div px-1 mx-5 mb-3" id="email-pending-verification" hidden>
                            <div class="js-email-pending-first-time" hidden>
                                <i class="bi bi-envelope mx-1 h5"></i>{{ __('view.catalog.items.create.contact_pending_verification') }}
                            </div>
                            <div class="js-email-pending-session" hidden>
                                <i class="bi bi-envelope mx-1 h5"></i>{{ __('view.catalog.items.create.contact_pending_session_verification') }}
                            </div>
                        </div>
                        <div class="px-1 mx-5 mb-3 text-muted small js-email-internal-reserved" hidden role="status">
                            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>{{ __('app.collaborator.email_reserved_for_internal') }}
                        </div>
                    </div>
                    <x-ui.inputs.text
                        name="full_name"
                        id="full_name"
                        :label="__('view.catalog.items.create.full_name_label')"
                        :help="__('view.catalog.items.create.full_name_help')"
                        required
                    />
                    <div class="warning-div px-1 mx-5 mb-3 js-email-name-differs-warning" hidden role="status">
                        <i class="bi bi-info-circle-fill mx-1 h5"></i>
                        <span class="js-email-name-differs-warning-text"></span>
                    </div>
                    @include('pages.catalog.items._partials.email-verification-code', ['codeInputId' => 'item-create-verification-code'])
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

    <div class="modal fade" id="item-create-clear-modal" tabindex="-1" aria-labelledby="item-create-clear-modal-label"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="item-create-clear-modal-label">{{ __('view.catalog.items.create.clear_form') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('view.shared.modal_dismiss') }}"></button>
                </div>
                <div class="modal-body">{{ __('view.catalog.items.create.clear_form_confirm') }}</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('view.catalog.items.create.clear_form_modal_cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger" id="item-create-clear-confirm-btn">
                        {{ __('view.catalog.items.create.clear_form_modal_confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <x-ui.images.catalog.upload-assets />

</x-layouts.app>
