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
                <form action="{{ route('catalog.extras.store') }}" method="POST" id="addExtraForm"
                    data-check-contact-route="{{ route('catalog.collaborators.check-contact') }}">
                    @csrf
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
                            name="contact"
                            id="contact"
                            type="email"
                            :label="__('view.catalog.items.show_extra.email_label')"
                            :help="__('view.catalog.items.show_extra.email_help')"
                            required
                        />
                        <div class="warning-div px-1 mx-5 mb-3" id="contact-warning" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>
                            {{ __('view.catalog.items.show_extra.contact_warning') }}
                        </div>
                        <div class="success-div px-1 mx-5 mb-3" id="contact-success" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>
                            {{ __('view.catalog.items.show_extra.contact_success') }}
                        </div>
                    </div>
                    <x-ui.inputs.text
                        name="full_name"
                        id="full_name"
                        :label="__('view.catalog.items.show_extra.full_name_label')"
                        :help="__('view.catalog.items.show_extra.full_name_help')"
                        required
                    />
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
