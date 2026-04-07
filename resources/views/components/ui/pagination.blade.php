@props([
    /**
     * Laravel paginator instance (e.g. $items) used to render `links()`.
     */
    'paginator',
    /**
     * Bootstrap color key used to drive CSS variables on `.pagination`.
     * Examples: primary, success, warning, danger, secondary, dark, info.
     */
    'variant' => 'primary',
    /**
     * Kept for backward compatibility; pagination row is always full width with
     * summary on the left and page links on the right (see vendor pagination view).
     */
    'align' => 'between',
    /**
     * Optional extra classes applied to the outer wrapper.
     */
    'class' => '',
])

@php
    $activeColorMap = [
        'primary' => '#0d6efd',
        'success' => '#198754',
        'warning' => '#ffc107',
        'danger' => '#dc3545',
        'info' => '#0dcaf0',
        'secondary' => '#6c757d',
        'dark' => '#212529',
    ];

    $activeColor = $activeColorMap[$variant] ?? $activeColorMap['primary'];
@endphp

<div class="w-100 {{ $class }}"
    style="--bs-pagination-color: {{ $activeColor }}; --bs-pagination-hover-color: {{ $activeColor }}; --bs-pagination-active-bg: {{ $activeColor }}; --bs-pagination-active-border-color: {{ $activeColor }}; --bs-pagination-active-color: #fff;">
    {{-- Laravel generates this markup; it is not user-provided HTML. --}}
    {!! $paginator->links('pagination::bootstrap-5') !!}
</div>
