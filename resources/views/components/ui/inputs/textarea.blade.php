@props([
    'name',
    'id' => null,
    'label' => null,
    'help' => null,
    'rows' => 6,
    'value' => null,
    'required' => false,
    'placeholder' => null,
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
        <textarea
            name="{{ $name }}"
            id="{{ $id }}"
            rows="{{ $rows }}"
            @if ($placeholder !== null && $placeholder !== '') placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            {{ $attributes->class([
                'form-control',
                'input-form',
                'is-invalid' => $hasError,
            ]) }}
        >{{ old($name, $value) }}</textarea>
        @if ($showErrors && $errorKey)
            @error($errorKey)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>
</div>
