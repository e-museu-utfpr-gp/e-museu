<x-layouts.admin :title="__('view.admin.catalog.items.show.title') . ' ' . $item->
    id" :heading="__('view.admin.catalog.items.show.heading', ['id' => $item->id, 'name' => $item->name])">
    <x-slot name="pageHeaderActions">
        <x-ui.buttons.admin.edit href="{{ route('admin.catalog.items.edit', $item->id) }}" class="me-1" />
        <form action="{{ route('admin.catalog.items.destroy', $item->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ui.buttons.admin.delete id="deleteItemButton" class="deleteItemButton"
                data-confirm-message="{{ __('view.admin.catalog.items.show.delete_confirm') }}" />
        </form>
    </x-slot>
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        @include('pages.admin.catalog.items._partials.show.show-left-details')
                        @include('pages.admin.catalog.items._partials.show.show-right-details')
                    </div>
                </div>
                @include('pages.admin.catalog.items._partials.show.show-bottom-details')
            </div>

        <x-ui.image-modal />
</x-layouts.admin>
