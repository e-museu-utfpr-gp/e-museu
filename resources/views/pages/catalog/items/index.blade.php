<x-layouts.app :title="__('view.catalog.items.index.title')">
    @include('pages.catalog.items._partials.index.explore.menu')
    <div class="container main-container mb-auto min-w-0">
        @if (request()->query('item_category') == '')
            <h1>{{ __('view.catalog.items.index.heading_all') }}</h1>
        @else
            <h1>{{ __('view.catalog.items.index.heading_in_category', ['category' => $categoryName]) }}</h1>
        @endif
        <div class="row">
            <div class="col-md-2 d-none d-md-block">
                @include('pages.catalog.items._partials.index.filter-menu')
            </div>
            <div class="col-2 d-block d-md-none">
                <x-ui.buttons.default type="button" variant="primary" class="d-md-none toggle-filter-button-mobile py-2"
                    data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                    <i class="bi bi-funnel-fill h3"></i>
                </x-ui.buttons.default>

                <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel"
                    style="overflow-y: scroll;">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="sidebarLabel">
                            {{ __('view.catalog.items.index.filters_title') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        @include('pages.catalog.items._partials.index.filter-menu')
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="content row">
                    @foreach ($items as $item)
                        @include('pages.catalog.items._partials.index.item-card')
                    @endforeach
                </div>
                @if (!$items->first())
                    <h4 class="fw-bold">
                        {{ __('view.catalog.items.index.none_found') }}
                    </h4>
                @endif
            </div>
        </div>
        <x-ui.pagination :paginator="$items" variant="primary" class="mx-5" />
    </div>
</x-layouts.app>
