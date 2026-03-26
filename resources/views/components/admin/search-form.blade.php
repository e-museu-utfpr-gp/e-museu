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
@endphp
<form action="{{ $action }}" class="d-flex admin-search-form" method="GET">
    <select
        class="form-select me-2 admin-search-column"
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
        class="form-control me-2 admin-search-text"
        placeholder="{{ $placeholder }}"
        value="{{ $isBooleanSelected ? '' : $currentSearch }}"
        aria-label="{{ $placeholder }}"
        @disabled($isBooleanSelected)
        @style(['display: none' => $isBooleanSelected])
    />
    @if ($hasBoolean)
        <select
            name="search"
            class="form-select me-2 admin-search-boolean"
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
</form>
@if ($hasBoolean)
    @push('scripts')
    <script>
        (function () {
            var forms = document.querySelectorAll('form.admin-search-form');
            forms.forEach(function (form) {
                var searchColumn = form.querySelector('.admin-search-column');
                var searchText = form.querySelector('.admin-search-text');
                var searchBoolean = form.querySelector('.admin-search-boolean');
                if (!searchColumn || !searchText || !searchBoolean) return;
                var booleanColumns = (searchColumn.getAttribute('data-boolean-columns') || '').split(',').map(function (s) { return s.trim(); }).filter(Boolean);
                function toggleSearchInput() {
                    var col = searchColumn.value;
                    var isBoolean = booleanColumns.indexOf(col) !== -1;
                    if (isBoolean) {
                        searchText.style.display = 'none';
                        searchText.disabled = true;
                        searchBoolean.style.display = '';
                        searchBoolean.disabled = false;
                    } else {
                        searchText.style.display = '';
                        searchText.disabled = false;
                        searchBoolean.style.display = 'none';
                        searchBoolean.disabled = true;
                    }
                }
                searchColumn.addEventListener('change', toggleSearchInput);
                toggleSearchInput();
            });
        })();
    </script>
    @endpush
@endif
