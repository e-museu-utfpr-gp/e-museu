@props([
    'name',
    'id' => null,
    'label' => null,
    'rows' => 5,
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'errorKey' => null,
    'showErrors' => true,
    'wrapper' => true,
    'wrapperClass' => 'mb-3',
])

@php
    $id = $id ?? $name;
    $errorKey = $errorKey ?? $name;
    $hasError = $showErrors && $errorKey && $errors->has($errorKey);
@endphp

@if ($wrapper)
    <div @class([$wrapperClass => true])>
        @if ($label)
            <label for="{{ $id }}" class="form-label">{{ $label }}</label>
        @endif
@endif
        <textarea
            name="{{ $name }}"
            id="{{ $id }}"
            rows="{{ $rows }}"
            @if ($placeholder !== null && $placeholder !== '') placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            {{ $attributes->class([
                'form-control',
                'is-invalid' => $hasError,
            ]) }}
        >{{ old($name, $value) }}</textarea>
        @if ($showErrors && $errorKey)
            @error($errorKey)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
@if ($wrapper)
    </div>
@endif
