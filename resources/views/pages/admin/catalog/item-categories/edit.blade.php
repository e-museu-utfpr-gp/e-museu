<x-layouts.admin :title="__('view.admin.catalog.item_categories.edit.title', ['id' =>
    $itemCategory->id])" :heading="__('view.admin.catalog.item_categories.edit.heading', ['id' => $itemCategory->id, 'name' => $itemCategory->name])">
            <form action="{{ route('admin.catalog.item-categories.update', $itemCategory->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                {{ __('view.admin.catalog.item_categories.edit.name') }}
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ $itemCategory->name }}">
                            @error('name')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
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
