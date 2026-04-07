<x-layouts.admin :title="__('view.admin.catalog.components.create.title')"
    :heading="__('view.admin.catalog.components.create.heading')">
            <form action="{{ route('admin.catalog.item-components.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="row" data-section-item-selector 
                             data-section-selector="#category_id" 
                             data-item-selector="#item_id" 
                             data-original-item-id="{{ request()->query('id') }}"
                             data-old-selected-id="{{ old('item_id', '') }}"
                             data-get-items-url="{{ route('admin.catalog.items.by-item-category') }}">
                            <div class="col-md-6">
                                <x-ui.inputs.admin.select
                                    name="category_id"
                                    id="category_id"
                                    :label="__('view.admin.catalog.components.create.item_category')"
                                    required
                                >
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}"
                                            @selected(old('category_id', request()->query('item_category')) == $itemCategory->id)>
                                            {{ $itemCategory->name }}</option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                            </div>
                            <div class="col-md-6">
                                <x-ui.inputs.admin.select
                                    name="item_id"
                                    id="item_id"
                                    :label="__('view.admin.catalog.components.create.item')"
                                    required
                                >
                                    <option value="">-</option>
                                </x-ui.inputs.admin.select>
                            </div>
                        </div>
                        <div class="row" data-section-item-selector
                             data-section-selector="#component_category_id"
                             data-item-selector="#component_id"
                             data-old-selected-id="{{ old('component_id', '') }}"
                             data-get-items-url="{{ route('admin.catalog.items.by-item-category') }}">
                            <div class="col-md-6">
                                <x-ui.inputs.admin.select
                                    name="component_category_id"
                                    id="component_category_id"
                                    :label="__('view.admin.catalog.components.create.component_category')"
                                    required
                                >
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}"
                                            @selected(old('component_category_id') == $itemCategory->id)>
                                            {{ $itemCategory->name }}</option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                            </div>
                            <div class="col-md-6">
                                <x-ui.inputs.admin.select
                                    name="component_id"
                                    id="component_id"
                                    :label="__('view.admin.catalog.components.create.component')"
                                    required
                                >
                                    <option value="">-</option>
                                </x-ui.inputs.admin.select>
                            </div>
                        </div>
                        <x-ui.inputs.admin.select
                            name="validation"
                            id="validation"
                            :label="__('view.admin.catalog.components.create.validation')"
                            required
                        >
                            <option value="0" @selected(old('validation') == 0)>{{ __('view.admin.catalog.components.create.no') }}</option>
                            <option value="1" @selected(old('validation') == 1)>{{ __('view.admin.catalog.components.create.yes') }}</option>
                        </x-ui.inputs.admin.select>
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">
                                {{ __('view.admin.catalog.components.create.submit') }}
                            </x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
            </form>
</x-layouts.admin>
