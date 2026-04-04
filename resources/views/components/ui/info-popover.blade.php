@props([
    'content',
    'placement' => 'top',
    'trigger' => 'hover click',
    'tag' => 'button',
    /** Ms to wait after mouse leaves trigger/tip before hiding (popover is in body; needs a bridge). */
    'hideDelayMs' => 280,
])

@php
    $isSpan = $tag === 'span';
    $delayForBootstrap = ['show' => 0, 'hide' => max(0, (int) $hideDelayMs)];
    $baseClass = $isSpan
        ? 'info-icon border-0 bg-transparent p-0 d-inline-flex align-items-center justify-content-center flex-shrink-0'
        : 'info-icon btn border-0 bg-transparent px-0 py-0 mb-1';
@endphp

@if ($isSpan)
    <span
        role="button"
        tabindex="0"
        class="{{ $baseClass }}"
        data-bs-toggle="popover"
        data-bs-placement="{{ $placement }}"
        data-bs-trigger="{{ $trigger }}"
        data-bs-delay='@json($delayForBootstrap)'
        data-bs-content="{{ e($content) }}"
        {{ $attributes->merge(['aria-label' => __('view.shared.info_popover_label')]) }}
    >
        <i
            class="bi bi-info-circle-fill tab-info-popover-icon"
            style="color: #ED6E38; cursor: pointer;"
            aria-hidden="true"
        ></i>
    </span>
@else
    <button
        type="button"
        class="{{ $baseClass }}"
        data-bs-toggle="popover"
        data-bs-placement="{{ $placement }}"
        data-bs-trigger="{{ $trigger }}"
        data-bs-delay='@json($delayForBootstrap)'
        data-bs-content="{{ e($content) }}"
        {{ $attributes->merge(['aria-label' => __('view.shared.info_popover_label')]) }}
    >
        <i
            class="bi bi-info-circle-fill h4 ms-1"
            style="color: #ED6E38; cursor: pointer;"
            aria-hidden="true"
        ></i>
    </button>
@endif
