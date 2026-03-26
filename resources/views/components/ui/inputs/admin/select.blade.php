@props([
    'name',
    'id' => null,
    'label' => null,
    'required' => false,
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
        <select
            name="{{ $name }}"
            id="{{ $id }}"
            @if ($required) required @endif
            {{ $attributes->class([
                'form-select',
                'is-invalid' => $hasError,
            ]) }}
        >{{ $slot }}</select>
        @if ($showErrors && $errorKey)
            @error($errorKey)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
@if ($wrapper)
    </div>
@endif
