@php
    $suffix = $filterPanelSuffix ?? 'default';
    // Reset only sidebar filters (tag categories, tags, sort, location); keep explore item category and text search.
    $catalogFilterResetUrl = route('catalog.items.index', [], false) . '?' . http_build_query([
        'item_category' => (string) request()->query('item_category', ''),
        'search' => (string) request()->input('search', ''),
        'order' => '1',
        'location_id' => '',
    ]);
    $selectedTagCategoryIds = (array) request()->input('category', []);
    $selectedTagIds = (array) request()->input('tag', []);
    $hasTagOrCategoryFilters = count($selectedTagCategoryIds) > 0 || count($selectedTagIds) > 0;
    $orderIsNonDefault = (string) request()->query('order', '1') !== '1';
    $locationFilterActive = request()->filled('location_id');
    $openMainFilterPanel = $hasTagOrCategoryFilters || $orderIsNonDefault || $locationFilterActive;
@endphp
<div class="filter-menu">
    <div>
        <button class="toggle-filter-button d-flex justify-content-between fw-bold p-2 @unless ($openMainFilterPanel) collapsed @endunless" type="button"
            data-bs-toggle="collapse" data-bs-target="#toggleFilter-{{ $suffix }}" aria-controls="toggleFilter-{{ $suffix }}" aria-expanded="{{ $openMainFilterPanel ? 'true' : 'false' }}"
            aria-label="Toggle navigation">
            <div>
                <i class="bi bi-funnel-fill mx-1"></i> {{ __('view.catalog.items.filter.filter') }}
            </div>
            <i class="bi bi-caret-down-fill me-2"></i>
        </button>
    </div>
    <div class="collapse ms-3 @if ($openMainFilterPanel) show @endif" id="toggleFilter-{{ $suffix }}">
        <form action="{{ route('catalog.items.index') }}" method="GET">
            <input name="item_category" value="{{ request()->query('item_category') }}" hidden>
            <input name="search" value="{{ request()->query('search') }}" hidden>
            <input name="order" type="hidden" value="{{ request()->query('order', 1) }}">
            <div class="mb-2">
                <label class="fw-bold d-block" for="filter-location-id-{{ $suffix }}">{{ __('view.catalog.items.filter.location') }}</label>
                <select name="location_id" id="filter-location-id-{{ $suffix }}" class="form-select form-select-sm">
                    <option value="">{{ __('view.catalog.items.filter.location_all') }}</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected((string) request()->query('location_id') === (string) $location->id)>
                            {{ $location->localized_label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="division-line m-1"></div>
            @foreach ($categories as $category)
                @php
                    $validatedTags = $category->tags->where('validation', true);
                    $tagIdsInGroup = $validatedTags->pluck('id')->map(static fn ($id) => (int) $id)->all();
                    $selectedTagIdsInt = array_map(static fn ($id) => (int) $id, $selectedTagIds);
                    $selectedTagCategoryIdsInt = array_map(static fn ($id) => (int) $id, $selectedTagCategoryIds);
                    $openCategoryAccordion =
                        in_array((int) $category->id, $selectedTagCategoryIdsInt, true)
                        || count(array_intersect($selectedTagIdsInt, $tagIdsInGroup)) > 0;
                @endphp
                <div>
                    <button class="toggle-filter-options d-flex justify-content-between fw-bold py-1 @unless ($openCategoryAccordion) collapsed @endunless" type="button"
                        data-bs-toggle="collapse" data-bs-target="#toggle-tagcat-{{ $category->id }}-{{ $suffix }}"
                        aria-controls="toggle-tagcat-{{ $category->id }}-{{ $suffix }}" aria-expanded="{{ $openCategoryAccordion ? 'true' : 'false' }}"
                        aria-label="Toggle navigation">
                        {{ $category->name }}
                        <i class="bi bi-caret-down-fill me-2"></i>
                    </button>
                    <div class="collapse ms-3 category-filter @if ($openCategoryAccordion) show @endif" id="toggle-tagcat-{{ $category->id }}-{{ $suffix }}">
                        <input type="checkbox" class="form-check-input" id="category-{{ $category->id }}-{{ $suffix }}"
                            value="{{ $category->id }}" name="category[]"
                            {{ in_array($category->id, $selectedTagCategoryIds, false) ? 'checked' : '' }} />
                        <label class="custom-checkbox-label fw-bold" for="category-{{ $category->id }}-{{ $suffix }}">{{ __('view.catalog.items.filter.all') }}</label>
                        @foreach ($category->tags as $tag)
                            @if ($tag->validation)
                                <div>
                                    <input type="checkbox" class="form-check-input" id="tag-{{ $tag->id }}-{{ $suffix }}"
                                        value="{{ $tag->id }}" name="tag[]"
                                        {{ in_array($tag->id, request()->input('tag', [])) ? 'checked' : '' }} />
                                    <label class="custom-checkbox-label"
                                        for="tag-{{ $tag->id }}-{{ $suffix }}">{{ $tag->name }}
                                    </label>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="division-line m-1"></div>
            @endforeach
            <div>
                <div class="d-flex align-items-center">
                    <input class="form-check-input me-2" type="radio" id="order-1-{{ $suffix }}" name="order" value="1"
                        {{ request()->query('order') == 1 ? 'checked' : '' }}>
                    <label class="fw-bold radio-input" for="order-1-{{ $suffix }}">{{ __('view.catalog.items.filter.date_asc') }}<i
                            class="bi bi-sort-numeric-down h3"></i></label>
                </div>

                <div class="d-flex align-items-center">
                    <input class="form-check-input me-2" type="radio" id="order-2-{{ $suffix }}" name="order" value="2"
                        {{ request()->query('order') == 2 ? 'checked' : '' }}>
                    <label class="fw-bold radio-input" for="order-2-{{ $suffix }}">{{ __('view.catalog.items.filter.date_desc') }}<i
                            class="bi bi-sort-numeric-up h3"></i></label>
                </div>

                <div class="d-flex align-items-center">
                    <input class="form-check-input me-2" type="radio" id="order-3-{{ $suffix }}" name="order" value="3"
                        {{ request()->query('order') == 3 ? 'checked' : '' }}>
                    <label class="fw-bold radio-input" for="order-3-{{ $suffix }}">{{ __('view.catalog.items.filter.alpha_asc') }}<i
                            class="bi bi-sort-alpha-down h3"></i></label>
                </div>

                <div class="d-flex align-items-center">
                    <input class="form-check-input me-2" type="radio" id="order-4-{{ $suffix }}" name="order" value="4"
                        {{ request()->query('order') == 4 ? 'checked' : '' }}>
                    <label class="fw-bold radio-input" for="order-4-{{ $suffix }}">{{ __('view.catalog.items.filter.alpha_desc') }}<i
                            class="bi bi-sort-alpha-up h3"></i></label><br>
                </div>
            </div>
            <div class="col d-flex align-items-center justify-content-end flex-wrap gap-2">
                <x-ui.buttons.default
                    :href="$catalogFilterResetUrl"
                    variant="outline-secondary"
                    class="btn-sm"
                    icon="bi bi-x-lg"
                >
                    {{ __('view.shared.buttons.reset') }}
                </x-ui.buttons.default>
                <x-ui.buttons.submit variant="plain" class="button nav-link py-2 px-3 fw-bold">{{ __('view.catalog.items.filter.apply') }}</x-ui.buttons.submit>
            </div>
        </form>
    </div>
</div>
