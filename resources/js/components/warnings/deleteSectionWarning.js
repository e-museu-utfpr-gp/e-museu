import i18next from 'i18next';

$(document).ready(function () {
    $('.deleteSectionButton').click(function (event) {
        const message = $(this).data('confirm-message') || i18next.t('warnings.delete_section');
        const confirmation = confirm(message);

        if (!confirmation) event.preventDefault();
    });
});
