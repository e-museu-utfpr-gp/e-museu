import i18next from 'i18next';

$(document).ready(function () {
    $('.deleteUserButton, .deleteAdminButton').click(function () {
        const message = $(this).data('confirm-message') || i18next.t('warnings.delete_admin');
        const confirmation = confirm(message);

        if (!confirmation) event.preventDefault();
    });
});
