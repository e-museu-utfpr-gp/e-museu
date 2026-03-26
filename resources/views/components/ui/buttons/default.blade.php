@props([
    'variant' => 'primary',
    'size' => null,
    'type' => 'button',
    'href' => null,
    'icon' => null,
])

@php
    $variantMap = [
        'primary' => 'btn btn-primary',
        'secondary' => 'btn btn-secondary',
        'success' => 'btn btn-success',
        'danger' => 'btn btn-danger',
        'warning' => 'btn btn-warning',
        'info' => 'btn btn-info',
        'light' => 'btn btn-light',
        'dark' => 'btn btn-dark',
        'outline-primary' => 'btn btn-outline-primary',
        'outline-secondary' => 'btn btn-outline-secondary',
        'link' => 'btn btn-link',
        'ghost' => 'btn border-0 bg-transparent px-0 py-0',
        'plain' => '',
    ];
    $base = $variantMap[$variant] ?? $variantMap['primary'];
    $sizeClass = match ($size) {
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        default => '',
    };
    $componentClass = trim($base . ' ' . $sizeClass);
    $isLink = filled($href);
@endphp

@if ($isLink)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $componentClass]) }}>
        @if ($icon)
            <i class="{{ $icon }}{{ $slot->isNotEmpty() ? ' me-1' : '' }}" @if ($slot->isEmpty()) aria-hidden="true" @endif></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $componentClass]) }}>
        @if ($icon)
            <i class="{{ $icon }}{{ $slot->isNotEmpty() ? ' me-1' : '' }}" @if ($slot->isEmpty()) aria-hidden="true" @endif></i>
        @endif
        {{ $slot }}
    </button>
@endif
