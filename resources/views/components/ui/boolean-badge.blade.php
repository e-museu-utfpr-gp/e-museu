@props(['value'])

@if ($value)
    {{ __('common.yes') }}
@else
    {{ __('common.no') }}
@endif
