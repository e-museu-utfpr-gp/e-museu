<x-layouts.admin :title="__('view.admin.catalog.item_categories.create.title')">
    <div class="mb-auto container-fluid">
        <x-ui.flash-messages />
        <form action="{{ route('admin.catalog.item-categories.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">
                            {{ __('view.admin.catalog.item_categories.create.heading') }}
                        </h2>
                    </div>
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
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i>
                            {{ __('view.admin.catalog.item_categories.create.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
