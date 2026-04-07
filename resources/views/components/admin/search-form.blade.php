@props([
    'action',
    'options',
    'placeholder',
    'buttonLabel',
    'booleanColumns' => [],
    'yesLabel' => null,
    'noLabel' => null,
    'searchSelectColumns' => [],
    'searchSelectOptions' => [],
    'searchSelectAnyLabel' => null,
])
@php
    $currentColumn = request()->query('search_column');
    $currentSearch = request()->query('search');
    $hasBoolean = count($booleanColumns) > 0;
    $hasSelectSearch = count($searchSelectColumns) > 0;
    $yesLabel = $yesLabel ?? __('view.shared.yes');
    $noLabel = $noLabel ?? __('view.shared.no');
    $isBooleanSelected = $hasBoolean && in_array($currentColumn, $booleanColumns, true);
    $isSelectSearchSelected = $hasSelectSearch && in_array($currentColumn, $searchSelectColumns, true);
    $anySelectLabel = $searchSelectAnyLabel ?? $placeholder;
    $adminSearchResetQuery = collect(request()->except(['search_column', 'search', 'page']))
        ->reject(static fn ($v) => $v === null || $v === '' || (is_array($v) && count($v) === 0))
        ->all();
    $adminSearchResetUrl = $action . (count($adminSearchResetQuery) > 0 ? '?' . http_build_query($adminSearchResetQuery) : '');
    $useTextSearch = ! $isBooleanSelected && ! $isSelectSearchSelected;
@endphp
<form action="{{ $action }}" class="d-flex flex-nowrap align-items-center gap-2 admin-search-form ms-auto" method="GET">
    <select
        class="form-select admin-search-column"
        name="search_column"
        aria-label="{{ $placeholder }}"
        @if ($hasBoolean) data-boolean-columns="{{ implode(',', $booleanColumns) }}" @endif
        @if ($hasSelectSearch) data-select-columns="{{ implode(',', $searchSelectColumns) }}" @endif
    >
        @foreach ($options as $option)
            <option value="{{ $option['value'] }}" @selected((string) $currentColumn === (string) $option['value'])>
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>
    <input
        type="search"
        name="search"
        class="form-control admin-search-text"
        placeholder="{{ $placeholder }}"
        value="{{ $useTextSearch ? $currentSearch : '' }}"
        aria-label="{{ $placeholder }}"
        @disabled(! $useTextSearch)
        @style(['display: none' => ! $useTextSearch])
    />
    @if ($hasBoolean)
        <select
            name="search"
            class="form-select admin-search-boolean"
            aria-label="{{ $placeholder }} ({{ $yesLabel }}/{{ $noLabel }})"
            @disabled(! $isBooleanSelected)
            @style(['display: none' => ! $isBooleanSelected])
        >
            <option value="">{{ $placeholder }}</option>
            <option value="1" @selected($isBooleanSelected && ($currentSearch === '1' || $currentSearch === 1))>{{ $yesLabel }}</option>
            <option value="0" @selected($isBooleanSelected && ($currentSearch === '0' || $currentSearch === 0))>{{ $noLabel }}</option>
        </select>
    @endif
    @if ($hasSelectSearch)
        <select
            name="search"
            class="form-select admin-search-select"
            aria-label="{{ $anySelectLabel }}"
            @disabled(! $isSelectSearchSelected)
            @style(['display: none' => ! $isSelectSearchSelected])
        >
            <option value="">{{ $anySelectLabel }}</option>
            @foreach ($searchSelectOptions as $opt)
                <option value="{{ $opt['value'] }}" @selected($isSelectSearchSelected && (string) $currentSearch === (string) $opt['value'])>
                    {{ $opt['label'] }}
                </option>
            @endforeach
        </select>
    @endif
    <x-ui.buttons.submit variant="secondary">{{ $buttonLabel }}</x-ui.buttons.submit>
    <x-ui.buttons.default
        :href="$adminSearchResetUrl"
        variant="outline-secondary"
        class="flex-shrink-0"
        icon="bi bi-x-lg"
    >
        {{ __('view.shared.buttons.reset') }}
    </x-ui.buttons.default>
</form>
