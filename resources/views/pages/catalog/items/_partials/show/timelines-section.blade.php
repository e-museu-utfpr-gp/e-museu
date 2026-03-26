<h3>{{ __('view.catalog.items.show.timelines') }}</h3>
@foreach ($item->itemTags as $tagItem)
    @if ($tagItem->validation == true && $tagItem->tag->validation == true)
        @if ($tagItem->tag->category?->id == $seriesCategoryId)
            <div class="mx-4 my-5">
                <h4 class="mb-4 fw-bold">{{ $tagItem->tag->name }}</h4>
                <div class="timeline m-2 px-2">
                    @foreach ($tagItem->tag->items->sortBy('date') as $timelineItem)
                        @if ($timelineItem->validation == 1)
                            <div class="my-4">
                                <div class="d-flex align-items-start">
                                    <div class="timeline-circle me-2"></div>
                                    @if ($timelineItem->date)
                                        <h6 class="fw-bold timeline-item-date">
                                            {{ date('d/m/Y', strtotime($timelineItem->date)) }}</h6>
                                    @else
                                        <h6 class="fw-bold timeline-item-date">{{ __('view.catalog.items.show.date_unknown_short') }}</h6>
                                    @endif
                                </div>
                                <div class="d-md-flex">
                                    @if ($timelineItem->image_url)
                                        <div>
                                            <img src="{{ $timelineItem->image_url }}"
                                                class="card-img-top p-1 clickable-image"
                                                style="width: 12rem; height: 12rem; object-fit: cover"
                                                alt="{{ __('view.catalog.items.show.timeline_image_alt') }}">
                                        </div>
                                    @endif
                                    <div class="ms-2">
                                        <p class="fw-bold">{{ $timelineItem->name }}</p>
                                        <p>{{ $timelineItem->description }}</p>
                                        <a href="{{ route('catalog.items.show', $timelineItem->id) }}">
                                            <h6 class="me-4 d-flex justify-content-end card-more-details">
                                                {{ __('view.catalog.items.show.more_details') }}</h6>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @endif
@endforeach

@if ($hasNoSeries)
    <div class="m-4">
        <strong>{{ __('view.catalog.items.show.no_series') }}</strong>
    </div>
@endif

