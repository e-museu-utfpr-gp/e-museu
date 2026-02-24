@extends('layouts.app')
@section('title', __('view.about.title'))

@section('content')
    <div class="container main-container mb-auto">
        <h1>{{ __('view.about.heading') }}</h1>
        <div class="row">
            <div class="col-md-6">
                <p class="p-4">
                    {{ __('view.about.intro') }}
                </p>
                <p class="ms-4 fw-bold">{{ __('view.about.contact_line') }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h3>{{ __('view.about.tecnolixo.heading') }}</h3>
                <p class="p-4">
                    {!! __('view.about.tecnolixo.body') !!}
                    <a target="_blank" href="https://www.utfpr.edu.br"><button class="nav-link p-3 fw-bold explore-button mt-3">{{ __('view.about.tecnolixo.button') }}</button></a>
                </p>
            </div>
            <div class="col-md-6 ">
                <img src="/img/tecno-lixo-3.jpg" class="p-4 clickable-image"
                    style="aspect-ratio: 3 / 2; width: 100%; max-height: 100%; object-fit: cover" alt="">
            </div>
        </div>
        <h3>{{ __('view.about.elixo.heading') }}</h3>
        <div class="row">
            <div class="col-md-6 ">
                <img src="/img/e-lixo-1.jpg" class="p-4 clickable-image"
                    style="aspect-ratio: 3 / 2; width: 100%; max-height: 100%; object-fit: cover" alt="">
            </div>
            <div class="col-md-6">
                <p class="p-4">
                    {!! __('view.about.elixo.body') !!}
                    <a target="_blank" href="https://www3.unicentro.br"><button class="nav-link p-3 fw-bold explore-button mt-3">{{ __('view.about.elixo.button') }}</button></a>
                </p>
            </div>
        </div>
        <h3>{{ __('view.about.gallery_heading') }}</h3>
        <div class="row">
            <div class="col-md-4">
                <img class="p-4 clickable-image" src="/img/e-lixo-1.jpg"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover" alt="{{ __('view.about.gallery_image_alt') }}">
            </div>
            <div class="col-md-4">
                <img class="p-4 clickable-image" src="/img/e-lixo-2.jpg"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover" alt="{{ __('view.about.gallery_image_alt') }}">
            </div>
            <div class="col-md-4">
                <img class="p-4 clickable-image" src="/img/tecno-lixo-2.jpg"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover" alt="{{ __('view.about.gallery_image_alt') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <img class="p-4 clickable-image" src="/img/tecno-lixo-1.jpg"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover" alt="{{ __('view.about.gallery_image_alt') }}">
            </div>
            <div class="col-md-4">
                <img class="p-4 clickable-image" src="/img/tecno-lixo-4.jpg"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover" alt="{{ __('view.about.gallery_image_alt') }}">
            </div>
            <div class="col-md-4">
                <img class="p-4 clickable-image" src="/img/tecno-lixo-5.jpg"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover" alt="{{ __('view.about.gallery_image_alt') }}">
            </div>
        </div>
    </div>

    @include('image-modal.img-modal')
@endsection
