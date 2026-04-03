@props(['lang'])

<span class="language-tab-label d-inline-flex align-items-center flex-nowrap gap-1">
    <span class="language-tab-label__text">{{ $lang->name }}</span>
    @if ($lang->code === \App\Enums\Content\ContentLanguage::NEUTRAL->value)
        <x-ui.info-popover
            tag="span"
            :content="__('view.shared.languages.neutral_tooltip')"
            aria-label="{{ __('view.shared.languages.neutral_tooltip_short') }}"
        />
    @endif
</span>
