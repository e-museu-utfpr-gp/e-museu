<x-layouts.admin :title="__('view.admin.catalog.components.show.title') . ' ' . $itemComponent->
    id" :heading="__('view.admin.catalog.components.show.heading', ['id' => $itemComponent->id])">
    <x-slot name="pageHeaderActions">
        <form action="{{ route('admin.catalog.item-components.update', $itemComponent->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <x-ui.buttons.admin.validate-invalidate class="me-1" data-toggle="tooltip" data-placement="top"
                title="{{ __('view.admin.catalog.components.index.validate_tooltip') }}" />
        </form>
        <form action="{{ route('admin.catalog.item-components.destroy', $itemComponent->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ui.buttons.admin.delete class="deleteComponentButton"
                data-confirm-message="{{ __('view.admin.catalog.components.index.delete_confirm') }}" />
        </form>
    </x-slot>
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.components.show.id') }}</h5>
                                <div class="card-body">
                                    <p class="card-text">{{ $itemComponent->id }}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.components.show.validated') }}</h5>
                                <div class="card-body">
                                    <p class="card-text">
                                        @if ($itemComponent->validation == 1)
                                            {{ __('view.admin.catalog.components.index.yes') }}
                                        @else
                                            {{ __('view.admin.catalog.components.index.no') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.components.show.created_at') }}</h5>
                                <div class="card-body">
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemComponent->created_at)) }}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.components.show.updated_at') }}</h5>
                                <div class="card-body">
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemComponent->updated_at)) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <h5 class="card-header">{{ __('view.admin.catalog.components.show.main_item') }}</h5>
                        <div class="card-body">
                            <strong>{{ __('view.admin.catalog.components.show.id') }}: </strong>
                            <p class="ms-3">{{ $itemComponent->item->id }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.name') }}: </strong>
                            <p class="card-text">{{ $itemComponent->item->name }}</p>
                            @if ($itemComponent->item->image_url)
                                <img src="{{ $itemComponent->item->image_url }}" class="img-thumbnail clickable-image"
                                    alt="{{ __('view.admin.catalog.components.show.image_alt_item') }}"
                                    style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover">
                            @endif
                            <strong>{{ __('view.admin.catalog.components.show.description') }}: </strong>
                            <p class="ms-3">{{ $itemComponent->item->description }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.history') }}: </strong>
                            <p class="card-text">{{ $itemComponent->item->history }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.detail') }}: </strong>
                            <p class="ms-3">{!! nl2br($itemComponent->item->detail) !!}</p>
                            <strong>{{ __('view.admin.catalog.components.show.date') }}: </strong>
                            <p class="card-text">{{ $itemComponent->item->date ? date('d-m-Y', strtotime($itemComponent->item->date)) : '—' }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.identification_code') }}: </strong>
                            <p class="ms-3">{{ $itemComponent->item->identification_code }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.validated') }}: </strong>
                            <p class="ms-3">
                                @if ($itemComponent->item->validation == 1)
                                    {{ __('view.admin.catalog.components.index.yes') }}
                                @else
                                    {{ __('view.admin.catalog.components.index.no') }}
                                @endif
                            </p>
                            <strong>{{ __('view.admin.catalog.components.show.item_category') }}: </strong>
                            <p class="card-text">{{ $itemComponent->item->itemCategory?->name }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.collaborator') }}: </strong>
                            <p class="card-text">{{ $itemComponent->item->collaborator?->full_name }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.created_at') }}: </strong>
                            <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemComponent->item->created_at)) }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.updated_at') }}: </strong>
                            <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemComponent->item->updated_at)) }}</p>
                            <div class="d-flex">
                                <x-ui.buttons.admin.view href="{{ route('admin.catalog.items.show', $itemComponent->item->id) }}"
                                    class="me-1" />
                                <x-ui.buttons.admin.edit href="{{ route('admin.catalog.items.edit', $itemComponent->item->id) }}"
                                    class="me-1" />
                                <form action="{{ route('admin.catalog.items.destroy', $itemComponent->item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.buttons.admin.delete class="deleteItemButton"
                                        data-confirm-message="{{ __('view.admin.catalog.items.index.delete_confirm') }}" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card mb-3">
                        <h5 class="card-header">{{ __('view.admin.catalog.components.show.component') }}</h5>
                        <div class="card-body">
                            <strong>{{ __('view.admin.catalog.components.show.id') }}: </strong>
                            <p class="ms-3">{{ $itemComponent->component->id }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.name') }}: </strong>
                            <p class="card-text">{{ $itemComponent->component->name }}</p>
                            @if ($itemComponent->component->image_url)
                                <img src="{{ $itemComponent->component->image_url }}"
                                    class="img-thumbnail clickable-image" alt="{{ __('view.admin.catalog.components.show.image_alt_component') }}"
                                    style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover">
                            @endif
                            <strong>{{ __('view.admin.catalog.components.show.description') }}: </strong>
                            <p class="ms-3">{{ $itemComponent->component->description }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.history') }}: </strong>
                            <p class="card-text">{{ $itemComponent->component->history }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.detail') }}: </strong>
                            <p class="ms-3">{!! nl2br($itemComponent->component->detail) !!}</p>
                            <strong>{{ __('view.admin.catalog.components.show.date') }}: </strong>
                            <p class="card-text">{{ $itemComponent->component->date ? date('d-m-Y', strtotime($itemComponent->component->date)) : '—' }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.identification_code') }}: </strong>
                            <p class="ms-3">{{ $itemComponent->component->identification_code }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.validated') }}: </strong>
                            <p class="ms-3">
                                @if ($itemComponent->component->validation == 1)
                                    {{ __('view.admin.catalog.components.index.yes') }}
                                @else
                                    {{ __('view.admin.catalog.components.index.no') }}
                                @endif
                            </p>
                            <strong>{{ __('view.admin.catalog.components.show.item_category') }}: </strong>
                            <p class="card-text">{{ $itemComponent->component->itemCategory?->name }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.collaborator') }}: </strong>
                            <p class="card-text">{{ $itemComponent->component->collaborator?->full_name }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.created_at') }}: </strong>
                            <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemComponent->component->created_at)) }}</p>
                            <strong>{{ __('view.admin.catalog.components.show.updated_at') }}: </strong>
                            <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemComponent->component->updated_at)) }}</p>
                            <div class="d-flex">
                                <x-ui.buttons.admin.view href="{{ route('admin.catalog.items.show', $itemComponent->component->id) }}"
                                    class="me-1" />
                                <x-ui.buttons.admin.edit href="{{ route('admin.catalog.items.edit', $itemComponent->component->id) }}"
                                    class="me-1" />
                                <form action="{{ route('admin.catalog.items.destroy', $itemComponent->component->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.buttons.admin.delete class="deleteItemButton"
                                        data-confirm-message="{{ __('view.admin.catalog.items.index.delete_confirm') }}" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <x-ui.image-modal />
</x-layouts.admin>
