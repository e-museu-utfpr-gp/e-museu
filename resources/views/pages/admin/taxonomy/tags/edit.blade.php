@php
    $headingCode = $preferredContentTabLanguageCode ?? '';
    $headingName = $headingCode !== ''
        ? old('translations.' . $headingCode . '.name', $headingTranslation?->name ?? '—')
        : ($headingTranslation?->name ?? '—');
@endphp
<x-layouts.admin :title="__('view.admin.taxonomy.tags.edit.title') . ' ' . $tag->id"
    :heading="__('view.admin.taxonomy.tags.edit.heading', ['id' => $tag->id, 'name' => $headingName])">
        <form action="{{ route('admin.taxonomy.tags.update', $tag->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <x-ui.inputs.admin.select
                        name="category_id"
                        id="category_id"
                        :label="__('view.admin.taxonomy.tags.edit.category')"
                        required
                    >
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $tag->tag_category_id) == $category->id)>
                                {{ $category->name }}</option>
                        @endforeach
                    </x-ui.inputs.admin.select>
                    @include('pages.admin.taxonomy.tags._partials.translation-tabs', [
                        'contentLanguages' => $contentLanguages,
                        'preferredContentTabLanguageId' => $preferredContentTabLanguageId,
                        'tag' => $tag,
                    ])
                    <x-ui.inputs.admin.select
                        name="validation"
                        id="validation"
                        :label="__('view.admin.taxonomy.tags.edit.validation')"
                    >
                        <option value="0" @selected(old('validation', $tag->validation) == 0)>{{ __('view.admin.taxonomy.tags.edit.no') }}</option>
                        <option value="1" @selected(old('validation', $tag->validation) == 1)>{{ __('view.admin.taxonomy.tags.edit.yes') }}</option>
                    </x-ui.inputs.admin.select>
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">
                            {{ __('view.admin.taxonomy.tags.edit.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </div>
            </div>
            <x-release-lock-on-leave type="tags" :id="$tag->id" />
        </form>
</x-layouts.admin>
