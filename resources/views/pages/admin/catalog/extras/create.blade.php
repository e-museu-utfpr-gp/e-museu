<x-layouts.admin :title="__('view.admin.catalog.extras.create.title')"
    :heading="__('view.admin.catalog.extras.create.heading')">
            <form action="{{ route('admin.catalog.extras.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <x-ui.inputs.admin.textarea
                            name="info"
                            id="info"
                            :rows="5"
                            :label="__('view.admin.catalog.extras.create.info')"
                        />
                        <div class="row" data-section-item-selector 
                             data-section-selector="#category_id" 
                             data-item-selector="#item_id" 
                             data-original-item-id="{{ request()->query('id') }}"
                             data-old-selected-id="{{ old('item_id', '') }}"
                             data-get-items-url="{{ route('admin.catalog.items.by-item-category') }}">
                            <div class="col-md-4">
                                <x-ui.inputs.admin.select
                                    name="category_id"
                                    id="category_id"
                                    :label="__('view.admin.catalog.extras.create.item_category')"
                                    required
                                >
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}"
                                            @selected(old('category_id', request()->query('item_category')) == $itemCategory->id)>
                                            {{ $itemCategory->name }}</option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                            </div>
                            <div class="col-md-8">
                                <x-ui.inputs.admin.select
                                    name="item_id"
                                    id="item_id"
                                    :label="__('view.admin.catalog.extras.create.item')"
                                    required
                                >
                                </x-ui.inputs.admin.select>
                            </div>
                        </div>
                        <x-ui.inputs.admin.select
                            name="collaborator_id"
                            id="collaborator_id"
                            :label="__('view.admin.catalog.extras.create.collaborator')"
                            required
                        >
                            @foreach ($collaborators as $collaborator)
                                <option value="{{ $collaborator->id }}" @selected(old('collaborator_id') == $collaborator->id)>
                                    {{ $collaborator->contact }}</option>
                            @endforeach
                        </x-ui.inputs.admin.select>
                        <x-ui.inputs.admin.select
                            name="validation"
                            id="validation"
                            :label="__('view.admin.catalog.extras.create.validation')"
                            required
                        >
                            <option value="0" @selected(old('validation') == 0)>{{ __('view.admin.catalog.extras.create.no') }}</option>
                            <option value="1" @selected(old('validation') == 1)>{{ __('view.admin.catalog.extras.create.yes') }}</option>
                        </x-ui.inputs.admin.select>
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">
                                {{ __('view.admin.catalog.extras.create.submit') }}
                            </x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
            </form>
</x-layouts.admin>
