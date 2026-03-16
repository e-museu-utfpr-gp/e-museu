@props(['type', 'id'])
@if (isset($type) && isset($id))
    <div
        data-release-lock="true"
        data-release-lock-url="{{ route('admin.release-lock') }}"
        data-release-lock-type="{{ $type }}"
        data-release-lock-id="{{ $id }}"
    ></div>
@endif
