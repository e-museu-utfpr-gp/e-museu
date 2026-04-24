{{-- Expects: $contentLanguages, $preferredContentTabLanguageId, $tagCategory (nullable), $aiTranslationUiEnabled (bool), $aiTranslationResource (optional), $aiTranslationFieldKeys (optional CSV), $aiTranslationFieldLimits (optional array) --}}

<div
    class="mb-3"
    @if (($aiTranslationUiEnabled ?? false) && isset($aiTranslationResource) && $aiTranslationResource !== '' && isset($aiTranslationFieldKeys) && $aiTranslationFieldKeys !== '')
        data-admin-ai-translation-root="1"
        data-ai-resource="{{ $aiTranslationResource }}"
        data-ai-field-keys="{{ $aiTranslationFieldKeys }}"
        data-ai-field-limits="{{ e(json_encode($aiTranslationFieldLimits ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}"
    @endif
>
    <label class="form-label fw-bold">{{ __('view.admin.catalog.items.form.content_by_language') }}</label>
    @error('translations')
        <div class="text-danger small mb-2">{{ $message }}</div>
    @enderror
    <ul class="nav nav-tabs" role="tablist">
        @foreach ($contentLanguages as $lang)
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link @if ($lang->id === $preferredContentTabLanguageId) active @endif"
                    id="tag-category-tab-trigger-{{ $lang->code }}"
                    data-bs-toggle="tab"
                    data-bs-target="#tag-category-tab-pane-{{ $lang->code }}"
                    role="tab"
                    aria-controls="tag-category-tab-pane-{{ $lang->code }}"
                    aria-selected="{{ $lang->id === $preferredContentTabLanguageId ? 'true' : 'false' }}"
                >
                    <x-ui.language-tab-label :lang="$lang" />
                </button>
            </li>
        @endforeach
    </ul>
    <div class="tab-content border border-top-0 p-3 rounded-bottom bg-white">
        @foreach ($contentLanguages as $lang)
            @php
                $tr = $tagCategory?->translations->firstWhere('language_id', $lang->id);
                $c = $lang->code;
                $nameVal = old('translations.' . $c . '.name', $tr?->name ?? '');
            @endphp
            <div
                class="tab-pane fade @if ($lang->id === $preferredContentTabLanguageId) show active @endif"
                id="tag-category-tab-pane-{{ $lang->code }}"
                role="tabpanel"
                aria-labelledby="tag-category-tab-trigger-{{ $lang->code }}"
            >
                @include('pages.admin._partials.ai-translation-tab-actions', [
                    'lang' => $lang,
                    'aiTranslationResource' => $aiTranslationResource ?? null,
                    'aiTranslationUiEnabled' => $aiTranslationUiEnabled ?? false,
                ])
                <x-ui.inputs.admin.text
                    name="translations[{{ $c }}][name]"
                    id="tag-category-name-{{ $c }}"
                    :label="__('view.admin.catalog.items.form.name')"
                    :value="$nameVal"
                    :errorKey="'translations.' . $c . '.name'"
                />
            </div>
        @endforeach
    </div>
</div>
