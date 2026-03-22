<x-layouts.admin :title="__('view.admin.taxonomy.tags.show.title') . ' ' . $tag->id"
    :heading="__('view.admin.taxonomy.tags.show.heading', ['id' => $tag->id, 'name' => $tag->name])">
    <x-slot name="pageHeaderActions">
        <x-ui.buttons.edit href="{{ route('admin.taxonomy.tags.edit', $tag->id) }}" class="me-1" />
        <form action="{{ route('admin.taxonomy.tags.destroy', $tag->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ui.buttons.delete class="deleteTagButton"
                data-confirm-message="{{ __('view.admin.taxonomy.tags.index.delete_confirm') }}" />
        </form>
    </x-slot>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.taxonomy.tags.show.id') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $tag->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.taxonomy.tags.show.name') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $tag->name }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.taxonomy.tags.show.validated') }}</h5>
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($tag->validation == 1)
                                        {{ __('view.admin.taxonomy.tags.index.yes') }}
                                    @else
                                        {{ __('view.admin.taxonomy.tags.index.no') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.taxonomy.tags.show.created_at') }}</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tag->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.taxonomy.tags.show.updated_at') }}</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tag->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.taxonomy.tags.show.category') }}</h5>
                            <div class="card-body">
                                <strong>{{ __('view.admin.taxonomy.tags.show.id') }}: </strong>
                                <p class="ms-3">{{ $tag->tagCategory->id }}</p>
                                <strong>{{ __('view.admin.taxonomy.tags.show.name') }}: </strong>
                                <p class="card-text">{{ $tag->tagCategory->name }}</p>
                                <strong>{{ __('view.admin.taxonomy.tags.show.created_at') }}: </strong>
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tag->tagCategory->created_at)) }}</p>
                                <strong>{{ __('view.admin.taxonomy.tags.show.updated_at') }}: </strong>
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tag->tagCategory->updated_at)) }}</p>
                                <div class="d-flex">
                                    <x-ui.buttons.view href="{{ route('admin.taxonomy.tag-categories.show', $tag->tagCategory->id) }}"
                                        class="me-1" />
                                    <x-ui.buttons.edit href="{{ route('admin.taxonomy.tag-categories.edit', $tag->tagCategory->id) }}"
                                        class="me-1" />
                                    <form action="{{ route('admin.taxonomy.tag-categories.destroy', $tag->tagCategory->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.buttons.delete class="deleteCategoryButton"
                                            data-confirm-message="{{ __('view.admin.taxonomy.tag_categories.delete_confirm') }}" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</x-layouts.admin>
