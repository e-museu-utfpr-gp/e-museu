<x-layouts.app :title="__('view.about.title')">
    <div class="container main-container mb-auto">
        @include('pages.about._partials.intro')
        @include('pages.about._partials.tecnolixo')
        @include('pages.about._partials.elixo')
        @include('pages.about._partials.gallery')
    </div>

    <x-ui.image-modal />
</x-layouts.app>
