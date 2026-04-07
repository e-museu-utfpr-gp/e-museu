@props(['href'])

<x-ui.buttons.default
    href="{{ $href }}"
    variant="primary"
    icon="bi bi-eye-fill"
    {{ $attributes->merge(['aria-label' => __('view.shared.buttons.view')]) }}
/>
