<x-layouts.admin :title="__('view.admin.taxonomy.tag_categories.edit.title', ['id' => $tagCategory->id])"
    :heading="__('view.admin.taxonomy.tag_categories.edit.heading', ['id' => $tagCategory->id, 'name' => $tagCategory->name])">
        <form action="{{ route('admin.taxonomy.tag-categories.update', $tagCategory->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <x-ui.inputs.admin.text
                        name="name"
                        id="name"
                        :label="__('view.admin.taxonomy.tag_categories.edit.name')"
                        :value="$tagCategory->name"
                    />
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
