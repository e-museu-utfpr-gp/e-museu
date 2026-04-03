<x-layouts.admin :title="__('view.admin.catalog.item_tags.create.title')"
    :heading="__('view.admin.catalog.item_tags.create.heading')">
            <form action="{{ route('admin.catalog.item-tags.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="row" data-section-item-selector 
                             data-section-selector="#item_category" 
                             data-item-selector="#item_id" 
                             data-original-item-id="{{ request()->query('id') }}"
                             data-old-selected-id="{{ old('item_id', '') }}"
                             data-get-items-url="{{ route('admin.catalog.items.by-item-category') }}">
                            <div class="col-md-4">
                                <x-ui.inputs.admin.select
                                    name="item_category"
                                    id="item_category"
                                    :label="__('view.admin.catalog.item_tags.create.item_category')"
                                    required
                                >
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}"
                                            @selected(old('item_category', request()->query('item_category')) == $itemCategory->id)>
                                            {{ $itemCategory->name }}</option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                            </div>
                            <div class="col-md-8">
                                <x-ui.inputs.admin.select
                                    name="item_id"
                                    id="item_id"
                                    :label="__('view.admin.catalog.item_tags.create.item')"
                                    required
                                >
                                    <option value="">-</option>
                                </x-ui.inputs.admin.select>
                            </div>
                        </div>
                        <div class="row" data-section-item-selector
                             data-section-selector="#tag_category_id"
                             data-item-selector="#tag_id"
                             data-category-query-key="category"
                             data-old-selected-id="{{ old('tag_id', '') }}"
                             data-get-items-url="{{ route('admin.catalog.tags.by-category') }}">
                            <div class="col-md-4">
                                <x-ui.inputs.admin.select
                                    name="category_id"
                                    id="tag_category_id"
                                    :label="__('view.admin.catalog.item_tags.create.category')"
                                    required
                                >
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                            {{ $category->name }}</option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                            </div>
                            <div class="col-md-8">
                                <x-ui.inputs.admin.select
                                    name="tag_id"
                                    id="tag_id"
                                    :label="__('view.admin.catalog.item_tags.create.tag')"
                                    required
                                >
                                    <option value="">-</option>
                                </x-ui.inputs.admin.select>
                            </div>
                        </div>
                        <x-ui.inputs.admin.select
                            name="validation"
                            id="validation"
                            :label="__('view.admin.catalog.item_tags.create.validation')"
                            required
                        >
                            <option value="0" @selected(old('validation') == 0)>{{ __('view.admin.catalog.item_tags.create.no') }}</option>
                            <option value="1" @selected(old('validation') == 1)>{{ __('view.admin.catalog.item_tags.create.yes') }}</option>
                        </x-ui.inputs.admin.select>
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">
                                {{ __('view.admin.catalog.item_tags.create.submit') }}
                            </x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
            </form>
</x-layouts.admin>
