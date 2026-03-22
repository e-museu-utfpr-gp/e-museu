<div class="explore-menu-div-mobile mt-0 sticky-top" id="sub-menu">
    <label class="fw-bold" for="search">{{ __('view.catalog.items.explore.search_placeholder') }}</label>
    <form action="{{ route('catalog.items.index') }}" method="GET" class="row">
        <input name="item_category" value="{{ request()->query('item_category') }}" hidden>
        <div class="input-div m-0 mt-2 col-10">
            <input class="form-control" type="text" name="search" id="search" placeholder="">
        </div>
        <button class="button nav-link mt-1 px-3 fw-bold col-2"><i class="h4 bi bi-search"></i></button>
    </form>
    <div class="division-line my-1"></div>
    <a href="{{ route('catalog.items.index', ['item_category' => '']) }}" class="explore-menu-option">
        <div
            class="nav-link menu-option py-4 px-4 fw-bold @if (request()->query('item_category') == '') menu-option-active @endif">
            {{ __('view.catalog.items.explore.all') }}
        </div>
    </a>
    @foreach ($itemCategories as $itemCategory)
        <a href="{{ route('catalog.items.index', ['item_category' => $itemCategory->id]) }}" class="explore-menu-option">
            <div
                class="nav-link menu-option py-4 px-4 fw-bold @if (request()->query('item_category') == $itemCategory->id) menu-option-active @endif">
                {{ $itemCategory->name }}
            </div>
        </a>
    @endforeach
</div>
