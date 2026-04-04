{{-- Expects: $contentLanguages, $preferredContentTabLanguageId, $itemCategory (nullable) --}}

<div class="mb-3">
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
                    id="item-category-tab-trigger-{{ $lang->code }}"
                    data-bs-toggle="tab"
                    data-bs-target="#item-category-tab-pane-{{ $lang->code }}"
                    role="tab"
                    aria-controls="item-category-tab-pane-{{ $lang->code }}"
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
                $tr = $itemCategory?->translations->firstWhere('language_id', $lang->id);
                $c = $lang->code;
                $nameVal = old('translations.' . $c . '.name', $tr?->name ?? '');
            @endphp
            <div
                class="tab-pane fade @if ($lang->id === $preferredContentTabLanguageId) show active @endif"
                id="item-category-tab-pane-{{ $lang->code }}"
                role="tabpanel"
                aria-labelledby="item-category-tab-trigger-{{ $lang->code }}"
            >
                <x-ui.inputs.admin.text
                    name="translations[{{ $c }}][name]"
                    id="item-category-name-{{ $c }}"
                    :label="__('view.admin.catalog.items.form.name')"
                    :value="$nameVal"
                    :errorKey="'translations.' . $c . '.name'"
                />
            </div>
        @endforeach
    </div>
</div>
