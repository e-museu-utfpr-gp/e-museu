@extends('layouts.app')
@section('title', __('view.home.title'))

@section('content')
    <div class="container-fluid headline">
        <img class="img-headline" src="/img/banner.png" alt="">
        <div class="container headline-content">
            <div class="row">
                <div class="col-md-6">
                    <p class="h1 fw-bold text-shadow">{{ __('view.home.headline.welcome') }}</p>
                    <p class="h2 text-shadow">{{ __('view.home.headline.subtitle') }}</p>
                    <h6 class="text-shadow">
                        {{ __('view.home.headline.description') }}
                    </h6>
                </div>
            </div>
        </div>
    </div>
    <div class="container main-container mb-auto">
        <h1>{{ __('view.home.about.title') }}</h1>
        <div class="row">
            <div class="col-md-6">
                <p class="p-4">
                    {!! nl2br(__('view.home.about.paragraph1')) !!}
                </p>
            </div>
            <div class="col-md-6 ">
                <div id="carouselExampleControls" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @php
                            $imageActive = true;
                        @endphp
                        @foreach ($items as $item)
                            @if ($item->image_url)
                                <div class="carousel-item @if ($imageActive) active @endif">
                                    <img src="{{ $item->image_url }}" class="p-4 clickable-image"
                                        style="aspect-ratio: 3 / 2; width: 100%; max-height: 100%; object-fit: cover"
                                        alt="">
                                </div>
                                @php
                                    $imageActive = false;
                                @endphp
                            @endif
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">{{ __('view.home.carousel.prev') }}</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">{{ __('view.home.carousel.next') }}</span>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 ">
                    <img src="/img/unicentro-utfpr-logos.png" class="p-4 clickable-image"
                        style="width: 100%; max-height: 100%;" alt="">
                </div>
                <div class="col-md-6">
                    <p class="p-4">
                        {!! __('view.home.about.paragraph2') !!}
                        <br><br>
                        {{ __('view.home.about.paragraph2_cont') }}
                        <br><br>
                        <strong>{{ __('view.home.about.assistant_note') }}</strong>
                        <br><br>
                        {{ __('view.home.about.cta') }}
                    </p>
                </div>
            </div>
            <h3>{{ __('view.home.exploration_title') }}</h3>
            <div class="row p-4">
                <div class="col-md-4 ">
                    <div class="col-md pb-1 d-flex justify-content-center">
                        <a href="{{ route('items.index') }}" class="card d-flex card-anim">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <img class="p-2" src="/img/compass.png" style="width: 7rem;" alt="...">
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="card-title fw-bold">{{ __('view.home.cards.explore.title') }}</h6>
                                        <p>{{ __('view.home.cards.explore.description') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 ">
                    <div class="col-md pb-1 d-flex justify-content-center">
                        <a href="{{ route('items.create') }}" class="card d-flex card-anim">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-4">
                                        <img class="p-2" src="/img/form.png" style="width: 7rem;" alt="...">
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="card-title fw-bold">{{ __('view.home.cards.contribute.title') }}</h6>
                                        <p>{{ __('view.home.cards.contribute.description') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 ">
                    <div class="col-md pb-1 d-flex justify-content-center">
                        <a href="{{ route('about') }}" class="card d-flex card-anim">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-4">
                                        <img class="p-2" src="/img/info.png" style="width: 7rem;" alt="...">
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="card-title fw-bold">{{ __('view.home.cards.about.title') }}</h6>
                                        <p>{{ __('view.home.cards.about.description') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('image-modal.img-modal')
@endsection
