@php
    $catalogExploreQueryBase = \Illuminate\Support\Arr::except(request()->query(), ['page']);
@endphp
<div class="explore-menu-div sticky-top-2 d-none d-md-block" id="sub-menu">
    <div class="container d-flex min-w-0">
        <div class="col-3 m-0 min-w-0">
            <label class="fw-bold" for="search">{{ __('view.catalog.items.explore.search_placeholder') }}</label>
            <form action="{{ route('catalog.items.index') }}" method="GET" class="d-flex">
                <input name="item_category" value="{{ request()->query('item_category') }}" hidden>
                @foreach (request()->input('category', []) as $cid)
                    <input type="hidden" name="category[]" value="{{ $cid }}">
                @endforeach
                @foreach (request()->input('tag', []) as $tid)
                    <input type="hidden" name="tag[]" value="{{ $tid }}">
                @endforeach
                <input type="hidden" name="order" value="{{ request()->query('order', 1) }}">
                <div class="input-div m-0 mt-1 flex-grow-1 min-w-0">
                    <input class="form-control input-form" type="text" name="search" id="search"
                        value="{{ request('search', '') }}" placeholder="">
                </div>
                <button type="submit" class="button nav-link px-3 fw-bold flex-shrink-0" aria-label="{{ __('view.shared.buttons.search') }}">
                    <i class="h4 bi bi-search"></i>
                </button>
            </form>
        </div>
        <div class="d-flex col-9 min-w-0 align-items-stretch">
            <div class="left-arrow menu-arrows px-4 h3 d-flex align-items-center flex-shrink-0">
                <i class="bi bi-chevron-left"></i>
            </div>
            <div class="explore-menu-options flex-grow-1 min-w-0">
                <a href="{{ route('catalog.items.index', [], false) }}?{{ http_build_query(array_merge($catalogExploreQueryBase, ['item_category' => ''])) }}" class="explore-menu-option">
                    <div
                        class="nav-link menu-option py-4 px-4 fw-bold @if (request()->query('item_category') == '') menu-option-active @endif">
                        {{ __('view.catalog.items.explore.all') }}
                    </div>
                </a>
                @foreach ($itemCategories as $itemCategory)
                    <a href="{{ route('catalog.items.index', [], false) }}?{{ http_build_query(array_merge($catalogExploreQueryBase, ['item_category' => (string) $itemCategory->id])) }}" class="explore-menu-option">
                        <div
                            class="nav-link menu-option py-4 px-4 fw-bold @if (request()->query('item_category') == $itemCategory->id) menu-option-active @endif">
                            {{ $itemCategory->name }}
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="right-arrow menu-arrows px-4 h3 d-flex align-items-center flex-shrink-0">
                <i class="bi bi-chevron-right"></i>
            </div>
        </div>
    </div>
</div>
