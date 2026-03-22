<x-layouts.admin :title="__('view.admin.catalog.components.create.title')">
    <div class="mb-auto container-fluid">
        <x-ui.flash-messages />
        <form action="{{ route('admin.catalog.item-components.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">{{ __('view.admin.catalog.components.create.heading') }}</h2>
                    </div>
                    <div class="row" data-section-item-selector 
                         data-section-selector="#category_id" 
                         data-item-selector="#item_id" 
                         data-original-item-id="{{ request()->query('id') }}"
                         data-get-items-url="{{ route('catalog.items.byCategory') }}">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">{{ __('view.admin.catalog.components.create.item_category') }}</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id">
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}"
                                            {{ old('category_id', request()->query('item_category')) == $itemCategory->id ? 'selected' : '' }}>
                                            {{ $itemCategory->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item_id" class="form-label">{{ __('view.admin.catalog.components.create.item') }}</label>
                                <select class="form-select @error('item_id') is-invalid @enderror" id="item_id"
                                    name="item_id">
                                    <option value="">-</option>
                                </select>
                                @error('item_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row" data-section-item-selector
                         data-section-selector="#component_category_id"
                         data-item-selector="#component_id"
                         data-get-items-url="{{ route('catalog.items.byCategory') }}">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="component_category_id" class="form-label">{{ __('view.admin.catalog.components.create.component_category') }}</label>
                                <select class="form-select @error('component_category_id') is-invalid @enderror"
                                    id="component_category_id" name="component_category_id">
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}"
                                            {{ old('component_category_id') == $itemCategory->id ? 'selected' : '' }}>
                                            {{ $itemCategory->name }}</option>
                                    @endforeach
                                </select>
                                @error('component_category_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="component_id" class="form-label">{{ __('view.admin.catalog.components.create.component') }}</label>
                                <select class="form-select @error('component_id') is-invalid @enderror" id="component_id"
                                    name="component_id">
                                    <option value="">-</option>
                                </select>
                                @error('component_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="validation" class="form-label">{{ __('view.admin.catalog.components.create.validation') }}</label>
                        <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                            name="validation">
                            <option value="0" {{ old('validation') == 0 ? 'selected' : '' }}>{{ __('view.admin.catalog.components.create.no') }}</option>
                            <option value="1" {{ old('validation') == 1 ? 'selected' : '' }}>{{ __('view.admin.catalog.components.create.yes') }}</option>
                        </select>
                        @error('validation')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> {{ __('view.admin.catalog.components.create.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>


</x-layouts.admin>
