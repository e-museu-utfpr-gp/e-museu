@props(['heading' => null])

<div class="mb-auto container-fluid">
    <x-ui.flash-messages />
    @if ($heading)
        <x-ui.page-title :text="$heading" />
    @endif
    {{ $slot }}
</div>
