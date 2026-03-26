<x-layouts.admin :title="__('view.admin.catalog.item_categories.edit.title', ['id' =>
    $itemCategory->id])" :heading="__('view.admin.catalog.item_categories.edit.heading', ['id' => $itemCategory->id, 'name' => $itemCategory->name])">
            <form action="{{ route('admin.catalog.item-categories.update', $itemCategory->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-md-6">
                        <x-ui.inputs.admin.text
                            name="name"
                            id="name"
                            :label="__('view.admin.catalog.item_categories.edit.name')"
                            :value="$itemCategory->name"
                        />
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">
                                {{ __('view.admin.catalog.item_categories.edit.submit') }}
                            </x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
            </form>
        <x-release-lock-on-leave type="item-categories" :id="$itemCategory->id" />
</x-layouts.admin>
