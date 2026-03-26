<x-layouts.admin :title="__('view.admin.catalog.item_categories.show.title', ['id' =>
    $itemCategory->id])" :heading="__('view.admin.catalog.item_categories.show.heading', ['id' => $itemCategory->id, 'name' => $itemCategory->name])">
    <x-slot name="pageHeaderActions">
        <x-ui.buttons.admin.edit href="{{ route('admin.catalog.item-categories.edit', $itemCategory->id) }}"
            class="me-1" />
        <form action="{{ route('admin.catalog.item-categories.destroy', $itemCategory->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ui.buttons.admin.delete class="deleteItemCategoryButton"
                data-confirm-message="{{ __('view.admin.catalog.item_categories.delete_confirm') }}" />
        </form>
    </x-slot>
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <h5 class="card-header">
                                    {{ __('view.admin.catalog.item_categories.show.id') }}
                                </h5>
                                <div class="card-body">
                                    <p class="card-text">{{ $itemCategory->id }}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">
                                    {{ __('view.admin.catalog.item_categories.show.name') }}
                                </h5>
                                <div class="card-body">
                                    <p class="card-text">{{ $itemCategory->name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <h5 class="card-header">
                                    {{ __('view.admin.catalog.item_categories.show.created_at') }}
                                </h5>
                                <div class="card-body">
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemCategory->created_at)) }}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">
                                    {{ __('view.admin.catalog.item_categories.show.updated_at') }}
                                </h5>
                                <div class="card-body">
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemCategory->updated_at)) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</x-layouts.admin>
