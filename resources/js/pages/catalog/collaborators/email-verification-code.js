import $ from 'jquery';

/**
 * Public catalog forms: e-mail → send code → enter code → confirm (then submit item/extra).
 * User-facing strings should come from Blade `data-msg-*` on the form; English fallbacks below are last resort.
 */
function jsonErrorMessage(body, fallback) {
    if (!body || typeof body !== 'object') {
        return fallback;
    }
    const top = body.message != null ? String(body.message).trim() : '';
    if (top !== '') {
        return top;
    }
    const errors = body.errors;
    if (errors && typeof errors === 'object') {
        const keys = Object.keys(errors);
        for (let i = 0; i < keys.length; i += 1) {
            const arr = errors[keys[i]];
            if (Array.isArray(arr) && arr.length > 0 && arr[0]) {
                return String(arr[0]);
            }
        }
    }
    return fallback;
}

function setStatus($el, text, variant) {
    $el.removeClass('text-success text-danger text-muted');
    if (variant === 'success') {
        $el.addClass('text-success');
    } else if (variant === 'error') {
        $el.addClass('text-danger');
    } else {
        $el.addClass('text-muted');
    }
    $el.text(text);
}

export function resetVerificationUi($form) {
    if (!$form || !$form.length) {
        return;
    }
    const $codeRow = $form.find('.js-verification-code-row');
    const $codeInput = $form.find('.js-verification-code-input');
    const $sendStatus = $form.find('.js-verification-send-status');
    const $codeStatus = $form.find('.js-verification-code-status');
    if ($codeRow.length) {
        $codeRow.prop('hidden', true);
    }
    if ($codeInput.length) {
        $codeInput.val('');
    }
    if ($sendStatus.length) {
        setStatus($sendStatus, '', 'muted');
    }
    if ($codeStatus.length) {
        setStatus($codeStatus, '', 'muted');
    }
    const $nameDiffers = $form.find('.js-email-name-differs-warning');
    if ($nameDiffers.length) {
        $nameDiffers.prop('hidden', true);
    }
    $form.find('.js-verified-collaborator-id').val('');
}

function triggerCheckContactIfPossible($form) {
    const $email = $form.find('#email');
    if ($email.length) {
        $email.trigger('blur');
    }
}

