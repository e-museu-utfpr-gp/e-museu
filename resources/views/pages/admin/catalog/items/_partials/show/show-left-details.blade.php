<div class="col-md-6">
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.id') }}</h5>
        <div class="card-body">
            <p class="card-text">{{ $item->id }}</p>
        </div>
    </div>
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.name') }}</h5>
        <div class="card-body">
            <p class="card-text">{{ $item->name }}</p>
        </div>
    </div>
    @php
        $sortedForShow = $item->images->sortBy('sort_order')->values();
        $coverImage = $sortedForShow->first(fn ($img) => $img->type->value === 'cover') ?? $sortedForShow->first();
        $galleryImages = $coverImage
            ? $sortedForShow->filter(fn ($img) => $img->id !== $coverImage->id)
            : $sortedForShow;
    @endphp
    @if ($coverImage)
        <div class="card mb-3">
            <h5 class="card-header">{{ __('app.catalog.item_image.cover') }}</h5>
            <div class="card-body">
                <img src="{{ $coverImage->image_url }}" class="img-thumbnail clickable-image myImg" alt=""
                    style="max-height: 200px; max-width: 100%; object-fit: contain;">
            </div>
        </div>
    @endif
    @if ($galleryImages->isNotEmpty())
        <div class="card mb-3">
            <h5 class="card-header">{{ __('app.catalog.item_image.gallery') }}</h5>
            <div class="card-body d-flex flex-wrap gap-2">
                @foreach ($galleryImages as $img)
                    <div class="position-relative">
                        <img src="{{ $img->image_url }}" class="img-thumbnail clickable-image myImg" alt=""
                            style="max-height: 120px;">
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    @if ($item->images->isEmpty())
        <div class="card mb-3">
            <h5 class="card-header">{{ __('view.admin.catalog.items.show.image') }}</h5>
            <div class="card-body">
                <p class="text-muted mb-0">{{ __('view.admin.catalog.items.show.no_images') }}</p>
            </div>
        </div>
    @endif

    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.description') }}</h5>
        <div class="card-body">
            <p class="card-text">{{ $item->description }}</p>
        </div>
    </div>
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.detail') }}</h5>
        <div class="card-body">
            <p class="card-text">{!! nl2br($item->detail) !!}</p>
        </div>
    </div>
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.date') }}</h5>
        <div class="card-body">
            <p class="card-text">{{ $item->date ? date('d-m-Y', strtotime($item->date)) : '—' }}</p>
        </div>
    </div>
</div>

