import i18next from 'i18next';

$(document).ready(function () {
    $('.deleteComponentButton').click(function () {
        const confirmation = confirm(i18next.t('warnings.delete_component'));

        if (!confirmation) event.preventDefault();
    });
});
