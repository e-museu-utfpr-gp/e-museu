@php
    $headingCode = $preferredContentTabLanguageCode ?? '';
    $headingName = $headingCode !== ''
        ? old('translations.' . $headingCode . '.name', $headingTranslation?->name ?? '—')
        : ($headingTranslation?->name ?? '—');
@endphp
<x-layouts.admin :title="__('view.admin.taxonomy.tag_categories.edit.title', ['id' => $tagCategory->id])"
    :heading="__('view.admin.taxonomy.tag_categories.edit.heading', ['id' => $tagCategory->id, 'name' => $headingName])">
        <form action="{{ route('admin.taxonomy.tag-categories.update', $tagCategory->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    @include('pages.admin.taxonomy.tag-categories._partials.translation-tabs', [
                        'contentLanguages' => $contentLanguages,
                        'preferredContentTabLanguageId' => $preferredContentTabLanguageId,
                        'tagCategory' => $tagCategory,
                    ])
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">
                            {{ __('view.admin.taxonomy.tag_categories.edit.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </div>
            </div>
            <x-release-lock-on-leave type="tag-categories" :id="$tagCategory->id" />
        </form>
</x-layouts.admin>
