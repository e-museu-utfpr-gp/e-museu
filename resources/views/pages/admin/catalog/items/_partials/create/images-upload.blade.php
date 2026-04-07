<div>
    <div class="mb-3">
        <label class="form-label">
            {{ __('view.shared.images_upload.cover_label') }}
            <x-ui.info-popover :content="__('view.shared.images_upload.cover_help')" />
        </label>

        <x-ui.images.catalog.item-cover-upload-zone
            inputName="cover_image"
            inputId="admin_create_cover_image"
            dropZoneId="admin-create-cover-drop-zone"
            dropZoneClass="upload-drop-zone rounded-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center p-3 text-center"
            placeholderId="admin-create-cover-placeholder"
            placeholderIconFontSize="1.5rem"
            previewId="admin-create-cover-preview"
            previewImgId="admin-create-cover-preview-img"
            previewMaxHeightPx="100"
            replaceBtnId="admin-create-cover-replace-btn"
            requiredMsgId="admin-create-cover-required-msg"
        />
    </div>

    <div class="mb-3">
        <label class="form-label">
            {{ __('view.shared.images_upload.gallery_label') }}
            <x-ui.info-popover :content="__('view.shared.images_upload.gallery_help')" />
        </label>

        <x-ui.images.catalog.item-gallery-upload-zone
            inputName="gallery_images[]"
            inputId="admin_create_gallery_input"
            dropZoneId="admin-create-gallery-drop-zone"
            dropZoneClass="upload-drop-zone rounded-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center p-3 text-center"
            iconFontSize="1.25rem"
        />
    </div>

    <div class="mb-3 rounded-3 p-3" style="background-color: #e8f5e9;">
        <h6 class="mb-2">{{ __('view.shared.images_upload.images_preview_title') }}</h6>
        <div id="admin-create-images-preview" class="d-flex flex-wrap gap-2 align-items-start">
            <p class="text-muted small mb-0" id="admin-create-images-preview-empty">{{ __('view.shared.images_upload.images_preview_empty') }}</p>
        </div>
    </div>
</div>
