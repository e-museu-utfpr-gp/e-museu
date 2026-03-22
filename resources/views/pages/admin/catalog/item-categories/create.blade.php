<x-layouts.admin :title="__('view.admin.catalog.item_categories.create.title')"
    :heading="__('view.admin.catalog.item_categories.create.heading')">
            <form action="{{ route('admin.catalog.item-categories.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                {{ __('view.admin.catalog.item_categories.create.name') }}
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">
                                {{ __('view.admin.catalog.item_categories.create.submit') }}
                            </x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
            </form>
</x-layouts.admin>
