@extends('layouts.app')
@section('title', __('view.catalog.items.index.title'))

@section('content')
    <div class="container main-container mb-auto">
        @if (request()->query('section') == '')
            <h1>{{ __('view.catalog.items.index.heading_all') }}</h1>
        @else
            <h1>{{ __('view.catalog.items.index.heading_in_section', ['section' => $sectionName]) }}</h1>
        @endif
        <div class="row">
            <div class="col-md-2 d-none d-md-block">
                @include('catalog.items.filter-menu')
            </div>
            <div class="col-2 d-block d-md-none">
                <button class="btn btn-primary d-md-none toggle-filter-button-mobile py-2" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                    <i class="bi bi-funnel-fill h3"></i>
                </button>

                <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel"
                    style="overflow-y: scroll;">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="sidebarLabel">
                            {{ __('view.catalog.items.index.filters_title') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        @include('catalog.items.filter-menu')
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="content row">
                    @foreach ($items as $item)
                        <div class="col pb-5 d-flex justify-content-center">
                            <a href={{ route('items.show', $item->id) }} class="card d-flex card-anim"
                                style="width: 18rem; height: 28rem;">
                                @if ($item->image_url)
                                    <img src="{{ $item->image_url }}" class="card-img-top p-1"
                                        style="height: 12rem; object-fit: cover;"
                                        alt="{{ __('view.catalog.items.index.image_alt') }}">
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title fw-bold border-dark">{{ Str::limit($item->name, 40) }}</h6>
                                    <p class="border-dark">{{ $item->identification_code }}</p>
                                    <div class="division-line my-1"></div>
                                    <div class="d-flex justify-content-between pt-1">
                                        <p class="card-subtitle border- fw-bold">{{ $item->category?->name }}</p>
                                        @if (\Carbon\Carbon::parse($item->date)->format('Y') != '0001')
                                            <p class="card-subtitle">{{ date('d/m/Y', strtotime($item->date)) }}</p>
                                        @else
                                            <p class="card-subtitle">
                                                {{ __('view.catalog.items.index.date_unknown') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="division-line my-1"></div>
                                    <p class="card-text">{{ Str::limit($item->description, 100) }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                @if (!$items->first())
                    <h4 class="fw-bold">
                        {{ __('view.catalog.items.index.none_found') }}
                    </h4>
                @endif
            </div>
        </div>
        <div class="mx-5">
            {{ $items->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
