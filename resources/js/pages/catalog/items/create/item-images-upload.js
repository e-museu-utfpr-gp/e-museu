import { initCatalogItemImageUploadForm } from '../../../../shared/catalog/catalog-item-image-upload-form';

initCatalogItemImageUploadForm({
    formId: 'item-create-form',
    initRegistryKey: 'item-create-form',
    cover: {
        inputId: 'cover_image',
        zoneId: 'cover-drop-zone',
        replaceBtnSelector: '#cover-replace-btn',
        placeholderId: 'cover-placeholder',
        previewWrapId: 'cover-preview',
        previewImgId: 'cover-preview-img',
        requiredMsgId: 'cover-required-msg',
        invalidHighlightZoneId: 'cover-drop-zone',
    },
    gallery: {
        zoneId: 'gallery-drop-zone',
        inputId: 'gallery_input',
    },
    preview: {
        containerId: 'images-preview',
        emptyId: 'images-preview-empty',
        thumbClass: 'image-preview-thumb',
        thumbWidthPx: 88,
        thumbHeightPx: 88,
        badgeFontSize: '10px',
        removeBtnClassName: 'btn btn-danger btn-sm position-absolute bottom-0 end-0 m-1 p-1',
        removeBtnInlineStyle: { fontSize: '10px' },
    },
    requireCoverOnSubmit: true,
});
