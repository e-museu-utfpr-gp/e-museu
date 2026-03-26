@props([
    'name',
    'id' => null,
    'label' => null,
    'help' => null,
    'required' => false,
    'errorKey' => null,
    'roundedTop' => false,
    'showErrors' => true,
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
            {{ $attributes->class([
                'form-select',
                'input-form',
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
