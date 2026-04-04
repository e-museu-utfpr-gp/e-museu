@props([
    'action',
    'options',
    'placeholder',
    'buttonLabel',
    'booleanColumns' => [],
    'yesLabel' => null,
    'noLabel' => null,
])
@php
    $currentColumn = request()->query('search_column');
    $currentSearch = request()->query('search');
    $hasBoolean = count($booleanColumns) > 0;
    $yesLabel = $yesLabel ?? __('view.shared.yes');
    $noLabel = $noLabel ?? __('view.shared.no');
    $isBooleanSelected = $hasBoolean && in_array($currentColumn, $booleanColumns, true);
    $adminSearchResetQuery = collect(request()->except(['search_column', 'search', 'page']))
        ->reject(static fn ($v) => $v === null || $v === '' || (is_array($v) && count($v) === 0))
        ->all();
    $adminSearchResetUrl = $action . (count($adminSearchResetQuery) > 0 ? '?' . http_build_query($adminSearchResetQuery) : '');
@endphp
<form action="{{ $action }}" class="d-flex flex-nowrap align-items-center gap-2 admin-search-form ms-auto" method="GET">
    <select
        class="form-select admin-search-column"
        name="search_column"
        aria-label="{{ $placeholder }}"
        @if ($hasBoolean) data-boolean-columns="{{ implode(',', $booleanColumns) }}" @endif
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
        value="{{ $isBooleanSelected ? '' : $currentSearch }}"
        aria-label="{{ $placeholder }}"
        @disabled($isBooleanSelected)
        @style(['display: none' => $isBooleanSelected])
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
