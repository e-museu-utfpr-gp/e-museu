@props(['title' => ''])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>E-museu: {{ $title }}</title>

    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
</head>

<body class="bd-light d-flex flex-column min-vh-100 app-public-layout">
    <nav
        class="navbar navbar-border navbar-expand-lg navbar-light bg-light d-flex justify-content-between px-md-5 py-0 sticky-top">
        <div class="container-fluid">
            <div class="navbar-left d-flex py-1">
                <div class="logo-div">
                    <a class="navbar-brand fw-bold" href="{{ route('home') }}"><img src="/img/tecnolixo-logo.png"
                            alt="{{ __('view.layout.logo_nav_alt') }}" width="40" height="40"> E-MUSEU</a>
                </div>
            </div>
            <button class="button navbar-toggler p-1 nav-link" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="{{ __('view.layout.toggle_navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-grow-0 mt-2" id="navbarSupportedContent">
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link p-3 fw-bold @if (Route::currentRouteName() == 'home') explore-button @endif"
                            href="{{ route('home') }}">{{ __('view.layout.nav.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-3 fw-bold @if (Route::currentRouteName() == 'catalog.items.index' || Route::currentRouteName() == 'catalog.items.show') explore-button @endif"
                            href="{{ route('catalog.items.index') }}"><i class="bi bi-search h6 me-1"></i>{{ __('view.layout.nav.explore') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-3 fw-bold @if (Route::currentRouteName() == 'catalog.items.create') explore-button @endif"
                            href="{{ route('catalog.items.create') }}">{{ __('view.layout.nav.contribute') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-3 fw-bold @if (Route::currentRouteName() == 'about') explore-button @endif"
                            href="{{ route('about') }}">{{ __('view.layout.nav.about') }}</a>
                    </li>
                    @if ($localeSwitcherLanguages->isNotEmpty())
                        <li class="nav-item d-flex align-items-center">
                            <form action="{{ route('locale.update') }}" method="post"
                                class="d-flex align-items-center mb-0 px-2 px-lg-0">
                                @csrf
                                <div class="locale-nav-select">
                                    <label class="visually-hidden" for="publicLocale">
                                        {{ __('view.layout.locale_label') }}
                                    </label>
                                    <select class="form-select locale-nav-select-input fw-bold" id="publicLocale"
                                        name="locale" aria-label="{{ __('view.layout.locale_label') }}"
                                        onchange="this.form.requestSubmit()">
                                        @foreach ($localeSwitcherLanguages as $lang)
                                            @php
                                                $hasUi = $lang->hasUiTranslationPack();
                                            @endphp
                                            <option value="{{ $lang->code }}"
                                                @selected(app()->getLocale() === $lang->code)
                                                @disabled(! $hasUi)>
                                                {{ $lang->name }}@if (! $hasUi)
                                                    ({{ __('view.layout.locale_no_ui') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    {{ $slot }}
    <div>
        <footer class="d-md-flex custom-footer px-md-5 justify-content-between fixed align-items-center  py-5 mt-2">
            <p class="custom-nav mb-0 d-flex justify-content-center col-md-4"><strong class="me-1">{{ __('view.layout.footer.contact') }}
                </strong>emuseuvirtual@gmail.com</p>

            <a href="{{ route('home') }}"
                class="col-md-4 d-flex align-items-center justify-content-center my-3 me-md-auto link-dark text-decoration-none">
                <img class="e-lixo-footer-logo" src="/img/e-lixo-footer-logo.png"
                    alt="{{ __('view.layout.footer_partner_e_lixo_alt') }}">
                <h2 class="mx-3">-</h2>
                <img class="tecnolixo-footer-logo" src="/img/tecnolixo-footer-logo.png"
                    alt="{{ __('view.layout.footer_partner_tecnolixo_alt') }}">
            </a>

            <p class="col-md-4 mb-0 d-flex justify-content-center">{{ __('view.layout.footer.copyright') }}</p>
        </footer>
    </div>
    @stack('scripts')
</body>

</html>
