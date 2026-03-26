@props([
    'type' => 'submit',
])

<x-ui.buttons.default
    type="{{ $type }}"
    variant="danger"
    icon="bi bi-trash-fill"
    {{ $attributes->merge(['aria-label' => __('view.shared.buttons.delete')]) }}
/>
