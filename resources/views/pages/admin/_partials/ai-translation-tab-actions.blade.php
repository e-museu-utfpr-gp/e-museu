{{-- Expects: $lang (Language), $aiTranslationResource (?string), $aiTranslationUiEnabled (bool) --}}
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
    <p class="text-muted mb-0 mt-1 text-end admin-ai-translate-disclaimer">{{ __('view.admin.ai.buttons_disclaimer') }}</p>
@endif
