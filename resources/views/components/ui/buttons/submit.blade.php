@props([
    'type' => 'submit',
    'variant' => 'primary',
    'icon' => null,
])

<x-ui.buttons.default
    type="{{ $type }}"
    variant="{{ $variant }}"
    :icon="$icon"
    {{ $attributes }}
>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @else
        {{ __('view.shared.buttons.submit') }}
    @endif
</x-ui.buttons.default>
