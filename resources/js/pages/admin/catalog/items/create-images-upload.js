import { initCatalogItemImageUploadForm } from '../../../../shared/catalog/catalog-item-image-upload-form';

initCatalogItemImageUploadForm({
    formId: 'admin-item-create-form',
    initRegistryKey: 'admin-item-create-form',
    cover: {
        inputId: 'admin_create_cover_image',
        zoneId: 'admin-create-cover-drop-zone',
        replaceBtnSelector: '#admin-create-cover-replace-btn',
        placeholderId: 'admin-create-cover-placeholder',
        previewWrapId: 'admin-create-cover-preview',
        previewImgId: 'admin-create-cover-preview-img',
        requiredMsgId: 'admin-create-cover-required-msg',
        invalidHighlightZoneId: 'admin-create-cover-drop-zone',
    },
    gallery: {
        zoneId: 'admin-create-gallery-drop-zone',
        inputId: 'admin_create_gallery_input',
    },
    preview: {
        containerId: 'admin-create-images-preview',
        emptyId: 'admin-create-images-preview-empty',
        thumbClass: 'admin-create-image-thumb',
        thumbWidthPx: 72,
        thumbHeightPx: 72,
        badgeFontSize: '9px',
        removeBtnClassName: 'btn btn-danger btn-sm position-absolute bottom-0 end-0 m-1 p-0',
        removeBtnInlineStyle: {
            fontSize: '10px',
            width: '20px',
            height: '20px',
        },
    },
    requireCoverOnSubmit: true,
});
