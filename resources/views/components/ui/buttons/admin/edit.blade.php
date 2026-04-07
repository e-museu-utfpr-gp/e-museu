@props(['href'])

<x-ui.buttons.default
    href="{{ $href }}"
    variant="warning"
    icon="bi bi-pencil-fill"
    {{ $attributes->merge(['aria-label' => __('view.shared.buttons.edit')]) }}
/>
