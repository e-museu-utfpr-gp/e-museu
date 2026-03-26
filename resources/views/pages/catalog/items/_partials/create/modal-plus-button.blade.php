@props([
    'target',
    'id',
])

<x-ui.buttons.default
    type="button"
    variant="plain"
    class="button nav-link px-2 pb-2"
    data-bs-toggle="modal"
    data-bs-target="{{ $target }}"
    {{ $attributes->merge(['aria-label' => __('view.shared.buttons.add')]) }}
    id="{{ $id }}"
>
    <i class="bi bi-plus h3" aria-hidden="true"></i>
</x-ui.buttons.default>

