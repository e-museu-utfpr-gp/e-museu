<x-layouts.admin :title="__('view.admin.catalog.items.create.title')"
    :heading="__('view.admin.catalog.items.create.heading')">
            <form action="{{ route('admin.catalog.items.store') }}" method="POST" enctype="multipart/form-data" id="admin-item-create-form"
                data-label-cover="{{ __('app.catalog.item_image.cover') }}"
                data-label-gallery="{{ __('app.catalog.item_image.gallery') }}"
                data-label-remove-image="{{ __('view.catalog.items.create.remove_image') }}"
            >
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <x-ui.inputs.admin.text
                            name="name"
                            id="name"
                            :label="__('view.admin.catalog.items.create.name')"
                        />
                        <x-ui.inputs.admin.textarea
                            name="description"
                            id="description"
                            :rows="5"
                            :label="__('view.admin.catalog.items.create.description')"
                        />
                        <x-ui.inputs.admin.textarea
                            name="detail"
                            id="detail"
                            :rows="7"
                            :label="__('view.admin.catalog.items.create.detail')"
                        />
                        <div class="row">
                            <div class="col-md-6">
                                <x-ui.inputs.admin.select
                                    name="category_id"
                                    id="category_id"
                                    :label="__('view.admin.catalog.items.create.item_category')"
                                    required
                                >
                                    <option value="" @selected(old('category_id') === null || old('category_id') === '')>-</option>
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}" @selected(old('category_id') == $itemCategory->id)>
                                            {{ $itemCategory->name }}
                                        </option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                                <x-ui.inputs.admin.select
                                    name="collaborator_id"
                                    id="collaborator_id"
                                    :label="__('view.admin.catalog.items.create.collaborator')"
                                    required
                                >
                                    <option value="" @selected(old('collaborator_id') === null || old('collaborator_id') === '')>-</option>
                                    @foreach ($collaborators as $collaborator)
                                        <option value="{{ $collaborator->id }}" @selected(old('collaborator_id') == $collaborator->id)>
                                            {{ $collaborator->contact }} - {{ $collaborator->full_name }}
                                        </option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                                <x-ui.inputs.admin.text
                                    name="date"
                                    id="date"
                                    type="date"
                                    :label="__('view.admin.catalog.items.create.date')"
                                />
                            </div>
                            <div class="col-md-6">
                            @include('pages.admin.catalog.items._partials.create.images-upload')
                                <x-ui.inputs.admin.select
                                    name="validation"
                                    id="validation"
                                    :label="__('view.admin.catalog.items.create.validation')"
                                >
                                    <option value="0" @selected(old('validation') == 0)>{{ __('view.admin.catalog.items.create.no') }}</option>
                                    <option value="1" @selected(old('validation') == 1)>{{ __('view.admin.catalog.items.create.yes') }}</option>
                                </x-ui.inputs.admin.select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-ui.inputs.admin.textarea
                            name="history"
                            id="history"
                            :rows="46"
                            :label="__('view.admin.catalog.items.create.history')"
                        />
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">{{ __('view.admin.catalog.items.create.submit') }}</x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
            </form>
        <x-ui.images.catalog.upload-assets />
</x-layouts.admin>
