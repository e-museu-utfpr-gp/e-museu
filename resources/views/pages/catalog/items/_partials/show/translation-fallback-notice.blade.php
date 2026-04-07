@props([
    'resolved' => null,
    'messageKey' => 'view.catalog.translation_fallback_notice',
])
@if ($resolved instanceof \App\Support\Content\ResolvedTranslation && $resolved->usedFallback())
    @php($label = $resolved->sourceLanguageLabel())
    <p class="small text-muted mb-2" role="status">
        {{ __($messageKey, ['language' => $label]) }}
    </p>
@endif
