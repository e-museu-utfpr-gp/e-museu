import './bootstrap';
import './i18n';

import './shared/catalog/upload-utils';

import './shared/ui/img-modal';
import './shared/ui/popover-button';
import './shared/ui/admin-password-toggle';
import './shared/ui/enhanced-select';

import './pages/catalog/items/index/explore-scroll';

import './pages/catalog/collaborators/email-verification-code';
import './pages/admin/collaborators/check-contact';

if (document.querySelector('form[data-extra-clear-session-on-hide][data-route-clear-contribution-session]')) {
    import('./pages/catalog/items/show/extra-modal-clear-session-on-hide');
}

/**
 * Item contribution wizard: only on the create page. Top-level await keeps this off other
 * public routes (e.g. catalog/items/:id) and ensures modal onclick handlers exist after load.
 */
if (document.getElementById('item-create-form')) {
    await import('./pages/catalog/items/create/item-images-upload');
    await Promise.all([
        import('./pages/catalog/items/create/modals/tag-modal'),
        import('./pages/catalog/items/create/modals/extra-modal'),
        import('./pages/catalog/items/create/modals/component-modal'),
        import('./pages/catalog/items/create/form-session-restore'),
    ]);
    await import('./pages/catalog/items/create/clear-item-create-form');
}
