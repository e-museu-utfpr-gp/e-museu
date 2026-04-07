<x-layouts.app :title="__('view.home.title')">
    @include('pages.home._partials.headline')

    <div class="container main-container mb-auto">
        @include('pages.home._partials.about', ['items' => $items])
        @include('pages.home._partials.exploration-cards')
    </div>

    <x-ui.image-modal />
</x-layouts.app>
