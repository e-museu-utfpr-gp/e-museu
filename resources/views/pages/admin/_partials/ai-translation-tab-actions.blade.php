{{-- Expects: $lang (Language), $aiTranslationResource (?string), $aiTranslationUiEnabled (bool), $aiTranslationProviderOptions (list<array{value:string,label:string}>) --}}
@if (($aiTranslationUiEnabled ?? false) && isset($aiTranslationResource) && $aiTranslationResource !== '' && ! $lang->isCatalogUniversalContentLocale())
    <div
        class="admin-ai-translate-progress alert alert-info py-2 px-3 mb-2 d-none align-items-center gap-2"
        role="status"
        aria-live="polite"
        aria-busy="false"
    >
        <span class="spinner-border spinner-border-sm text-primary" aria-hidden="true"></span>
        <span class="admin-ai-translate-progress__label">{{ __('view.admin.ai.busy') }}</span>
    </div>
    <div
        class="d-flex flex-wrap justify-content-end gap-2 mb-2"
        role="group"
        aria-label="{{ __('view.admin.ai.actions_aria', ['locale' => $lang->catalogContentTabLabel()]) }}"
    >
        <select
            class="form-select form-select-sm w-auto js-admin-ai-provider"
            data-ai-target-locale="{{ $lang->code }}"
            aria-label="{{ __('view.admin.ai.provider_select_aria', ['locale' => $lang->catalogContentTabLabel()]) }}"
        >
            <option value="auto">{{ __('view.admin.ai.provider_auto') }}</option>
            @foreach (($aiTranslationProviderOptions ?? []) as $providerOption)
                <option value="{{ $providerOption['value'] }}">{{ $providerOption['label'] }}</option>
            @endforeach
        </select>
        <button
            type="button"
            class="btn btn-sm btn-outline-primary js-admin-ai-translate"
            data-ai-mode="fill"
            data-ai-target-locale="{{ $lang->code }}"
            data-ai-resource="{{ $aiTranslationResource }}"
        >
            <span class="bi bi-globe2" aria-hidden="true"></span>
            {{ __('view.admin.ai.translate_fill') }}
        </button>
        <button
            type="button"
            class="btn btn-sm btn-outline-secondary js-admin-ai-translate"
            data-ai-mode="regenerate"
            data-ai-target-locale="{{ $lang->code }}"
            data-ai-resource="{{ $aiTranslationResource }}"
        >
            <span class="bi bi-arrow-clockwise" aria-hidden="true"></span>
            {{ __('view.admin.ai.translate_regenerate') }}
        </button>
    </div>
    <p
        class="text-muted mb-0 mt-1 text-end small admin-ai-translate-disclaimer"
        data-ai-meta-label="{{ __('view.admin.ai.generated_with') }}"
    >
        <span class="d-inline-flex align-items-start gap-1">
            <span class="bi bi-stars text-primary mt-1" aria-hidden="true"></span>
            <span>{{ __('view.admin.ai.buttons_disclaimer') }}</span>
        </span>
        <span class="admin-ai-translate-disclaimer__meta d-none mt-1"></span>
    </p>
@endif
