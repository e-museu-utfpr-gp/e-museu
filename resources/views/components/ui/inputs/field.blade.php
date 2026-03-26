@props([
    'for' => null,
    'label' => null,
    'help' => null,
    'roundedTop' => false,
])

<div {{ $attributes }}>
    @if ($label)
        <label @if ($for) for="{{ $for }}" @endif>
            <h5>
                {{ $label }}
                @if ($help)
                    <x-ui.info-popover :content="$help" />
                @endif
            </h5>
        </label>
    @endif
    <div @class(['input-div', 'rounded-top' => $roundedTop])>
        {{ $slot }}
    </div>
</div>
