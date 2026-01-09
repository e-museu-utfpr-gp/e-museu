<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>E-museu: @yield('title')</title>

    @vite(['resources/sass/app.scss', 'resources/js/bootstrap.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">

</head>

<body class="bd-light d-flex flex-column min-vh-100">
    <div class="row">
        <div class="col-md-2 flex-column flex-shrink-0 p-3">
            <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
                <a class="text-decoration-none" href="{{ route('home') }}"><span class="fs-4 ms-2">E-Museu</span></a>
            </a>
            <hr>
            <a role="button" class="btn btn-secondary d-block d-md-none mb-3" type="button" data-bs-toggle="collapse"
                data-bs-target="#sidebarCollapse" aria-expanded="false" aria-controls="sidebarCollapse">
                Menu
            </a>
            <div class="collapse d-md-block" id="sidebarCollapse">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li>
                        <a href="{{ route('admin.sections.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.sections')) active @endif" aria-current="page">
                            Categorias de Item
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.items.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.items')) active @endif" aria-current="page">
                            Itens do Museu
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.components.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.components')) active @endif" aria-current="page">
                            Associações de Item a Componente
                        </a>
                    </li>
                    <hr/>
                    <li>
                        <a href="{{ route('admin.categories.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.categories')) active @endif" aria-current="page">
                            Categorias de Etiqueta
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tags.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.tags')) active @endif" aria-current="page">
                            Etiquetas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.item-tags.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.item-tags')) active @endif" aria-current="page">
                            Associações de Item a Etiqueta
                        </a>
                    </li>
                    <hr/>
                    <li>
                        <a href="{{ route('admin.extras.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.extras')) active @endif" aria-current="page">
                            Informações Extra
                        </a>
                    </li>
                    <hr/>
                    <li>
                        <a href="{{ route('admin.proprietaries.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.proprietaries')) active @endif" aria-current="page">
                            Colaboradores
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.users')) active @endif" aria-current="page">
                            Administradores
                        </a>
                    </li>
                </ul>
                <hr/>
                <div class="dropdown mt-2">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle ms-3"
                        id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <strong>
                            {{ auth()->user()->username }}
                        </strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu text-small shadow" aria-labelledby="dropdownUser1">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button
                                    class="btn border-0 bg-transparent px-0 py-0 dropdown-item ms-2"type="submit">Sair</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <hr>
        </div>
        <div class="col-md-10 p-4">
            @yield('content')
        </div>
    </div>
</body>

</html>
