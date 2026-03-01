import i18next from 'i18next';

$(document).ready(function () {
    $('.deleteCategoryButton').click(function (event) {
        const message = $(this).data('confirm-message') || i18next.t('warnings.delete_category');
        const confirmation = confirm(message);

        if (!confirmation) event.preventDefault();
    });
});
