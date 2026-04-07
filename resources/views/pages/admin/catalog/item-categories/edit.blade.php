@php
    $headingCode = $preferredContentTabLanguageCode ?? '';
    $headingName = $headingCode !== ''
        ? old('translations.' . $headingCode . '.name', $headingTranslation?->name ?? '—')
        : ($headingTranslation?->name ?? '—');
@endphp
<x-layouts.admin :title="__('view.admin.catalog.item_categories.edit.title', ['id' => $itemCategory->id])"
    :heading="__('view.admin.catalog.item_categories.edit.heading', ['id' => $itemCategory->id, 'name' => $headingName])">
            <form action="{{ route('admin.catalog.item-categories.update', $itemCategory->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-md-6">
                        @include('pages.admin.catalog.item-categories._partials.translation-tabs', [
                            'contentLanguages' => $contentLanguages,
                            'preferredContentTabLanguageId' => $preferredContentTabLanguageId,
                            'itemCategory' => $itemCategory,
                        ])
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">
                                {{ __('view.admin.catalog.item_categories.edit.submit') }}
                            </x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
                <x-release-lock-on-leave type="item-categories" :id="$itemCategory->id" />
            </form>
</x-layouts.admin>
