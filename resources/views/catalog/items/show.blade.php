@extends('layouts.app')
@section('title', $item->name)

@php
    $hasSeries = $item->itemTags
        ->filter(function ($tagItem) {
            return $tagItem->tag->category->name == 'Série' && $tagItem->validation == true;
        })
        ->isEmpty();

    $hasComponents = $item->itemComponents
        ->filter(function ($itemComponent) {
            return $itemComponent->validation == true && $itemComponent->component->validation == true;
        })
        ->isEmpty();

    $hasExtras = $item->extras
        ->filter(function ($extra) {
            return $extra->validation == true;
        })
        ->isEmpty();
@endphp

@section('content')
    <div class="container main-container mb-auto">
        @if (session('success'))
            <div class="success-div text-wrap fw-bold m-1 p-1">
                {{ session('success') }}
            </div>
        @endif
        @foreach ($errors->all() as $error)
            <p class="error-div text-wrap fw-bold m-1 mb-4 p-1"><i class="bi bi-exclamation-circle-fill mx-1 h5"></i>
                {{ $error }}</p>
        @endforeach
        <div class="row">
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
                            <img src="{{ $item->image_url }}" class="card-img-top p-1 clickable-image"
                                style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover"
                                alt="{{ __('view.catalog.items.show.image_alt') }}">
                        </div>
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
                                <a href={{ route('items.index', ['section' => $item->category?->id]) }}>
                                    <p class="show-item-link">{{ $item->category?->name }}</p>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <p class="fw-bold">{{ __('view.catalog.items.show.date') }}</p>
                            </div>
                            <div class="col-md-7">
                                @if (\Carbon\Carbon::parse($item->date)->format('Y') != '0001')
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
                                        <p class="fw-bold">{{ $tagItem->tag->category->name }}</p>
                                    </div>
                                    <div class="col-md-7">
                                        <a href={{ route('items.index', ['tag[]' => $tagItem->tag->id]) }}>
                                            <p class="show-item-link">{{ $tagItem->tag->name }}</p>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div>
                        <h5>{{ __('view.catalog.items.show.technical_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <p>{!! nl2br($item->detail) !!}</p>
                    </div>
                    <div>
                        <h5>{{ __('view.catalog.items.show.components') }}</h5>
                    </div>
                    <div class="card-body row m-1">
                        @foreach ($item->itemComponents as $ItemComponent)
                            @if ($ItemComponent->validation == true && $ItemComponent->component->validation == true)
                                <div class="col-6 p-2">
                                    <a href={{ route('items.show', $ItemComponent->component->id) }}>
                                        <div class="card-anim component-button p-1">
                                            @if ($ItemComponent->component->image_url)
                                                <div class="">
                                                    <img src="{{ $ItemComponent->component->image_url }}"
                                                        class="component-img p-1" alt="{{ __('view.catalog.items.show.component_image_alt') }}">
                                                </div>
                                            @endif
                                            <div class="p-1">
                                                <p class="mb-1 fw-bold">
                                                    {{ Str::limit($ItemComponent->component->name, 30) }}</p>
                                                <p class="mb-0">
                                                    {{ Str::limit($ItemComponent->component->category?->name) }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                        @if ($hasComponents)
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
                    @if (!$hasExtras)
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

            <div class="col-md-8 order-md-1">
                <h1>{{ $item->name }}</h1>
                <div class="m-4">
                    <p class="fw-bold">{{ __('view.catalog.items.show.identification_code') }}: {{ $item->identification_code }}<p>
                    <p>{{ $item->description }}</p>
                </div>
                <h3>{{ __('view.catalog.items.show.history') }}</h3>
                <div class="m-4">
                    @if ($item->history == null)
                        <div>
                            <strong>{{ __('view.catalog.items.show.no_history') }}</strong>
                        </div>
                    @else
                        {!! nl2br($item->history) !!}
                    @endif
                </div>
                <h3>{{ __('view.catalog.items.show.timelines') }}</h3>
                @foreach ($item->itemTags as $tagItem)
                    @if ($tagItem->validation == true && $tagItem->tag->validation == true)
                        @if ($tagItem->tag->category->name == 'Série')
                            <div class="mx-4 my-5">
                                <h4 class="mb-4 fw-bold">{{ $tagItem->tag->name }}</h4>
                                <div class="timeline m-2 px-2">
                                    @foreach ($tagItem->tag->items->sortBy('date') as $timelineItem)
                                        @if ($timelineItem->validation == 1)
                                            <div class="my-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="timeline-circle me-2"></div>
                                                    @if (\Carbon\Carbon::parse($timelineItem->date)->format('Y') != '0001')
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
                                                        <a href={{ route('items.show', $timelineItem->id) }}>
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
                @if ($hasSeries)
                    <div class="m-4">
                        <strong>{{ __('view.catalog.items.show.no_series') }}</strong>
                    </div>
                @endif
                <h3>{{ __('view.catalog.items.show.extra_info') }}</h3>
                @if ($item->extras->isNotEmpty() && $item->extras->contains('validation', '1'))
                    @foreach ($item->extras as $extra)
                        @if ($extra->validation == '1')
                            <div class="m-4">
                                <p>{{ $extra->info }}</p>
                                <div class="row">
                                    <p class="fw-bold col-2">{{ __('view.catalog.items.show.added_by') }} </p>
                                    <p class="col-10">{{ $extra->collaborator->full_name }}</p>
                                </div>
                                <div class="division-line my-1"></div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="m-4">
                        <strong>{{ __('view.catalog.items.show.no_extras') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('image-modal.img-modal')
    @include('catalog.items.show-modals.extra-modal')

    <script>
        let checkContactRoute = "{{ route('check-contact') }}";
    </script>

@endsection
