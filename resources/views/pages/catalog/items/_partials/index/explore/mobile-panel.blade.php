@php
    $catalogExploreQueryBase = \Illuminate\Support\Arr::except(request()->query(), ['page']);
@endphp
<div class="explore-menu-div-mobile mt-0 sticky-top" id="sub-menu">
    <label class="fw-bold" for="search-mobile">{{ __('view.catalog.items.explore.search_placeholder') }}</label>
    <form action="{{ route('catalog.items.index') }}" method="GET" class="row">
        <input name="item_category" value="{{ request()->query('item_category') }}" hidden>
        @foreach (request()->input('category', []) as $cid)
            <input type="hidden" name="category[]" value="{{ $cid }}">
        @endforeach
        @foreach (request()->input('tag', []) as $tid)
            <input type="hidden" name="tag[]" value="{{ $tid }}">
        @endforeach
        <input type="hidden" name="order" value="{{ request()->query('order', 1) }}">
        <input type="hidden" name="location_id" value="{{ request()->query('location_id', '') }}">
        <div class="input-div m-0 mt-2 col-10">
            <input class="form-control" type="text" name="search" id="search-mobile"
                value="{{ request('search', '') }}" placeholder="">
        </div>
        <button type="submit" class="button nav-link mt-1 px-3 fw-bold col-2" aria-label="{{ __('view.shared.buttons.search') }}">
            <i class="h4 bi bi-search"></i>
        </button>
    </form>
    <div class="division-line my-1"></div>
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
