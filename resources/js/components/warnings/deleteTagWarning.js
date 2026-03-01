import i18next from 'i18next';

$(document).ready(function () {
    $('.deleteTagButton').click(function () {
        const confirmation = confirm(i18next.t('warnings.delete_tag'));

        if (!confirmation) event.preventDefault();
    });
});
