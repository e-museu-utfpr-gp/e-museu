<div class="modal fade" id="addExtraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    {{ __('view.catalog.items.show_extra.title') }}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- app.js loads extra-modal-clear-session-on-hide when this data attribute is present (stable hook if #addExtraForm id changes). --}}
                <form action="{{ route('catalog.extras.store') }}" method="POST" id="addExtraForm"
                    data-extra-clear-session-on-hide="1"
                    data-check-contact-route="{{ route('catalog.collaborators.check-contact') }}"
                    data-route-clear-contribution-session="{{ route('catalog.collaborators.clear-contribution-session') }}"
                    data-route-request-verification-code="{{ route('catalog.collaborators.request-verification-code') }}"
                    data-route-confirm-verification-code="{{ route('catalog.collaborators.confirm-verification-code') }}"
                    data-msg-email-required="{{ __('view.catalog.items.create.email_verification_email_required') }}"
                    data-msg-code-required="{{ __('view.catalog.items.create.email_verification_code_required') }}"
                    data-msg-full-name-required="{{ __('view.catalog.items.create.email_verification_full_name_required') }}"
                    data-msg-full-name-required-before-code="{{ __('view.catalog.items.create.email_verification_full_name_required_before_code') }}"
                    data-msg-name-differs-warning="{{ __('view.catalog.items.create.email_verification_name_differs_warning') }}">
                    @csrf
                    <input type="hidden" name="collaborator_id" value="{{ old('collaborator_id', '') }}" class="js-verified-collaborator-id">
                    <input name="item_id" value="{{ $item->id }}" hidden>
                    <div class="mb-3">
                        <label class="form-label" id="extra_content_locale_heading" for="extra_content_locale">
                            {{ __('view.catalog.items.create.content_language_label') }}
                            <x-ui.info-popover :content="__('view.catalog.items.create.content_language_help')" />
                        </label>
                        <select
                            name="content_locale"
                            id="extra_content_locale"
                            class="form-select w-100"
                            required
                            aria-labelledby="extra_content_locale_heading"
                        >
                            @foreach ($contributionLanguages as $lang)
                                <option value="{{ $lang->code }}" @selected(old('content_locale', $defaultExtraContentLocale) === $lang->code)>
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
                    <x-ui.inputs.textarea
                        name="info"
                        id="info"
                        :label="__('view.catalog.items.show_extra.label')"
                        :help="__('view.catalog.items.show_extra.info_help')"
                        :rows="15"
                        required
                    />
                    <div>
                        <x-ui.inputs.text
                            name="email"
                            id="email"
                            type="email"
                            :label="__('view.catalog.items.show_extra.email_label')"
                            :help="__('view.catalog.items.show_extra.email_help')"
                            required
                        />
                        <div class="warning-div px-1 mx-5 mb-3 js-email-check-contact-network-error" hidden role="alert">
                            <i class="bi bi-wifi-off mx-1 h5" aria-hidden="true"></i>
                            {{ __('view.catalog.items.show_extra.contact_check_network_error') }}
                        </div>
                        <div class="warning-div px-1 mx-5 mb-3" id="email-warning" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>
                            {{ __('view.catalog.items.show_extra.contact_warning') }}
                        </div>
                        <div class="success-div px-1 mx-5 mb-3" id="email-success" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>
                            {{ __('view.catalog.items.show_extra.contact_success') }}
                        </div>
                        <div class="warning-div px-1 mx-5 mb-3" id="email-pending-verification" hidden>
                            <div class="js-email-pending-first-time" hidden>
                                <i class="bi bi-envelope mx-1 h5"></i>
                                {{ __('view.catalog.items.show_extra.contact_pending_verification') }}
                            </div>
                            <div class="js-email-pending-session" hidden>
                                <i class="bi bi-envelope mx-1 h5"></i>
                                {{ __('view.catalog.items.show_extra.contact_pending_session_verification') }}
                            </div>
                        </div>
                        <div class="px-1 mx-5 mb-3 text-muted small js-email-internal-reserved" hidden role="status">
                            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>{{ __('app.collaborator.email_reserved_for_internal') }}
                        </div>
                    </div>
                    <x-ui.inputs.text
                        name="full_name"
                        id="full_name"
                        :label="__('view.catalog.items.show_extra.full_name_label')"
                        :help="__('view.catalog.items.show_extra.full_name_help')"
                        required
                    />
                    <div class="warning-div px-1 mx-5 mb-3 js-email-name-differs-warning" hidden role="status">
                        <i class="bi bi-info-circle-fill mx-1 h5"></i>
                        <span class="js-email-name-differs-warning-text"></span>
                    </div>
                    @include('pages.catalog.items._partials.email-verification-code', ['codeInputId' => 'extra-catalog-verification-code'])
                    <div class="col d-flex align-items-center justify-content-end">
                        <x-ui.buttons.submit variant="plain" class="button nav-link py-2 px-3 fw-bold" id="save-extra-button">
                            {{ __('view.catalog.items.show_extra.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="cancel-button nav-link py-2 px-3 fw-bold" type="button" data-bs-dismiss="modal">
                    {{ __('view.catalog.items.show_extra.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
