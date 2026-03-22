<x-layouts.admin :title="__('view.admin.taxonomy.tag_categories.show.title', ['id' => $tagCategory->id])"
    :heading="__('view.admin.taxonomy.tag_categories.show.heading', ['id' => $tagCategory->id, 'name' => $tagCategory->name])">
    <x-slot name="pageHeaderActions">
        <x-ui.buttons.edit href="{{ route('admin.taxonomy.tag-categories.edit', $tagCategory->id) }}"
            class="me-1" />
        <form action="{{ route('admin.taxonomy.tag-categories.destroy', $tagCategory->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ui.buttons.delete class="deleteCategoryButton"
                data-confirm-message="{{ __('view.admin.taxonomy.tag_categories.delete_confirm') }}" />
        </form>
    </x-slot>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.taxonomy.tag_categories.show.id') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ $tagCategory->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.taxonomy.tag_categories.show.name') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ $tagCategory->name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.taxonomy.tag_categories.show.created_at') }}
                            </h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tagCategory->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.taxonomy.tag_categories.show.updated_at') }}
                            </h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tagCategory->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</x-layouts.admin>
