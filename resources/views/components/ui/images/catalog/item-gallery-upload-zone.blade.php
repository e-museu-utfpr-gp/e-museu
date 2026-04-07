@props([
    'inputName',
    'inputId',
    'dropZoneId',
    'dropZoneClass' => 'upload-drop-zone',

    'iconClass' => 'bi bi-images',
    'iconFontSize' => '1.5rem',

    'errorKey' => 'gallery_images.*',
])

<div id="{{ $dropZoneId }}"
    class="{{ $dropZoneClass }}"
    style="cursor: pointer; border-style: dashed; border-color: #c8e6c9; background: #f1f8e9;">
    <input type="file"
        name="{{ $inputName }}"
        id="{{ $inputId }}"
        accept="image/jpeg,image/png,image/jpg,image/webp"
        class="d-none"
        multiple>

    <i class="{{ $iconClass }} text-secondary mb-2" style="font-size: {{ $iconFontSize }};"></i>
    <p class="text-muted small mb-0">{{ __('view.shared.images_upload.gallery_drop_here') }}</p>
</div>

@error($errorKey)
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
