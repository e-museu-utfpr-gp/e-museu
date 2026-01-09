<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>E-museu: @yield('title')</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
</head>

<body class="bd-light d-flex flex-column min-vh-100">
    <nav
        class="navbar navbar-border navbar-expand-lg navbar-light bg-light d-flex justify-content-between px-md-5 py-0 sticky-top">
        <div class="container-fluid">
            <div class="navbar-left d-flex py-1">
                <div class="logo-div">
                    <a class="navbar-brand fw-bold" href="/"><img src="/img/tecnolixo-logo.png" alt=""
                            width="40" height="40"> E-MUSEU</a>
                </div>
            </div>
            <button class="button navbar-toggler p-1 nav-link" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-grow-0 mt-2" id="navbarSupportedContent">
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link p-3 fw-bold @if (Route::currentRouteName() == 'home') explore-button @endif"
                            href="{{ route('home') }}">PÁGINA INICIAL</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-3 fw-bold @if (Route::currentRouteName() == 'items.index' || Route::currentRouteName() == 'items.show') explore-button @endif"
                            href="{{ route('items.index') }}"><i class="bi bi-search h6 me-1"></i>EXPLORAR</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-3 fw-bold @if (Route::currentRouteName() == 'items.create') explore-button @endif"
                            href={{ route('items.create') }}>COLABORAÇÃO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-3 fw-bold @if (Route::currentRouteName() == 'about') explore-button @endif"
                            href={{ route('about') }}>SOBRE</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @if (request()->routeIs('items.index'))
        @include('items.explore-menu')
    @endif
    @include('assistent.assistent')
    @yield('content')
    <div>
        <footer class="d-md-flex custom-footer px-md-5 justify-content-between fixed align-items-center  py-5 mt-2">
            <p class="custom-nav mb-0 d-flex justify-content-center col-md-4"><strong class="me-1">Contato:
                </strong>emuseuvirtual@gmail.com</p>

            <a href="/"
                class="col-md-4 d-flex align-items-center justify-content-center my-3 me-md-auto link-dark text-decoration-none">
                <img class="e-lixo-footer-logo" src="/img/e-lixo-footer-logo.png" alt="">
                <h2 class="mx-3">-</h2>
                <img class="tecnolixo-footer-logo" src="/img/tecnolixo-footer-logo.png" alt="">
            </a>

            <p class="col-md-4 mb-0 d-flex justify-content-center">2024 - UTFPR - Unicentro</p>
        </footer>
    </div>
</body>

</html>
