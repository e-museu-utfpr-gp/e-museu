<button class="button navbar-toggler p-1 nav-link justify-content-between d-flex d-md-none py-3" type="button"
    data-bs-toggle="collapse" data-bs-target="#categoriesToggle" aria-controls="categoriesToggle" aria-expanded="false"
    aria-label="Toggle navigation">
    @if (request()->query('item_category') == '')
        <h4 class="ms-3 fw-bold">{{ __('view.catalog.items.explore.all') }}</h4>
    @else
        @foreach ($itemCategories as $itemCategory)
            @if ($itemCategory->id == request()->query('item_category'))
                <h4 class="ms-3 fw-bold">{{ $itemCategory->name }}</h4>
            @endif
        @endforeach
    @endif
    <i class="bi bi-caret-down-fill me-3"></i>
</button>
