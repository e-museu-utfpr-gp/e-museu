<x-layouts.admin :title="__('view.admin.taxonomy.tags.create.title')"
    :heading="__('view.admin.taxonomy.tags.create.heading')">
        <form action="{{ route('admin.taxonomy.tags.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <x-ui.inputs.admin.select
                        name="category_id"
                        id="category_id"
                        :label="__('view.admin.taxonomy.tags.create.category')"
                        required
                    >
                        <option value="" @selected(old('category_id') === null || old('category_id') === '')>-</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </x-ui.inputs.admin.select>
                    @include('pages.admin.taxonomy.tags._partials.translation-tabs', [
                        'contentLanguages' => $contentLanguages,
                        'preferredContentTabLanguageId' => $preferredContentTabLanguageId,
                        'tag' => null,
                    ])
                    <x-ui.inputs.admin.select
                        name="validation"
                        id="validation"
                        :label="__('view.admin.taxonomy.tags.create.validation')"
                    >
                        <option value="0" @selected(old('validation') == 0)>{{ __('view.admin.taxonomy.tags.create.no') }}</option>
                        <option value="1" @selected(old('validation') == 1)>{{ __('view.admin.taxonomy.tags.create.yes') }}</option>
                    </x-ui.inputs.admin.select>
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">
                            {{ __('view.admin.taxonomy.tags.create.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </div>
            </div>
        </form>
</x-layouts.admin>
