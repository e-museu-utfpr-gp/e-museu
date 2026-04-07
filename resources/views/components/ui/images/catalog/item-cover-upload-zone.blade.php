@props([
    'inputName',
    'inputId',
    'dropZoneId',
    'dropZoneClass' => 'upload-drop-zone',

    'placeholderId',
    'placeholderIconClass' => 'bi bi-image',
    'placeholderIconFontSize' => '2rem',

    'previewId',
    'previewImgId',
    'previewMaxHeightPx' => 120,
    'previewImgSrc' => '',

    'replaceBtnId',

    'requiredMsgId',
    'placeholderHidden' => false,
    'previewHidden' => true,
    'errorKey' => 'cover_image',
])

<div id="{{ $dropZoneId }}"
    class="{{ $dropZoneClass }}"
    style="cursor: pointer; border-style: dashed; border-color: #c8e6c9; background: #f1f8e9;">
    <input type="file"
        name="{{ $inputName }}"
        id="{{ $inputId }}"
        accept="image/jpeg,image/png,image/jpg,image/webp"
        class="d-none">

    <div id="{{ $placeholderId }}" class="{{ $placeholderHidden ? 'd-none' : '' }}">
        <i class="{{ $placeholderIconClass }} text-secondary mb-2" style="font-size: {{ $placeholderIconFontSize }};"></i>
        <p class="text-muted small mb-0">{{ __('view.shared.images_upload.cover_drop_here') }}</p>
    </div>

    <div id="{{ $previewId }}" class="{{ $previewHidden ? 'd-none' : '' }} position-relative">
        <img id="{{ $previewImgId }}" src="{{ $previewImgSrc }}" alt=""
            class="rounded shadow-sm"
            style="max-height: {{ $previewMaxHeightPx }}px; max-width: 100%; object-fit: contain;">
        <span class="badge bg-primary position-absolute top-0 start-0 m-1">{{ __('app.catalog.item_image.cover') }}</span>

        <x-ui.buttons.default type="button" variant="outline-secondary" size="sm"
            class="position-absolute bottom-0 end-0 m-1"
            id="{{ $replaceBtnId }}">
            {{ __('view.shared.images_upload.replace_image') }}
        </x-ui.buttons.default>
    </div>
</div>

<p id="{{ $requiredMsgId }}" class="text-danger small mt-1 d-none">{{ __('view.shared.images_upload.cover_required') }}</p>

@error($errorKey)
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
