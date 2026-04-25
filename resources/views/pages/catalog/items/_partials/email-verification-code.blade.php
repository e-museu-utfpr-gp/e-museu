@props([
    'codeInputId' => 'catalog-verification-code',
])

@isset($antiBotTurnstileWidgetData)
    @if ($antiBotTurnstileWidgetData)
        <div
            class="js-antibot-verification-request mb-2"
            data-response-field="{{ $antiBotTurnstileWidgetData['responseFieldName'] }}"
        >
            @include('components.antibot.turnstile-widget', $antiBotTurnstileWidgetData)
        </div>
    @endif
@endisset

<div class="d-flex justify-content-end mb-2">
    <button type="button" class="edit-button nav-link py-2 px-3 fw-bold js-send-verification-code" disabled>
        {{ __('view.catalog.items.create.email_send_verification_code') }}
    </button>
</div>
<div
    class="js-verification-send-status mb-3 catalog-verification-msg catalog-verification-status-box"
    role="status"
    aria-live="polite"
></div>
<div class="js-verification-code-row mb-3" hidden>
    <x-ui.inputs.text
        name="catalog_verification_code"
        id="{{ $codeInputId }}"
        :label="__('view.catalog.items.create.email_verification_code_label')"
        :help="__('view.catalog.items.create.email_verification_code_help')"
        :showErrors="false"
        :submittable="false"
        autocomplete="one-time-code"
        maxlength="6"
        pattern="[0-9]*"
        inputmode="numeric"
        class="js-verification-code-input"
    />
    <div
        class="js-verification-code-status mb-2 catalog-verification-msg catalog-verification-status-box"
        role="status"
        aria-live="polite"
    ></div>
    <div class="d-flex justify-content-end mt-2">
        <button type="button" class="button nav-link py-2 px-3 fw-bold js-confirm-verification-code">
            {{ __('view.catalog.items.create.email_confirm_verification_code') }}
        </button>
    </div>
</div>
