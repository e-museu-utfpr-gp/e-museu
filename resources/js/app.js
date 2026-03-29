import './bootstrap';
import './i18n';

import './shared/catalog/upload-utils';

import './shared/ui/img-modal';
import './shared/ui/popover-button';

import './pages/catalog/items/index/explore-scroll';

import './pages/admin/collaborators/check-contact';

/**
 * Item contribution wizard: only on the create page. Top-level await keeps this off other
 * public routes (e.g. catalog/items/:id) and ensures modal onclick handlers exist after load.
 */
if (document.getElementById('item-create-form')) {
    await Promise.all([
        import('./pages/catalog/items/create/modals/tag-modal'),
        import('./pages/catalog/items/create/modals/extra-modal'),
        import('./pages/catalog/items/create/modals/component-modal'),
        import('./pages/catalog/items/create/form-session-restore'),
        import('./pages/catalog/items/create/item-images-upload'),
    ]);
}
