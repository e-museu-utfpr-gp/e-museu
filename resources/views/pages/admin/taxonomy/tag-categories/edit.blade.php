<x-layouts.admin :title="__('view.admin.taxonomy.tag_categories.edit.title', ['id' => $tagCategory->id])"
    :heading="__('view.admin.taxonomy.tag_categories.edit.heading', ['id' => $tagCategory->id, 'name' => $tagCategory->name])">
        <form action="{{ route('admin.taxonomy.tag-categories.update', $tagCategory->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            {{ __('view.admin.taxonomy.tag_categories.edit.name') }}
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ $tagCategory->name }}">
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">
                            {{ __('view.admin.taxonomy.tag_categories.edit.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </div>
            </div>
        </form>
    <x-release-lock-on-leave type="tag-categories" :id="$tagCategory->id" />
</x-layouts.admin>
