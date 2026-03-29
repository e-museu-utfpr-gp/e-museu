@props([
    'name',
    'id' => null,
    'label' => null,
    'required' => false,
    'placeholder' => null,
    'errorKey' => null,
    'showErrors' => true,
    'wrapper' => true,
    'wrapperClass' => 'mb-3',
    'autocomplete' => 'current-password',
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
        <div class="input-group">
            <input
                type="password"
                name="{{ $name }}"
                id="{{ $id }}"
                autocomplete="{{ $autocomplete }}"
                @if ($placeholder !== null && $placeholder !== '') placeholder="{{ $placeholder }}" @endif
                @if ($required) required @endif
                {{ $attributes->class([
                    'form-control',
                    'is-invalid' => $hasError,
                ]) }}
            />
            <button
                type="button"
                class="btn btn-outline-secondary"
                data-password-toggle
                data-target="{{ $id }}"
                data-label-show="{{ __('view.shared.buttons.show_password') }}"
                data-label-hide="{{ __('view.shared.buttons.hide_password') }}"
                aria-label="{{ __('view.shared.buttons.show_password') }}"
                title="{{ __('view.shared.buttons.show_password') }}"
            >
                <i class="bi bi-eye" aria-hidden="true"></i>
            </button>
        </div>
        @if ($showErrors && $errorKey)
            @error($errorKey)
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        @endif
@if ($wrapper)
    </div>
@endif

