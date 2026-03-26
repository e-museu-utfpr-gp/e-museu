@props([
    'type' => 'submit',
])

<x-ui.buttons.default
    type="{{ $type }}"
    variant="warning"
    icon="bi bi-check2-circle"
    {{ $attributes }}
>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @else
        {{ __('view.shared.buttons.validate_invalidate') }}
    @endif
</x-ui.buttons.default>
