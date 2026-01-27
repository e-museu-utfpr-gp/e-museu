import $ from 'jquery';

$(document).ready(function () {
    const $contactInput = $('#contact');
    const $contactWarning = $('#contact-warning');
    const $contactSuccess = $('#contact-success');
    const $fullNameInput = $('#full_name');

    if (!$contactInput.length) {
        return;
    }

    const checkContactRoute = window.checkContactRoute || '/check-contact';

    function checkContact() {
        const contact = $contactInput.val();

        if (!contact) {
            $contactWarning.prop('hidden', true);
            $contactSuccess.prop('hidden', true);
            return;
        }

        $.ajax({
            type: 'GET',
            url: checkContactRoute,
            data: {
                contact: contact,
            },
            success: function (data) {
                if (data == false) {
                    $contactWarning.prop('hidden', false);
                    $contactSuccess.prop('hidden', true);
                } else {
                    $contactWarning.prop('hidden', true);
                    $contactSuccess.prop('hidden', false);
                    if ($fullNameInput.length) {
                        $fullNameInput.val(data.full_name || '');
                    }
                }
            },
            error: function () {
                $contactWarning.prop('hidden', false);
                $contactSuccess.prop('hidden', true);
            },
        });
    }

    $contactInput.on('blur', checkContact);
    $contactInput.on('input', function () {
        if ($(this).val() === '') {
            $contactWarning.prop('hidden', true);
            $contactSuccess.prop('hidden', true);
        }
    });
});
