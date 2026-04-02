@php
    $headingCode = $preferredContentTabLanguageCode ?? '';
    $headingName = $headingCode !== ''
        ? old('translations.' . $headingCode . '.name', $headingTranslation?->name ?? '—')
        : ($headingTranslation?->name ?? '—');
@endphp
<x-layouts.admin :title="__('view.admin.catalog.items.edit.title') . ' ' . $item->id"
    :heading="__('view.admin.catalog.items.edit.heading', ['id' => $item->id, 'name' => $headingName])">
            <form action="{{ route('admin.catalog.items.update', $item->id) }}" method="POST" enctype="multipart/form-data" id="admin-item-edit-form"
                data-label-cover="{{ __('app.catalog.item_image.cover') }}"
                data-label-gallery="{{ __('app.catalog.item_image.gallery') }}"
            >
                @csrf
                @method('PATCH')
                <div id="admin-delete-image-ids"></div>
                <input type="hidden" name="set_cover_image_id" id="set_cover_image_id" value="">
                @include('pages.admin.catalog.items._partials.translation-tabs', [
                    'contentLanguages' => $contentLanguages,
                    'preferredContentTabLanguageId' => $preferredContentTabLanguageId,
                    'item' => $item,
                ])
                <div class="row">
                    <div class="col-md-6">
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
                </div>
                <div class="mb-3">
                    <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">{{ __('view.admin.catalog.items.edit.submit') }}</x-ui.buttons.submit>
                </div>
                <x-release-lock-on-leave type="items" :id="$item->id" />
            </form>

        <x-ui.image-modal />
        <x-ui.images.catalog.upload-assets />

</x-layouts.admin>
