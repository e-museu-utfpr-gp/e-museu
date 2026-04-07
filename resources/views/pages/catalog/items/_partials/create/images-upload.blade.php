<div>
    <div class="mb-3">
        <h5 class="mb-2">
            {{ __('view.shared.images_upload.cover_label') }} <span class="text-danger">*</span>
            <x-ui.info-popover :content="__('view.shared.images_upload.cover_help')" />
        </h5>

        <x-ui.images.catalog.item-cover-upload-zone
            inputName="cover_image"
            inputId="cover_image"
            dropZoneId="cover-drop-zone"
            dropZoneClass="upload-drop-zone rounded-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center p-4 text-center"
            placeholderId="cover-placeholder"
            placeholderIconFontSize="2rem"
            previewId="cover-preview"
            previewImgId="cover-preview-img"
            previewMaxHeightPx="120"
            replaceBtnId="cover-replace-btn"
            requiredMsgId="cover-required-msg"
        />
    </div>

    <div class="mb-3">
        <h5 class="mb-2">
            {{ __('view.shared.images_upload.gallery_label') }}
            <x-ui.info-popover :content="__('view.shared.images_upload.gallery_help')" />
        </h5>

        <x-ui.images.catalog.item-gallery-upload-zone
            inputName="gallery_images[]"
            inputId="gallery_input"
            dropZoneId="gallery-drop-zone"
            dropZoneClass="upload-drop-zone rounded-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center p-4 text-center"
        />
    </div>

    <h5 class="mb-2 mt-3">{{ __('view.shared.images_upload.images_preview_title') }}</h5>
    <div class="mb-4 catalog-images-preview-panel rounded-3 p-3">
        <div id="images-preview" class="d-flex flex-wrap gap-3 align-items-start">
            <p class="text-muted mb-0" id="images-preview-empty">{{ __('view.shared.images_upload.images_preview_empty') }}</p>
        </div>
    </div>
</div>