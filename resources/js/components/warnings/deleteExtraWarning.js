import i18next from 'i18next';

$(document).ready(function () {
    $('.deleteExtraButton').click(function (event) {
        const message = $(this).data('confirm-message') || i18next.t('warnings.delete_extra');
        const confirmation = confirm(message);

        if (!confirmation) event.preventDefault();
    });
});
