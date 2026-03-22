<x-layouts.admin :title="__('view.admin.taxonomy.tags.edit.title') . ' ' . $tag->id"
    :heading="__('view.admin.taxonomy.tags.edit.heading', ['id' => $tag->id, 'name' => $tag->name])">
        <form action="{{ route('admin.taxonomy.tags.update', $tag->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('view.admin.taxonomy.tags.edit.name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ $tag->name }}">
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="validation" class="form-label">{{ __('view.admin.taxonomy.tags.edit.validation') }}</label>
                        <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                            name="validation">
                            <option value="0" @if ($tag->validation == 0) selected @endif>{{ __('view.admin.taxonomy.tags.edit.no') }}</option>
                            <option value="1" @if ($tag->validation == 1) selected @endif>{{ __('view.admin.taxonomy.tags.edit.yes') }}</option>
                        </select>
                        @error('validation')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">{{ __('view.admin.taxonomy.tags.edit.category') }}</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                            name="category_id">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @if ($tag->tag_category_id == $category->id) selected @endif>
                                    {{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">
                            {{ __('view.admin.taxonomy.tags.edit.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </div>
            </div>
        </form>
    <x-release-lock-on-leave type="tags" :id="$tag->id" />
</x-layouts.admin>
