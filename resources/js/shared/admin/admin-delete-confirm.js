import $ from 'jquery';
import i18next from 'i18next';

/**
 * Admin delete links/buttons: confirm() before navigation/submit.
 * Optional per-element override: data-confirm-message="…"
 */
const ADMIN_DELETE_CONFIRM = [
    { selector: '.deleteCategoryButton', i18nKey: 'warnings.delete_category' },
    { selector: '.deleteTagButton', i18nKey: 'warnings.delete_tag' },
    { selector: '.deleteItemButton', i18nKey: 'warnings.delete_item' },
    { selector: '.deleteExtraButton', i18nKey: 'warnings.delete_extra' },
    { selector: '.deleteComponentButton', i18nKey: 'warnings.delete_component' },
    { selector: '.deleteItemTagButton', i18nKey: 'warnings.delete_item_tag' },
    { selector: '.deleteItemCategoryButton', i18nKey: 'warnings.delete_item_category' },
    { selector: '.deleteUserButton, .deleteAdminButton', i18nKey: 'warnings.delete_admin' },
    { selector: '.deleteProprietaryButton, .deleteCollaboratorButton', i18nKey: 'warnings.delete_collaborator' },
];

$(document).ready(function () {
    ADMIN_DELETE_CONFIRM.forEach(function ({ selector, i18nKey }) {
        $(document).on('click', selector, function (event) {
            const custom = $(this).attr('data-confirm-message');
            const fallback = 'Are you sure you want to delete this?';
            const message =
                custom !== undefined && custom !== null && String(custom).trim() !== ''
                    ? String(custom)
                    : i18next.exists(i18nKey)
                      ? i18next.t(i18nKey)
                      : fallback;
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
