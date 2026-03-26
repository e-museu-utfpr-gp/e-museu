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
                {{ __('view.home.about.cta') }}
            </p>
        </div>
    </div>
</div>
