<x-layouts.app :title="$item->name">

@php
    $itemTranslationResolved = $item->resolveTranslation();

    $hasNoSeries = $item->itemTags
        ->filter(function ($tagItem) use ($seriesCategoryId) {
            return $tagItem->tag->tagCategory?->id == $seriesCategoryId && $tagItem->validation == true;
        })
        ->isEmpty();

    $hasNoComponents = $item->itemComponents
        ->filter(function ($itemComponent) {
            return $itemComponent->validation == true && $itemComponent->component->validation == true;
        })
        ->isEmpty();

    $hasValidatedExtras = $item->extras
        ->filter(function ($extra) {
            return $extra->validation == true;
        })
        ->isNotEmpty();
@endphp

    <div class="container main-container mb-auto">
        <x-ui.flash-messages variant="app" />
        <div class="row">
            @include('pages.catalog.items._partials.show.item-details-sidebar')

            <div class="col-md-8 order-md-1">
                <h1>{{ $item->name }}</h1>
                @include('pages.catalog.items._partials.show.translation-fallback-notice', ['resolved' => $itemTranslationResolved])
                <div class="m-4">
                    <p class="fw-bold">{{ __('view.catalog.items.show.identification_code') }}: {{ $item->identification_code }}</p>
                    <p>{{ $item->description }}</p>
                </div>
                <h3>{{ __('view.catalog.items.show.history') }}</h3>
                <div class="m-4">
                    @if ($item->history == null)
                        <div>
                            <strong>{{ __('view.catalog.items.show.no_history') }}</strong>
                        </div>
                    @else
                        @php echo nl2br(e($item->history)); @endphp
                    @endif
                </div>
                @include('pages.catalog.items._partials.show.timelines-section')
                <h3>{{ __('view.catalog.items.show.extra_info') }}</h3>
                @if ($item->extras->isNotEmpty() && $item->extras->contains('validation', '1'))
                    @foreach ($item->extras as $extra)
                        @if ($extra->validation == '1')
                            <div class="m-4">
                                @include('pages.catalog.items._partials.show.translation-fallback-notice', [
                                    'resolved' => $extra->resolveTranslation(),
                                    'messageKey' => 'view.catalog.translation_fallback_notice_extra',
                                ])
                                <p>{{ $extra->info }}</p>
                                <div class="row">
                                    <p class="fw-bold col-2">{{ __('view.catalog.items.show.added_by') }} </p>
                                    <p class="col-10">{{ $extra->collaborator->full_name }}</p>
                                </div>
                                <div class="division-line my-1"></div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="m-4">
                        <strong>{{ __('view.catalog.items.show.no_extras') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-ui.image-modal />
    @include('pages.catalog.items._partials.show.extra-modal')

</x-layouts.app>
