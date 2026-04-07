@props(['text'])

<div {{ $attributes->class(['card', 'mb-3']) }}>
    <h1 class="card-header h2 mb-0">{{ $text }}</h1>
    @if ($slot->isNotEmpty())
        <div class="card-body d-flex">
            {{ $slot }}
        </div>
    @endif
</div>
