@php
    $hasCurrentCover = filled($currentCoverImage);
@endphp

<div class="mb-3">
    <label class="form-label">
        {{ __('view.shared.images_upload.cover_label') }}
        <x-ui.info-popover :content="__('view.shared.images_upload.cover_help')" />
    </label>

    <x-ui.images.catalog.item-cover-upload-zone
        inputName="image"
        inputId="image"
        dropZoneId="admin-cover-drop-zone"
        dropZoneClass="upload-drop-zone rounded-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center p-3 text-center"
        placeholderId="admin-cover-placeholder"
        placeholderIconFontSize="1.5rem"
        previewId="admin-cover-preview"
        previewImgId="admin-cover-preview-img"
        previewMaxHeightPx="100"
        :previewImgSrc="$currentCoverImage?->image_url ?? ''"
        replaceBtnId="admin-cover-replace-btn"
        requiredMsgId="admin-cover-required-msg"
        :placeholderHidden="$hasCurrentCover"
        :previewHidden="!$hasCurrentCover"
    />
</div>

