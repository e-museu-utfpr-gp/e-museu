<div class="col-md-6">
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.history') }}</h5>
        <div class="card-body">
            <p>{{ $item->history }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <h5 class="card-header d-flex justify-content-between">{{ __('view.admin.catalog.items.show.extra_info') }}
                    <x-ui.buttons.default variant="success"
                        href="{{ route('admin.catalog.extras.create', ['id' => $item->id, 'item_category' => $item->itemCategory?->id]) }}"
                        icon="bi bi-plus-circle">{{ __('view.admin.catalog.items.show.add_extra') }}</x-ui.buttons.default></h5>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($item->extras as $extra)
                            <li class="list-group-item">
                                <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
                                <p class="ms-3">{{ $extra->id }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.curiosity') }}: </strong>
                                <p class="ms-3">{{ Str::limit($extra->info, 500) }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.validated_label') }}: </strong>
                                <p class="ms-3">
                                    @if ($extra->validation == 1)
                                        {{ __('view.admin.catalog.items.show.yes') }}
                                    @else
                                        {{ __('view.admin.catalog.items.show.no') }}
                                    @endif
                                </p>
                                <strong>{{ __('view.admin.catalog.items.show.item_label') }}: </strong>
                                <p class="ms-3">{{ $item->name }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.collaborator') }}: </strong>
                                <p class="ms-3">{{ $extra->collaborator->full_name }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
                                <p class="ms-3">{{ date('d-m-Y', strtotime($extra->created_at)) }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                                <p class="ms-3">{{ date('d-m-Y', strtotime($extra->updated_at)) }}</p>
                                <div class="d-flex">
                                    <x-ui.buttons.admin.view href="{{ route('admin.catalog.extras.show', $extra->id) }}"
                                        class="me-1" />
                                    <x-ui.buttons.admin.edit href="{{ route('admin.catalog.extras.edit', $extra->id) }}"
                                        class="me-1" />
                                    <form action="{{ route('admin.catalog.extras.destroy', $extra->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.buttons.admin.delete class="deleteExtraButton"
                                            data-confirm-message="{{ __('view.admin.catalog.extras.index.delete_confirm') }}" />
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <h5 class="card-header d-flex justify-content-between">{{ __('view.admin.catalog.items.show.related_tags') }}
                    <x-ui.buttons.default variant="success"
                        href="{{ route('admin.catalog.item-tags.create', ['id' => $item->id, 'item_category' => $item->itemCategory?->id]) }}"
                        icon="bi bi-plus-circle">{{ __('view.admin.catalog.items.show.add_tag') }}</x-ui.buttons.default></h5>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($item->itemTags as $tagItem)
                            <li class="list-group-item">
                                <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
                                <p class="ms-3">{{ $tagItem->id }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.item_label') }}: </strong>
                                <p class="ms-3">{{ $item->name }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.tag_label') }}: </strong>
                                <p class="ms-3">{{ $tagItem->tag->name }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.validated') }}: </strong>
                                <p class="ms-3">
                                    @if ($tagItem->validation == 1)
                                        {{ __('view.admin.catalog.items.show.yes') }}
                                    @else
                                        {{ __('view.admin.catalog.items.show.no') }}
                                    @endif
                                </p>
                                <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
                                <p class="ms-3">{{ date('d-m-Y H:i:s', strtotime($tagItem->created_at)) }}
                                </p>
                                <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                                <p class="ms-3">{{ date('d-m-Y H:i:s', strtotime($tagItem->updated_at)) }}
                                </p>
                                <div class="d-flex">
                                    <x-ui.buttons.admin.view href="{{ route('admin.catalog.item-tags.show', $tagItem->id) }}"
                                        class="me-1" />
                                    <form action="{{ route('admin.catalog.item-tags.update', $tagItem->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <x-ui.buttons.admin.validate-invalidate class="me-1" />
                                    </form>
                                    <form action="{{ route('admin.catalog.item-tags.destroy', $tagItem->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.buttons.admin.delete class="deleteItemTagButton"
                                            data-confirm-message="{{ __('view.admin.catalog.item_tags.index.delete_confirm') }}" />
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <h5 class="card-header d-flex justify-content-between">{{ __('view.admin.catalog.items.show.related_components') }}
                    <x-ui.buttons.default variant="success"
                        href="{{ route('admin.catalog.item-components.create', ['id' => $item->id, 'item_category' => $item->itemCategory?->id]) }}"
                        icon="bi bi-plus-circle">{{ __('view.admin.catalog.items.show.add_component') }}</x-ui.buttons.default></h5>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($item->itemComponents as $itemComponent)
                            <li class="list-group-item">
                                <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
                                <p class="ms-3">{{ $itemComponent->id }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.main_item') }}: </strong>
                                <p class="ms-3">{{ $item->name }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.component_label') }}: </strong>
                                <p class="ms-3">{{ $itemComponent->component->name }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.validated') }}: </strong>
                                <p class="ms-3">
                                    @if ($itemComponent->validation == 1)
                                        {{ __('view.admin.catalog.items.show.yes') }}
                                    @else
                                        {{ __('view.admin.catalog.items.show.no') }}
                                    @endif
                                </p>
                                <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
                                <p class="ms-3">{{ date('d-m-Y H:i:s', strtotime($itemComponent->created_at)) }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                                <p class="ms-3">{{ date('d-m-Y H:i:s', strtotime($itemComponent->updated_at)) }}</p>
                                <div class="d-flex">
                                    <x-ui.buttons.admin.view href="{{ route('admin.catalog.item-components.show', $itemComponent->id) }}"
                                        class="me-1" />
                                    <form action="{{ route('admin.catalog.item-components.update', $itemComponent->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <x-ui.buttons.admin.validate-invalidate class="me-1" />
                                    </form>
                                    <form action="{{ route('admin.catalog.item-components.destroy', $itemComponent->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.buttons.admin.delete class="deleteComponentButton"
                                            data-confirm-message="{{ __('view.admin.catalog.components.index.delete_confirm') }}" />
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

