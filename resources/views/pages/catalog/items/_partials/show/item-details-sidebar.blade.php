<div class="col-md-4 order-md-2">
    <div>
        <a class="nav-link py-3 fw-bold explore-button px-2" href="" data-bs-toggle="modal"
            data-bs-target="#addExtraModal">
            <i class="bi bi-patch-plus-fill h4 me-2"></i>{{ __('view.catalog.items.show.send_extra') }}
        </a>
    </div>
    <div class="card my-2">
        <div>
            <h5>{{ $item->name }}</h5>
        </div>

        @if ($item->image_url)
            <div style="overflow:hidden;">
                <img src="{{ $item->image_url }}" class="card-img-top p-1 clickable-image myImg"
                    style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover"
                    alt="{{ __('view.catalog.items.show.image_alt') }}">
            </div>
            @if ($item->images->count() > 1)
                <div class="d-flex flex-wrap gap-1 p-1">
                    @foreach ($item->images as $img)
                        <img src="{{ $img->image_url }}" class="clickable-image myImg rounded" alt=""
                            style="width: 48px; height: 48px; object-fit: cover;">
                    @endforeach
                </div>
            @endif
        @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <p class="fw-bold">{{ __('view.catalog.items.show.identification_code') }}</p>
                </div>
                <div class="col-md-7">
                    <p>{{ $item->identification_code }}</p>
                </div>
                <div class="col-md-5">
                    <p class="fw-bold">{{ __('view.catalog.items.show.category') }}</p>
                </div>
                <div class="col-md-7">
                    <a href="{{ route('catalog.items.index', ['item_category' => $item->itemCategory?->id]) }}">
                        <p class="show-item-link">{{ $item->itemCategory?->name }}</p>
                    </a>
                    @if ($item->itemCategory)
                        @include('pages.catalog.items._partials.show.translation-fallback-notice', [
                            'resolved' => $item->itemCategory->resolveTranslation(),
                        ])
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <p class="fw-bold">{{ __('view.catalog.items.show.date') }}</p>
                </div>
                <div class="col-md-7">
                    @if ($item->date)
                        <p>{{ date('d/m/Y', strtotime($item->date)) }}</p>
                    @else
                        <p>{{ __('view.catalog.items.show.date_unknown') }}</p>
                    @endif
                </div>
            </div>

            @foreach ($item->itemTags as $tagItem)
                @if ($tagItem->validation == true && $tagItem->tag->validation == true)
                    <div class="row">
                        <div class="col-md-5">
                            <p class="fw-bold">{{ $tagItem->tag->tagCategory?->name }}</p>
                            @if ($tagItem->tag->tagCategory)
                                @include('pages.catalog.items._partials.show.translation-fallback-notice', [
                                    'resolved' => $tagItem->tag->tagCategory->resolveTranslation(),
                                ])
                            @endif
                        </div>
                        <div class="col-md-7">
                            <a href="{{ route('catalog.items.index', ['tag[]' => $tagItem->tag->id]) }}">
                                <p class="show-item-link">{{ $tagItem->tag->name }}</p>
                            </a>
                            @include('pages.catalog.items._partials.show.translation-fallback-notice', [
                                'resolved' => $tagItem->tag->resolveTranslation(),
                            ])
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div>
            <h5>{{ __('view.catalog.items.show.technical_details') }}</h5>
        </div>
        <div class="card-body">
            @php echo nl2br(e($item->detail)); @endphp
        </div>

        <div>
            <h5>{{ __('view.catalog.items.show.components') }}</h5>
        </div>
        <div class="card-body row m-1">
            @foreach ($item->itemComponents as $itemComponent)
                @if ($itemComponent->validation == true && $itemComponent->component->validation == true)
                    <div class="col-6 p-2">
                        <a href="{{ route('catalog.items.show', $itemComponent->component->id) }}">
                            <div class="card-anim component-button p-1">
                                @if ($itemComponent->component->image_url)
                                    <div class="">
                                        <img src="{{ $itemComponent->component->image_url }}"
                                            class="component-img p-1" alt="{{ __('view.catalog.items.show.component_image_alt') }}">
                                    </div>
                                @endif
                                <div class="p-1">
                                    @include('pages.catalog.items._partials.show.translation-fallback-notice', [
                                        'resolved' => $itemComponent->component->resolveTranslation(),
                                    ])
                                    <p class="mb-1 fw-bold">
                                        {{ Str::limit($itemComponent->component->name, 30) }}</p>
                                    @if ($itemComponent->component->itemCategory)
                                        @include('pages.catalog.items._partials.show.translation-fallback-notice', [
                                            'resolved' => $itemComponent->component->itemCategory->resolveTranslation(),
                                        ])
                                    @endif
                                    <p class="mb-0">
                                        {{ Str::limit($itemComponent->component->itemCategory?->name) }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
            @if ($hasNoComponents)
                <div class="m-4">
                    <strong>{{ __('view.catalog.items.show.no_components') }}</strong>
                </div>
            @endif
        </div>

        <div>
            <h5>{{ __('view.catalog.items.show.credits') }}</h5>
        </div>
        <div class="row m-1">
            <div class="col-md-5">
                <p class="fw-bold">{{ __('view.catalog.items.show.added_by') }}</p>
            </div>
            <div class="col-md-7">
                <p>{{ $item->collaborator->full_name }}</p>
            </div>
        </div>

        @if ($hasValidatedExtras)
            <div class="dropdown">
                <a type="button" class="nav-link py-3 fw-bold dropdown-toggle" data-bs-toggle="collapse"
                    data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    <h5>{{ __('view.catalog.items.show.collaborators') }} <i class="bi bi-caret-down-fill"></i></h5>
                </a>
                <div class="collapse" id="collapseExample">
                    <ul class="list-group p-2">
                        <div class="card-body">
                            @foreach ($item->extras as $extra)
                                @if ($extra->validation == true)
                                    <div class="row">
                                        <div class="col-md-5">
                                            <p class="fw-bold">{{ __('view.catalog.items.show.collaborator') }}</p>
                                        </div>
                                        <div class="col-md-7">
                                            <p>{{ $extra->collaborator->full_name }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>

