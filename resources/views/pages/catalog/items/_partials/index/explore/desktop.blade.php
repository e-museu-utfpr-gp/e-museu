<div class="explore-menu-div sticky-top-2 d-none d-md-block" id="sub-menu">
    <div class="container d-flex">
        <div class="col-3 m-0">
            <label class="fw-bold" for="search">{{ __('view.catalog.items.explore.search_placeholder') }}</label>
            <form action="{{ route('catalog.items.index') }}" method="GET" class="d-flex">
                <input name="item_category" value="{{ request()->query('item_category') }}" hidden>
                <div class="input-div m-0 mt-1">
                    <input class="form-control input-form" type="text" name="search" id="search" placeholder="">
                </div>
                <button class="button nav-link px-3 fw-bold"><i class="h4 bi bi-search"></i></button>
            </form>
        </div>
        <div class="d-flex col-9">
            <div class="left-arrow menu-arrows px-4 h3 d-flex align-items-center">
                <i class="bi bi-chevron-left"></i>
            </div>
            <div class="d-flex explore-menu-options">
                <a href="{{ route('catalog.items.index') }}" class="explore-menu-option">
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
            <div class="right-arrow menu-arrows px-4 h3 d-flex align-items-center">
                <i class="bi bi-chevron-right"></i>
            </div>
        </div>
    </div>
</div>
