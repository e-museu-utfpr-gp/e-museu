<div class="mb-3">
    <label class="form-label">
        {{ __('view.shared.images_upload.gallery_label') }}
        <x-ui.info-popover :content="__('view.shared.images_upload.gallery_help')" />
    </label>

    <x-ui.images.catalog.item-gallery-upload-zone
        inputName="gallery_images[]"
        inputId="admin_gallery_input"
        dropZoneId="admin-gallery-drop-zone"
        dropZoneClass="upload-drop-zone rounded-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center p-3 text-center"
        iconFontSize="1.25rem"
    />
</div>

