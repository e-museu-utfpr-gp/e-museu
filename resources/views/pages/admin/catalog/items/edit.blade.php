<x-layouts.admin :title="__('view.admin.catalog.items.edit.title') . ' ' . $item->
    id" :heading="__('view.admin.catalog.items.edit.heading', ['id' => $item->id, 'name' => $item->name])">
            <form action="{{ route('admin.catalog.items.update', $item->id) }}" method="POST" enctype="multipart/form-data" id="admin-item-edit-form"
                data-label-cover="{{ __('app.catalog.item_image.cover') }}"
                data-label-gallery="{{ __('app.catalog.item_image.gallery') }}"
            >
                @csrf
                @method('PATCH')
                <div id="admin-delete-image-ids"></div>
                <input type="hidden" name="set_cover_image_id" id="set_cover_image_id" value="">
                <div class="row">
                    <div class="col-md-6">
                        <x-ui.inputs.admin.text
                            name="name"
                            id="name"
                            :label="__('view.admin.catalog.items.edit.name')"
                            :value="$item->name"
                        />
                        <x-ui.inputs.admin.textarea
                            name="description"
                            id="description"
                            :rows="5"
                            :label="__('view.admin.catalog.items.edit.description')"
                            :value="$item->description"
                        />
                        <x-ui.inputs.admin.textarea
                            name="detail"
                            id="detail"
                            :rows="7"
                            :label="__('view.admin.catalog.items.edit.detail')"
                            :value="$item->detail"
                        />
                        <div class="row">
                            <div class="col-md-6">
                                <x-ui.inputs.admin.select
                                    name="category_id"
                                    id="category_id"
                                    :label="__('view.admin.catalog.items.edit.item_category')"
                                    required
                                >
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}" @selected(old('category_id', $item->category_id) == $itemCategory->id)>
                                            {{ $itemCategory->name }}
                                        </option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                                <x-ui.inputs.admin.select
                                    name="collaborator_id"
                                    id="collaborator_id"
                                    :label="__('view.admin.catalog.items.edit.collaborator')"
                                    required
                                >
                                    @foreach ($collaborators as $collaborator)
                                        <option value="{{ $collaborator->id }}" @selected(old('collaborator_id', $item->collaborator_id) == $collaborator->id)>
                                            {{ $collaborator->contact }} - {{ $collaborator->full_name }}
                                        </option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                                <x-ui.inputs.admin.text
                                    name="date"
                                    id="date"
                                    type="date"
                                    :label="__('view.admin.catalog.items.edit.date')"
                                    :value="$item->date?->format('Y-m-d')"
                                />
                                <x-ui.inputs.admin.text
                                    name="identification_code"
                                    id="identification_code"
                                    :label="__('view.admin.catalog.items.edit.identification_code')"
                                    :value="$item->identification_code"
                                />
                                <x-ui.inputs.admin.select
                                    name="validation"
                                    id="validation"
                                    :label="__('view.admin.catalog.items.edit.validation')"
                                >
                                    <option value="0" @selected(old('validation', $item->validation) == 0)>{{ __('view.admin.catalog.items.edit.no') }}</option>
                                    <option value="1" @selected(old('validation', $item->validation) == 1)>{{ __('view.admin.catalog.items.edit.yes') }}</option>
                                </x-ui.inputs.admin.select>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $currentCoverImage = $item->coverImage ?? $item->images->sortBy('sort_order')->first();
                                @endphp
                                @include('pages.admin.catalog.items._partials.edit.cover-upload')
                                @include('pages.admin.catalog.items._partials.edit.gallery-upload')
                                @include('pages.admin.catalog.items._partials.edit.current-images-preview')
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-ui.inputs.admin.textarea
                            name="history"
                            id="history"
                            :rows="46"
                            :label="__('view.admin.catalog.items.edit.history')"
                            :value="$item->history"
                        />
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">{{ __('view.admin.catalog.items.edit.submit') }}</x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
                <x-release-lock-on-leave type="items" :id="$item->id" />
            </form>

        <x-ui.image-modal />
        <x-ui.images.catalog.upload-assets />

</x-layouts.admin>