function initCatalogEmailVerificationForm(form) {
    if (!(form instanceof HTMLFormElement)) {
        return;
    }
    const routeRequest = form.getAttribute('data-route-request-verification-code');
    const routeConfirm = form.getAttribute('data-route-confirm-verification-code');
    if (!routeRequest || !routeConfirm) {
        return;
    }

    const $form = $(form);
    const $email = $form.find('input[name="email"]').first();
    const $sendBtn = $form.find('.js-send-verification-code');
    const $codeRow = $form.find('.js-verification-code-row');
    const $codeInput = $form.find('.js-verification-code-input');
    const $confirmBtn = $form.find('.js-confirm-verification-code');
    const $sendStatus = $form.find('.js-verification-send-status');
    const $codeStatus = $form.find('.js-verification-code-status');
    const $fullName = $form.find('input[name="full_name"]').first();

    if (!$email.length || !$sendBtn.length) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const msgEmailRequired = form.getAttribute('data-msg-email-required') || 'Enter your email.';
    const msgCodeRequired = form.getAttribute('data-msg-code-required') || 'Enter the code.';
    const msgFullNameRequired =
        form.getAttribute('data-msg-full-name-required') || 'Enter your full name before confirming the code.';
    const msgFullNameRequiredBeforeCode =
        form.getAttribute('data-msg-full-name-required-before-code') ||
        'Enter your email and full name before requesting the code.';

    let internalReservedFromCheck = false;
    /** Until the request finishes, ignore extra clicks on “send code”. */
    let verificationSendInFlight = false;
    /** After a successful send, keep the button disabled briefly to limit repeat requests. */
    let verificationSendCooldownUntil = 0;
    const verificationSendCooldownMs = 10000;

    function emailFieldValid() {
        const el = $email[0];
        if (!(el instanceof HTMLInputElement)) {
            return false;
        }
        const v = String(el.value || '').trim();
        if (v === '') {
            return false;
        }
        return el.checkValidity();
    }

    function fullNameNonEmpty() {
        return String($fullName.val() || '').trim() !== '';
    }

    function updateSendVerificationButtonEnabled() {
        const ok = emailFieldValid() && fullNameNonEmpty() && !internalReservedFromCheck;
        const cooldown = Date.now() < verificationSendCooldownUntil;
        $sendBtn.prop('disabled', !ok || cooldown || verificationSendInFlight);
    }

    form.addEventListener('catalog-check-contact', function (e) {
        if (!(e instanceof CustomEvent) || !e.detail || typeof e.detail !== 'object') {
            return;
        }
        internalReservedFromCheck = e.detail.internalReserved === true;
        updateSendVerificationButtonEnabled();
    });

    $email.on('input', function () {
        $form.find('.js-verified-collaborator-id').val('');
    });
    $email.on('input change blur', updateSendVerificationButtonEnabled);
    $fullName.on('input change', updateSendVerificationButtonEnabled);

    updateSendVerificationButtonEnabled();

    $sendBtn.on('click', function () {
        const email = String($email.val() || '').trim();
        const fullName = String($fullName.val() || '').trim();
        if (!email) {
            setStatus($sendStatus, msgEmailRequired, 'error');
            setStatus($codeStatus, '', 'muted');
            return;
        }
        if (!fullName) {
            setStatus($sendStatus, msgFullNameRequiredBeforeCode, 'error');
            setStatus($codeStatus, '', 'muted');
            return;
        }
        if (internalReservedFromCheck) {
            return;
        }
        if (verificationSendInFlight || Date.now() < verificationSendCooldownUntil) {
            return;
        }
        setStatus($sendStatus, '', 'muted');
        setStatus($codeStatus, '', 'muted');
        verificationSendInFlight = true;
        $sendBtn.prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: routeRequest,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            data: {
                _token: csrfToken,
                email,
                full_name: fullName,
            },
            success: function (data) {
                const msg = data && data.message ? String(data.message).trim() : '';
                setStatus($sendStatus, '', 'muted');
                $codeRow.prop('hidden', false);
                setStatus($codeStatus, msg, 'success');
                $codeInput.trigger('focus');
                verificationSendCooldownUntil = Date.now() + verificationSendCooldownMs;
                window.setTimeout(updateSendVerificationButtonEnabled, verificationSendCooldownMs);
            },
            error: function (xhr) {
                const body = xhr.responseJSON;
                const msg = jsonErrorMessage(body, xhr.statusText || 'Error');
                if ($codeRow.prop('hidden')) {
                    setStatus($sendStatus, msg, 'error');
                    setStatus($codeStatus, '', 'muted');
                } else {
                    setStatus($codeStatus, msg, 'error');
                    setStatus($sendStatus, '', 'muted');
                }
            },
            complete: function () {
                verificationSendInFlight = false;
                updateSendVerificationButtonEnabled();
            },
        });
    });

    $confirmBtn.on('click', function () {
        const email = String($email.val() || '').trim();
        const rawCode = String($codeInput.val() || '').replace(/\s+/g, '');
        if (!email) {
            setStatus($codeStatus, msgEmailRequired, 'error');
            return;
        }
        if (!/^[0-9]{6}$/.test(rawCode)) {
            setStatus($codeStatus, msgCodeRequired, 'error');
            return;
        }
        const fullNameForConfirm = String($fullName.val() || '').trim();
        if (!fullNameForConfirm) {
            setStatus($codeStatus, msgFullNameRequired, 'error');
            return;
        }
        setStatus($codeStatus, '', 'muted');
        $confirmBtn.prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: routeConfirm,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            data: {
                _token: csrfToken,
                email,
                code: rawCode,
                full_name: fullNameForConfirm,
            },
            success: function (data) {
                const msg = data && data.message ? String(data.message).trim() : '';
                setStatus($codeStatus, msg, 'success');
                const $cid = $form.find('.js-verified-collaborator-id');
                if ($cid.length && data && data.collaborator_id) {
                    $cid.val(String(data.collaborator_id));
                }
                triggerCheckContactIfPossible($form);
            },
            error: function (xhr) {
                const body = xhr.responseJSON;
                const msg = jsonErrorMessage(body, xhr.statusText || 'Error');
                setStatus($codeStatus, msg, 'error');
            },
            complete: function () {
                $confirmBtn.prop('disabled', false);
            },
        });
    });
}

function initAll() {
    document.querySelectorAll('form[data-route-request-verification-code]').forEach(initCatalogEmailVerificationForm);
}

$(function () {
    initAll();
});
