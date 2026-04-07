{{-- Expects: $contentLanguages, $preferredContentTabLanguageId, $item (nullable) --}}

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
                    id="tab-trigger-{{ $lang->code }}"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-pane-{{ $lang->code }}"
                    role="tab"
                    aria-controls="tab-pane-{{ $lang->code }}"
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
                $tr = $item?->translations->firstWhere('language_id', $lang->id);
                $c = $lang->code;
                $nameVal = old('translations.' . $c . '.name', $tr?->name ?? '');
                $descVal = old('translations.' . $c . '.description', $tr?->description ?? '');
                $detailVal = old('translations.' . $c . '.detail', $tr?->detail ?? '');
                $historyVal = old('translations.' . $c . '.history', $tr?->history ?? '');
            @endphp
            <div
                class="tab-pane fade @if ($lang->id === $preferredContentTabLanguageId) show active @endif"
                id="tab-pane-{{ $lang->code }}"
                role="tabpanel"
                aria-labelledby="tab-trigger-{{ $lang->code }}"
            >
                <div class="row">
                    <div class="col-md-6">
                        <x-ui.inputs.admin.text
                            name="translations[{{ $c }}][name]"
                            id="item-name-{{ $c }}"
                            :label="__('view.admin.catalog.items.form.name')"
                            :value="$nameVal"
                            :errorKey="'translations.' . $c . '.name'"
                        />
                        <x-ui.inputs.admin.textarea
                            name="translations[{{ $c }}][description]"
                            id="item-description-{{ $c }}"
                            :rows="5"
                            :label="__('view.admin.catalog.items.form.description')"
                            :value="$descVal"
                            :errorKey="'translations.' . $c . '.description'"
                        />
                        <x-ui.inputs.admin.textarea
                            name="translations[{{ $c }}][detail]"
                            id="item-detail-{{ $c }}"
                            :rows="7"
                            :label="__('view.admin.catalog.items.form.detail')"
                            :value="$detailVal"
                            :errorKey="'translations.' . $c . '.detail'"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-ui.inputs.admin.textarea
                            name="translations[{{ $c }}][history]"
                            id="item-history-{{ $c }}"
                            :rows="22"
                            :label="__('view.admin.catalog.items.form.history')"
                            :value="$historyVal"
                            :errorKey="'translations.' . $c . '.history'"
                        />
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
