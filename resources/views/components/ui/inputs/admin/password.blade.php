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

@once
    @push('scripts')
        <script>
            (function () {
                if (window.__adminPasswordToggleInitialized) return;
                window.__adminPasswordToggleInitialized = true;

                document.addEventListener('click', function (event) {
                    var button = event.target.closest('[data-password-toggle]');
                    if (!button) return;

                    var targetId = button.getAttribute('data-target');
                    var input = targetId ? document.getElementById(targetId) : null;
                    if (!input) return;

                    var icon = button.querySelector('i');
                    var showLabel = button.getAttribute('data-label-show') || 'Show password';
                    var hideLabel = button.getAttribute('data-label-hide') || 'Hide password';
                    var showing = input.type === 'text';

                    input.type = showing ? 'password' : 'text';

                    if (icon) {
                        icon.classList.toggle('bi-eye', showing);
                        icon.classList.toggle('bi-eye-slash', !showing);
                    }

                    var nextLabel = showing ? showLabel : hideLabel;
                    button.setAttribute('aria-label', nextLabel);
                    button.setAttribute('title', nextLabel);
                });
            })();
        </script>
    @endpush
@endonce
