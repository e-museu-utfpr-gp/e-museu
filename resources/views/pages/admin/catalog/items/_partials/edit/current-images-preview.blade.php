<div class="mb-3 rounded-3 p-3" style="background-color: #e8f5e9;">
    <h6 class="mb-2">{{ __('view.admin.catalog.items.edit.current_images') }}</h6>
    <div id="admin-images-preview" class="d-flex flex-wrap gap-2 align-items-start">
        @if ($item->images->isEmpty())
            <p class="text-muted small mb-0">{{ __('view.admin.catalog.items.show.no_images') }}</p>
        @else
            @php
                $sortedImages = $item->images->sortBy('sort_order')->values();
                $coverShown = false;
            @endphp
            @foreach ($sortedImages as $img)
                @php
                    $showAsCover = !$coverShown && $img->type->value === 'cover';
                    if ($showAsCover) {
                        $coverShown = true;
                    }
                @endphp
                <div class="position-relative d-inline-block admin-preview-thumb" @if ($showAsCover) id="admin-current-cover-thumb" @endif data-image-id="{{ $img->id }}">
                    <img src="{{ $img->image_url }}" class="img-thumbnail clickable-image" alt="" style="width: 72px; height: 72px; object-fit: cover;">
                    <span class="badge bg-{{ $showAsCover ? 'primary' : 'secondary' }} position-absolute top-0 start-0 m-1" style="font-size: 9px;">
                        {{ $showAsCover ? __('app.catalog.item_image.cover') : __('app.catalog.item_image.gallery') }}
                    </span>
                    @if (!$showAsCover)
                        <x-ui.buttons.default type="button" variant="outline-primary" size="sm"
                            class="position-absolute top-0 end-0 m-1 p-0 set-cover-btn" style="width: 22px; height: 22px; font-size: 11px; line-height: 1;"
                            title="{{ __('view.admin.catalog.items.edit.set_as_cover') }}" data-image-id="{{ $img->id }}"
                            icon="bi bi-image-fill" />
                    @endif
                    <x-ui.buttons.default type="button" variant="danger" size="sm"
                        class="position-absolute bottom-0 end-0 m-1 p-0 delete-image-btn" style="width: 22px; height: 22px; font-size: 12px; line-height: 1;"
                        title="{{ __('view.admin.catalog.items.edit.delete_image') }}"
                        data-image-id="{{ $img->id }}"
                        data-confirm="{{ __('view.admin.catalog.items.edit.delete_image_confirm') }}"
                        icon="bi bi-trash" />
                </div>
            @endforeach
        @endif
    </div>
</div>

