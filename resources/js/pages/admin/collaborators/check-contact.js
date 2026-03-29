import $ from 'jquery';

$(document).ready(function () {
    const $contactInput = $('#contact');
    const $contactWarning = $('#contact-warning');
    const $contactSuccess = $('#contact-success');
    const $fullNameInput = $('#full_name');

    if (!$contactInput.length) {
        return;
    }

    function resolveCheckContactRoute() {
        if (window.checkContactRoute) {
            return window.checkContactRoute;
        }
        const createForm = document.getElementById('item-create-form');
        const fromCreate = createForm && createForm.getAttribute('data-check-contact-route');
        if (fromCreate) {
            return fromCreate;
        }
        const extraForm = document.getElementById('addExtraForm');
        const fromExtra = extraForm && extraForm.getAttribute('data-check-contact-route');
        if (fromExtra) {
            return fromExtra;
        }
        const withAttr = document.querySelector('[data-check-contact-route]');
        if (withAttr) {
            return withAttr.getAttribute('data-check-contact-route');
        }
        return '';
    }

    const checkContactRoute = resolveCheckContactRoute();
    if (!checkContactRoute) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const KEYUP_DEBOUNCE_MS = 300;
    let keyupTimer = null;
    let checkContactXhr = null;

    function checkContact() {
        const contact = $contactInput.val();

        if (!contact) {
            $contactWarning.prop('hidden', true);
            $contactSuccess.prop('hidden', true);
            return;
        }

        if (checkContactXhr) {
            checkContactXhr.abort();
        }

        const xhr = $.ajax({
            type: 'POST',
            url: checkContactRoute,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            data: {
                _token: csrfToken,
                contact: contact,
            },
            success: function (data) {
                if (data === false) {
                    $contactWarning.prop('hidden', false);
                    $contactSuccess.prop('hidden', true);
                } else {
                    $contactWarning.prop('hidden', true);
                    $contactSuccess.prop('hidden', false);
                    if ($fullNameInput.length && data && typeof data === 'object') {
                        $fullNameInput.val(data.full_name || '');
                    }
                }
            },
            error: function (_jq, textStatus) {
                if (textStatus === 'abort') {
                    return;
                }
                $contactWarning.prop('hidden', false);
                $contactSuccess.prop('hidden', true);
            },
            complete: function () {
                if (checkContactXhr === xhr) {
                    checkContactXhr = null;
                }
            },
        });
        checkContactXhr = xhr;
    }

    function scheduleCheckContact() {
        if (keyupTimer !== null) {
            clearTimeout(keyupTimer);
        }
        keyupTimer = setTimeout(function () {
            keyupTimer = null;
            checkContact();
        }, KEYUP_DEBOUNCE_MS);
    }

    $contactInput.on('blur', checkContact);
    if ($contactInput.closest('#addExtraForm').length) {
        $contactInput.on('change', checkContact);
        $contactInput.on('keyup', scheduleCheckContact);
    }
    $contactInput.on('input', function () {
        if ($(this).val() === '') {
            $contactWarning.prop('hidden', true);
            $contactSuccess.prop('hidden', true);
        }
    });
});
