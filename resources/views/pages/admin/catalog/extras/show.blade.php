<x-layouts.admin :title="__('view.admin.catalog.extras.show.title') . ' ' . $extra->
    id" :heading="__('view.admin.catalog.extras.show.heading', ['id' => $extra->id])">
    <x-slot name="pageHeaderActions">
        <x-ui.buttons.admin.edit href="{{ route('admin.catalog.extras.edit', $extra->id) }}" class="me-1" />
        <form action="{{ route('admin.catalog.extras.destroy', $extra->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ui.buttons.admin.delete class="deleteExtraButton"
                data-confirm-message="{{ __('view.admin.catalog.extras.show.delete_confirm') }}" />
        </form>
    </x-slot>
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.extras.show.id') }}</h5>
                                <div class="card-body">
                                    <p class="card-text">{{ $extra->id }}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.extras.show.curiosity') }}</h5>
                                <div class="card-body">
                                    <p class="card-text">{{ $extra->info }}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.extras.show.validated') }}</h5>
                                <div class="card-body">
                                    <p class="card-text">
                                        @if ($extra->validation == 1)
                                            {{ __('view.admin.catalog.extras.show.yes') }}
                                        @else
                                            {{ __('view.admin.catalog.extras.show.no') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.extras.show.created_at') }}</h5>
                                <div class="card-body">
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($extra->created_at)) }}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.extras.show.updated_at') }}</h5>
                                <div class="card-body">
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($extra->updated_at)) }}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.extras.show.collaborator') }}</h5>
                                <div class="card-body">
                                    <strong>{{ __('view.admin.catalog.extras.show.id') }}: </strong>
                                    <p class="ms-3">{{ $extra->collaborator->id }}</p>
                                    <strong>{{ __('view.admin.catalog.extras.show.full_name') }}: </strong>
                                    <p class="ms-3">{{ $extra->collaborator->full_name }}</p>
                                    <strong>{{ __('view.admin.catalog.extras.show.contact') }}: </strong>
                                    <p class="ms-3">{{ $extra->collaborator->contact }}</p>
                                    <strong>{{ __('view.admin.catalog.extras.show.blocked') }}: </strong>
                                    <p class="ms-3">
                                        @if ($extra->collaborator->blocked == 1)
                                            {{ __('view.admin.catalog.extras.show.yes') }}
                                        @else
                                            {{ __('view.admin.catalog.extras.show.no') }}
                                        @endif
                                    </p>
                                    <strong>{{ __('view.admin.catalog.extras.show.created_at') }}: </strong>
                                    <p class="ms-3">{{ date('d-m-Y H:i:s', strtotime($extra->collaborator->created_at)) }}</p>
                                    <strong>{{ __('view.admin.catalog.extras.show.updated_at') }}: </strong>
                                    <p class="ms-3">{{ date('d-m-Y H:i:s', strtotime($extra->collaborator->updated_at)) }}</p>
                                    <div class="d-flex">
                                        <x-ui.buttons.admin.view href="{{ route('admin.collaborators.show', $extra->collaborator->id) }}"
                                            class="me-1" />
                                        <x-ui.buttons.admin.edit href="{{ route('admin.collaborators.edit', $extra->collaborator->id) }}"
                                            class="me-1" />
                                        <form action="{{ route('admin.collaborators.destroy', $extra->collaborator->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.buttons.admin.delete class="deleteCollaboratorButton" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h5 class="card-header">{{ __('view.admin.catalog.extras.show.item') }}</h5>
                        <div class="card-body">
                            @if ($extra->item)
                            <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
                            <p class="ms-3">{{ $extra->item->id }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.name') }}: </strong>
                            <p class="card-text">{{ $extra->item->name }}</p>
                            @if ($extra->item->image_url)
                                <img src="{{ $extra->item->image_url }}" class="img-thumbnail clickable-image"
                                    alt="{{ __('view.admin.catalog.items.show.image') }}"
                                    style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover">
                            @endif
                            <strong>{{ __('view.admin.catalog.items.show.description') }}: </strong>
                            <p class="ms-3">{{ $extra->item->description }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.history') }}: </strong>
                            <p class="card-text">{{ $extra->item->history }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.detail') }}: </strong>
                            <p class="ms-3">{!! nl2br($extra->item->detail) !!}</p>
                            <strong>{{ __('view.admin.catalog.items.show.date') }}: </strong>
                            <p class="card-text">{{ $extra->item->date ? date('d-m-Y', strtotime($extra->item->date)) : '—' }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.identification_code') }}: </strong>
                            <p class="ms-3">{{ $extra->item->identification_code }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.validated') }}: </strong>
                            <p class="ms-3">
                                @if ($extra->item->validation == 1)
                                    {{ __('view.admin.catalog.items.show.yes') }}
                                @else
                                    {{ __('view.admin.catalog.items.show.no') }}
                                @endif
                            </p>
                            <strong>{{ __('view.admin.catalog.items.show.item_category') }}: </strong>
                            <p class="card-text">{{ $extra->item->itemCategory?->name }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.collaborator') }}: </strong>
                            <p class="card-text">{{ $extra->item->collaborator?->full_name ?? '—' }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
                            <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($extra->item->created_at)) }}</p>
                            <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                            <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($extra->item->updated_at)) }}</p>
                            <div class="d-flex">
                                <x-ui.buttons.admin.view href="{{ route('admin.catalog.items.show', $extra->item->id) }}"
                                    class="me-1" />
                                <x-ui.buttons.admin.edit href="{{ route('admin.catalog.items.edit', $extra->item->id) }}"
                                    class="me-1" />
                                <form action="{{ route('admin.catalog.items.destroy', $extra->item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.buttons.admin.delete class="deleteItemButton"
                                        data-confirm-message="{{ __('view.admin.catalog.items.show.delete_confirm') }}" />
                                </form>
                            </div>
                            @else
                            <p class="card-text text-muted">—</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        <x-ui.image-modal />
</x-layouts.admin>
