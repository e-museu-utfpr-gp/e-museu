import i18next from 'i18next';

$(document).ready(function () {
    $('.deleteContributionButton').click(function () {
        const confirmation = confirm(i18next.t('warnings.delete_contribution'));

        if (!confirmation) event.preventDefault();
    });
});
