import i18next from 'i18next';

$(document).ready(function () {
    $('.deleteProprietaryButton, .deleteCollaboratorButton').click(function () {
        const message = $(this).data('confirm-message') || i18next.t('warnings.delete_collaborator');
        const confirmation = confirm(message);

        if (!confirmation) event.preventDefault();
    });
});
