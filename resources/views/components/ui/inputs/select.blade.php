@props([
    'name',
    'id' => null,
    'label' => null,
    'help' => null,
    'required' => false,
    'errorKey' => null,
    'roundedTop' => false,
    'showErrors' => true,
    'enhanced' => true,
])

@php
    $id = $id ?? $name;
    $errorKey = $errorKey ?? $name;
    $hasError = $showErrors && $errorKey && $errors->has($errorKey);
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="form-label">
            <h5>
                {{ $label }}
                @if ($help)
                    <x-ui.info-popover :content="$help" />
                @endif
            </h5>
        </label>
    @endif
    <div @class(['input-div', 'rounded-top' => $roundedTop])>
        <select
            name="{{ $name }}"
            id="{{ $id }}"
            @if ($required) required @endif
            @if ($enhanced)
                data-enhanced-search-placeholder="{{ __('view.shared.select_search_placeholder') }}"
                data-enhanced-no-results="{{ __('view.shared.select_no_results') }}"
            @endif
            {{ $attributes->class([
                'form-select',
                'input-form',
                'js-enhanced-select' => $enhanced,
                'is-invalid' => $hasError,
            ]) }}
        >{{ $slot }}</select>
        @if ($showErrors && $errorKey)
            @error($errorKey)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>
</div>
