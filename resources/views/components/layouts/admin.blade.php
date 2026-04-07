@props([
    'title' => '',
    'heading' => null,
])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-admin-dependent-select-error="{{ __('view.admin.layout.dependent_select_load_failed') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>E-museu: {{ $title }}</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    @vite(['resources/sass/app.scss', 'resources/js/admin.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">

</head>

<body class="bd-light d-flex flex-column min-vh-100 admin-layout">
    <div class="row g-0">
        <div class="col-md-2 flex-column flex-shrink-0 p-3">
            <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none"
                href="{{ route('home') }}">
                <span class="fs-4 ms-2">E-Museu</span>
            </a>
            <hr>
            <x-ui.buttons.default href="#" variant="secondary" class="d-block d-md-none mb-3" role="button"
                data-bs-toggle="collapse" data-bs-target="#sidebarCollapse" aria-expanded="false"
                aria-controls="sidebarCollapse">
                {{ __('view.admin.layout.menu') }}
            </x-ui.buttons.default>
            <div class="collapse d-md-block" id="sidebarCollapse">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li>
                        <a href="{{ route('admin.catalog.item-categories.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.catalog.item-categories')) active @endif" aria-current="page">
                            {{ __('view.admin.layout.nav.item_categories') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.catalog.items.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.catalog.items')) active @endif" aria-current="page">
                            {{ __('view.admin.layout.nav.items') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.catalog.item-components.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.catalog.item-components')) active @endif" aria-current="page">
                            {{ __('view.admin.layout.nav.item_components') }}
                        </a>
                    </li>
                    <hr/>
                    <li>
                        <a href="{{ route('admin.taxonomy.tag-categories.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.taxonomy.tag-categories')) active @endif" aria-current="page">
                            {{ __('view.admin.layout.nav.tag_categories') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.taxonomy.tags.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.taxonomy.tags')) active @endif" aria-current="page">
                            {{ __('view.admin.layout.nav.tags') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.catalog.item-tags.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.catalog.item-tags')) active @endif" aria-current="page">
                            {{ __('view.admin.layout.nav.item_tags') }}
                        </a>
                    </li>
                    <hr/>
                    <li>
                        <a href="{{ route('admin.catalog.extras.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.catalog.extras')) active @endif" aria-current="page">
                            {{ __('view.admin.layout.nav.extras') }}
                        </a>
                    </li>
                    <hr/>
                    <li>
                        <a href="{{ route('admin.collaborators.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.collaborators')) active @endif" aria-current="page">
                            {{ __('view.admin.layout.nav.collaborators') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.identity.admins.index') }}"
                            class="nav-link @if (Str::startsWith(Route::currentRouteName(), 'admin.identity.admins')) active @endif" aria-current="page">
                            {{ __('view.admin.layout.nav.administrators') }}
                        </a>
                    </li>
                </ul>
                <hr/>
                @if ($localeSwitcherLanguages->isNotEmpty())
                    <div class="px-3 mb-3">
                        <label class="form-label small text-muted mb-1" for="adminLocale">
                            {{ __('view.admin.layout.locale_label') }}
                        </label>
                        <form action="{{ route('locale.update') }}" method="post">
                            @csrf
                            <select class="form-select form-select-sm" id="adminLocale" name="locale"
                                aria-label="{{ __('view.admin.layout.locale_label') }}"
                                onchange="this.form.requestSubmit()">
                                @foreach ($localeSwitcherLanguages as $lang)
                                    @php
                                        $hasUi = $lang->hasUiTranslationPack();
                                    @endphp
                                    <option value="{{ $lang->code }}"
                                        @selected(app()->getLocale() === $lang->code)
                                        @disabled(! $hasUi)>
                                        {{ $lang->name }}@if (! $hasUi)
                                            ({{ __('view.admin.layout.locale_no_ui') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
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
                                    class="btn border-0 bg-transparent px-0 py-0 dropdown-item ms-2" type="submit">{{ __('view.admin.layout.logout') }}</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <hr>
        </div>
        <div class="col-md-10 p-4 admin-main">
            <div class="mb-auto container-fluid min-w-0">
                <x-ui.flash-messages />
                @if (filled($heading))
                    <x-admin.page-header :text="$heading">
                        @isset($pageHeaderActions)
                            {{ $pageHeaderActions }}
                        @endisset
                    </x-admin.page-header>
                @endif
                {{ $slot }}
            </div>
        </div>
    </div>
    @stack('scripts')
</body>

</html>
