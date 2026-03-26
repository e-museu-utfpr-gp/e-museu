@props([
    'for' => null,
    'label' => null,
    'wrapperClass' => 'mb-3',
])

<div @class([$wrapperClass => true])>
    @if ($label)
        <label @if ($for) for="{{ $for }}" @endif class="form-label">{{ $label }}</label>
    @endif
    {{ $slot }}
</div>
