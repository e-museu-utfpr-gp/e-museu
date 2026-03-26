<x-layouts.admin :title="__('view.admin.catalog.item_tags.show.title') . ' ' . $itemTag->
    tag->id . '-' . $itemTag->item->id" :heading="__('view.admin.catalog.item_tags.show.heading', ['id' => $itemTag->id])">
    <x-slot name="pageHeaderActions">
        <form action="{{ route('admin.catalog.item-tags.update', $itemTag->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <x-ui.buttons.admin.validate-invalidate class="me-1" data-toggle="tooltip" data-placement="top"
                title="{{ __('view.admin.catalog.item_tags.show.validate_invalidate_tooltip') }}" />
        </form>
        <form action="{{ route('admin.catalog.item-tags.destroy', $itemTag->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ui.buttons.admin.delete class="deleteItemTagButton"
                data-confirm-message="{{ __('view.admin.catalog.item_tags.show.delete_confirm') }}" />
        </form>
    </x-slot>
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.item_tags.show.id') }}</h5>
                                <div class="card-body">
                                    <p class="card-text">{{ $itemTag->id }}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.item_tags.show.validated') }}</h5>
                                <div class="card-body">
                                    <p class="card-text">
                                        @if ($itemTag->validation == 1)
                                            {{ __('view.admin.catalog.item_tags.show.yes') }}
                                        @else
                                            {{ __('view.admin.catalog.item_tags.show.no') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.item_tags.show.created_at') }}</h5>
                                <div class="card-body">
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemTag->created_at)) }}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.item_tags.show.updated_at') }}</h5>
                                <div class="card-body">
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemTag->updated_at)) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.item_tags.show.tag') }}</h5>
                                <div class="card-body">
                                    <strong>{{ __('view.admin.catalog.item_tags.show.id') }}: </strong>
                                    <p class="ms-3">{{ $itemTag->tag->id }}</p>
                                    <strong>{{ __('view.admin.catalog.item_tags.show.name') }}: </strong>
                                    <p class="card-text">{{ $itemTag->tag->name }}</p>
                                    <strong>{{ __('view.admin.catalog.item_tags.show.validated') }}: </strong>
                                    <p class="ms-3">
                                        @if ($itemTag->tag->validation == 1)
                                            {{ __('view.admin.catalog.item_tags.show.yes') }}
                                        @else
                                            {{ __('view.admin.catalog.item_tags.show.no') }}
                                        @endif
                                    </p>
                                    <strong>{{ __('view.admin.catalog.item_tags.show.created_at') }}: </strong>
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemTag->tag->created_at)) }}</p>
                                    <strong>{{ __('view.admin.catalog.item_tags.show.updated_at') }}: </strong>
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemTag->tag->updated_at)) }}</p>
                                    <div class="d-flex">
                                        <x-ui.buttons.admin.view href="{{ route('admin.taxonomy.tags.show', $itemTag->tag->id) }}"
                                            class="me-1" />
                                        <x-ui.buttons.admin.edit href="{{ route('admin.taxonomy.tags.edit', $itemTag->tag->id) }}"
                                            class="me-1" />
                                        <form action="{{ route('admin.taxonomy.tags.destroy', $itemTag->tag->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.buttons.admin.delete class="deleteTagButton" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h5 class="card-header">{{ __('view.admin.catalog.item_tags.show.item') }}</h5>
                        <div class="card-body">
                            <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
                            <p class="ms-3">{{ $itemTag->item->id }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.name') }}: </strong>
                            <p class="card-text">{{ $itemTag->item->name }}</p>
                            @if ($itemTag->item->image_url)
                                <img src="{{ $itemTag->item->image_url }}" class="img-thumbnail clickable-image"
                                    alt="{{ __('view.admin.catalog.items.show.image') }}"
                                    style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover">
                            @endif
                            <strong>{{ __('view.admin.catalog.items.show.description') }}: </strong>
                            <p class="ms-3">{{ $itemTag->item->description }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.history') }}: </strong>
                            <p class="card-text">{{ $itemTag->item->history }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.detail') }}: </strong>
                            <p class="ms-3">{!! nl2br($itemTag->item->detail) !!}</p>
                            <strong>{{ __('view.admin.catalog.items.show.date') }}: </strong>
                            <p class="card-text">{{ $itemTag->item->date ? date('d-m-Y', strtotime($itemTag->item->date)) : '—' }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.identification_code') }}: </strong>
                            <p class="ms-3">{{ $itemTag->item->identification_code }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.validated') }}: </strong>
                            <p class="ms-3">
                                @if ($itemTag->item->validation == 1)
                                    {{ __('view.admin.catalog.items.show.yes') }}
                                @else
                                    {{ __('view.admin.catalog.items.show.no') }}
                                @endif
                            </p>
                            <strong>{{ __('view.admin.catalog.items.show.item_category') }}: </strong>
                            <p class="card-text">{{ $itemTag->item->itemCategory?->name }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.collaborator') }}: </strong>
                            <p class="card-text">{{ $itemTag->item->collaborator?->full_name }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
                            <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemTag->item->created_at)) }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                            <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemTag->item->updated_at)) }}</p>
                            <div class="d-flex">
                                <x-ui.buttons.admin.view href="{{ route('admin.catalog.items.show', $itemTag->item->id) }}"
                                    class="me-1" />
                                <x-ui.buttons.admin.edit href="{{ route('admin.catalog.items.edit', $itemTag->item->id) }}"
                                    class="me-1" />
                                <form action="{{ route('admin.catalog.items.destroy', $itemTag->item->id) }}" method="POST">
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
