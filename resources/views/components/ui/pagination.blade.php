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
     * Bootstrap justify-content key: center | start | end
     */
    'align' => 'center',
    /**
     * Optional extra classes applied to the outer <nav>.
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
    $alignClassMap = [
        'start' => 'justify-content-start',
        'center' => 'justify-content-center',
        'end' => 'justify-content-end',
    ];
    $alignClass = $alignClassMap[$align] ?? $alignClassMap['center'];
@endphp

<div class="d-flex {{ $alignClass }} {{ $class }}"
    style="--bs-pagination-color: {{ $activeColor }}; --bs-pagination-hover-color: {{ $activeColor }}; --bs-pagination-active-bg: {{ $activeColor }}; --bs-pagination-active-border-color: {{ $activeColor }}; --bs-pagination-active-color: #fff;">
    {{-- Laravel generates this markup; it is not user-provided HTML. --}}
    {!! $paginator->links('pagination::bootstrap-5') !!}
</div>

