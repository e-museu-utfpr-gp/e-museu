@php
    use Illuminate\Support\Str;

    $headingCode = $preferredContentTabLanguageCode ?? '';
    $headingInfoRaw = $headingCode !== ''
        ? old('translations.' . $headingCode . '.info', $headingTranslation?->info ?? '')
        : ($headingTranslation?->info ?? '');
    $headingPreview = $headingInfoRaw !== ''
        ? Str::limit(trim(strip_tags((string) $headingInfoRaw)), 80)
        : '—';
@endphp
<x-layouts.admin :title="__('view.admin.catalog.extras.edit.title') . ' ' . $extra->id"
    :heading="__('view.admin.catalog.extras.edit.heading', ['id' => $extra->id, 'preview' => $headingPreview])">
            <form action="{{ route('admin.catalog.extras.update', $extra->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-md-6">
                        <div class="row" data-section-item-selector
                             data-section-selector="#category_id"
                             data-item-selector="#item_id"
                             data-original-item-id="{{ $extra->item->id }}"
                             data-old-selected-id="{{ old('item_id', $extra->item_id) }}"
                             data-get-items-url="{{ route('admin.catalog.items.by-item-category') }}">
                            <div class="col-md-4">
                                <x-ui.inputs.admin.select
                                    name="category_id"
                                    id="category_id"
                                    :label="__('view.admin.catalog.extras.edit.item_category')"
                                    required
                                >
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}"
                                            @selected(old('category_id', $extra->item->itemCategory?->id) == $itemCategory->id)>
                                            {{ $itemCategory->name }}</option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                            </div>
                            <div class="col-md-8">
                                <x-ui.inputs.admin.select
                                    name="item_id"
                                    id="item_id"
                                    :label="__('view.admin.catalog.extras.edit.item')"
                                    required
                                >
                                </x-ui.inputs.admin.select>
                            </div>
                        </div>
                        @include('pages.admin.catalog.extras._partials.translation-tabs', [
                            'contentLanguages' => $contentLanguages,
                            'preferredContentTabLanguageId' => $preferredContentTabLanguageId,
                            'extra' => $extra,
                        ])
                        <x-ui.inputs.admin.select
                            name="collaborator_id"
                            id="collaborator_id"
                            :label="__('view.admin.catalog.extras.edit.collaborator')"
                            required
                        >
                            @foreach ($collaborators as $collaborator)
                                <option value="{{ $collaborator->id }}"
                                    @selected(old('collaborator_id', $extra->collaborator->id) == $collaborator->id)>
                                    {{ $collaborator->email }}</option>
                            @endforeach
                        </x-ui.inputs.admin.select>
                        <x-ui.inputs.admin.select
                            name="validation"
                            id="validation"
                            :label="__('view.admin.catalog.extras.edit.validation')"
                            required
                        >
                            <option value="0" @selected(old('validation', $extra->validation) == 0)>{{ __('view.admin.catalog.extras.edit.no') }}</option>
                            <option value="1" @selected(old('validation', $extra->validation) == 1)>{{ __('view.admin.catalog.extras.edit.yes') }}</option>
                        </x-ui.inputs.admin.select>
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">
                                {{ __('view.admin.catalog.extras.edit.submit') }}
                            </x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
                <x-release-lock-on-leave type="extras" :id="$extra->id" />
            </form>
</x-layouts.admin>
