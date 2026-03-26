@props([
    'content',
    'placement' => 'top',
    'trigger' => 'focus',
])

<button
    type="button"
    class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
    data-bs-toggle="popover"
    data-bs-placement="{{ $placement }}"
    data-bs-trigger="{{ $trigger }}"
    data-bs-content="{{ e($content) }}"
    {{ $attributes->merge(['aria-label' => __('view.shared.info_popover_label')]) }}
>
    <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;" aria-hidden="true"></i>
</button>
