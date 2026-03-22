<x-layouts.admin :title="__('view.admin.taxonomy.tags.create.title')"
    :heading="__('view.admin.taxonomy.tags.create.heading')">
        <form action="{{ route('admin.taxonomy.tags.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('view.admin.taxonomy.tags.create.name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="validation" class="form-label">{{ __('view.admin.taxonomy.tags.create.validation') }}</label>
                        <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                            name="validation">
                            <option value="0" {{ old('validation') == 0 ? 'selected' : '' }}>{{ __('view.admin.taxonomy.tags.create.no') }}</option>
                            <option value="1" {{ old('validation') == 1 ? 'selected' : '' }}>{{ __('view.admin.taxonomy.tags.create.yes') }}</option>
                        </select>
                        @error('validation')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">{{ __('view.admin.taxonomy.tags.create.category') }}</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                            name="category_id">
                            <option selected="selected" value="">-</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">
                            {{ __('view.admin.taxonomy.tags.create.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </div>
            </div>
        </form>
</x-layouts.admin>
