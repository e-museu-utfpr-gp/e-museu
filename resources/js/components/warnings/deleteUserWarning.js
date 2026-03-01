import i18next from 'i18next';

$(document).ready(function () {
    $('.deleteUserButton').click(function () {
        const confirmation = confirm(i18next.t('warnings.delete_user'));

        if (!confirmation) event.preventDefault();
    });
});
