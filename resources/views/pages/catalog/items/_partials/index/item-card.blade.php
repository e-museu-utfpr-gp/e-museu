<div class="col pb-5 d-flex justify-content-center">
    <a href="{{ route('catalog.items.show', $item->id) }}" class="card d-flex card-anim"
        style="width: 18rem; height: 28rem;">
        @if ($item->image_url)
            <img src="{{ $item->image_url }}" class="card-img-top p-1" style="height: 12rem; object-fit: cover;"
                alt="{{ __('view.catalog.items.index.image_alt') }}">
        @endif

        <div class="card-body">
            <h6 class="card-title fw-bold border-dark">{{ Str::limit($item->name, 40) }}</h6>
            <p class="border-dark">{{ $item->identification_code }}</p>
            <div class="division-line my-1"></div>

            <div class="d-flex justify-content-between pt-1">
                <p class="card-subtitle border- fw-bold">{{ $item->itemCategory?->name }}</p>
                @if ($item->date)
                    <p class="card-subtitle">{{ date('d/m/Y', strtotime($item->date)) }}</p>
                @else
                    <p class="card-subtitle">{{ __('view.catalog.items.index.date_unknown') }}</p>
                @endif
            </div>
            @if ($item->location)
                <p class="card-subtitle text-muted small mb-0">{{ $item->location->localized_label }}</p>
            @endif

            <div class="division-line my-1"></div>
            <p class="card-text">{{ Str::limit($item->description, 100) }}</p>
        </div>
    </a>
</div>

