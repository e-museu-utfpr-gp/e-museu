@php
    $aboutGalleryRows = [
        [
            ['src' => '/img/e-lixo-1.jpg', 'class' => '', 'altKey' => 'view.about.gallery_elixo_1_alt'],
            ['src' => '/img/e-lixo-2.jpg', 'class' => '', 'altKey' => 'view.about.gallery_elixo_2_alt'],
            ['src' => '/img/tecno-lixo-2.jpg', 'class' => '', 'altKey' => 'view.about.gallery_tecno_2_alt'],
        ],
        [
            ['src' => '/img/tecno-lixo-1.jpg', 'class' => 'about-gallery__img--flip-x', 'altKey' => 'view.about.gallery_tecno_1_alt'],
            ['src' => '/img/tecno-lixo-4.jpg', 'class' => '', 'altKey' => 'view.about.gallery_tecno_4_alt'],
            ['src' => '/img/tecno-lixo-5.jpg', 'class' => '', 'altKey' => 'view.about.gallery_tecno_5_alt'],
        ],
    ];
@endphp
<h3>{{ __('view.about.gallery_heading') }}</h3>
@foreach ($aboutGalleryRows as $row)
    <div class="row">
        @foreach ($row as $item)
            <div class="col-md-4">
                <img class="p-4 clickable-image {{ $item['class'] }}" src="{{ $item['src'] }}"
                    style="aspect-ratio: 1/1; width: 100%; max-height: 100%; object-fit: cover"
                    alt="{{ __($item['altKey']) }}" loading="lazy">
            </div>
        @endforeach
    </div>
@endforeach
