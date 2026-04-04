@php
    // Limpar só filtros do painel (tags/categorias de etiqueta + ordem); mantém categoria do menu e a busca.
    $catalogFilterResetUrl = route('catalog.items.index', [], false) . '?' . http_build_query([
        'item_category' => (string) request()->query('item_category', ''),
        'search' => (string) request()->input('search', ''),
        'order' => '1',
    ]);
    $selectedTagCategoryIds = (array) request()->input('category', []);
    $selectedTagIds = (array) request()->input('tag', []);
    $hasTagOrCategoryFilters = count($selectedTagCategoryIds) > 0 || count($selectedTagIds) > 0;
    $orderIsNonDefault = (string) request()->query('order', '1') !== '1';
    $openMainFilterPanel = $hasTagOrCategoryFilters || $orderIsNonDefault;
@endphp
<div class="filter-menu">
    <div>
        <button class="toggle-filter-button d-flex justify-content-between fw-bold p-2 @unless ($openMainFilterPanel) collapsed @endunless" type="button"
            data-bs-toggle="collapse" data-bs-target="#toggleFilter" aria-controls="toggleFilter" aria-expanded="{{ $openMainFilterPanel ? 'true' : 'false' }}"
            aria-label="Toggle navigation">
            <div>
                <i class="bi bi-funnel-fill mx-1"></i> {{ __('view.catalog.items.filter.filter') }}
            </div>
            <i class="bi bi-caret-down-fill me-2"></i>
        </button>
    </div>
    <div class="collapse ms-3 @if ($openMainFilterPanel) show @endif" id="toggleFilter">
        <form action="{{ route('catalog.items.index') }}" method="GET">
            <input name="item_category" value="{{ request()->query('item_category') }}" hidden>
            <input name="search" value="{{ request()->query('search') }}" hidden>
            <input name="order" type="hidden" value="{{ request()->query('order', 1) }}">
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
                        data-bs-toggle="collapse" data-bs-target="#toggle{{ $category->name }}"
                        aria-controls="toggle{{ $category->name }}" aria-expanded="{{ $openCategoryAccordion ? 'true' : 'false' }}"
                        aria-label="Toggle navigation">
                        {{ $category->name }}
                        <i class="bi bi-caret-down-fill me-2"></i>
                    </button>
                    <div class="collapse ms-3 category-filter @if ($openCategoryAccordion) show @endif" id="toggle{{ $category->name }}">
                        <input type="checkbox" class="form-check-input" id="category-{{ $category->id }}"
                            value="{{ $category->id }}" name="category[]"
                            {{ in_array($category->id, $selectedTagCategoryIds, false) ? 'checked' : '' }} />
                        <label class="custom-checkbox-label fw-bold" for="category-{{ $category->id }}">{{ __('view.catalog.items.filter.all') }}</label>
                        @foreach ($category->tags as $tag)
                            @if ($tag->validation)
                                <div>
                                    <input type="checkbox" class="form-check-input" id="tag-{{ $tag->id }}"
                                        value="{{ $tag->id }}" name="tag[]"
                                        {{ in_array($tag->id, request()->input('tag', [])) ? 'checked' : '' }} />
                                    <label class="custom-checkbox-label"
                                        for="tag-{{ $tag->id }}">{{ $tag->name }}
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
                    <input class="form-check-input me-2" type="radio" id="order-1" name="order" value="1"
                        {{ request()->query('order') == 1 ? 'checked' : '' }}>
                    <label class="fw-bold radio-input" for="order-1">{{ __('view.catalog.items.filter.date_asc') }}<i
                            class="bi bi-sort-numeric-down h3"></i></label>
                </div>

                <div class="d-flex align-items-center">
                    <input class="form-check-input me-2" type="radio" id="order-2" name="order" value="2"
                        {{ request()->query('order') == 2 ? 'checked' : '' }}>
                    <label class="fw-bold radio-input" for="order-2">{{ __('view.catalog.items.filter.date_desc') }}<i
                            class="bi bi-sort-numeric-up h3"></i></label>
                </div>

                <div class="d-flex align-items-center">
                    <input class="form-check-input me-2" type="radio" id="order-3" name="order" value="3"
                        {{ request()->query('order') == 3 ? 'checked' : '' }}>
                    <label class="fw-bold radio-input" for="order-3">{{ __('view.catalog.items.filter.alpha_asc') }}<i
                            class="bi bi-sort-alpha-down h3"></i></label>
                </div>

                <div class="d-flex align-items-center">
                    <input class="form-check-input me-2" type="radio" id="order-4" name="order" value="4"
                        {{ request()->query('order') == 4 ? 'checked' : '' }}>
                    <label class="fw-bold radio-input" for="order-4">{{ __('view.catalog.items.filter.alpha_desc') }}<i
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
