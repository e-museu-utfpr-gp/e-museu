<x-layouts.admin :title="__('view.admin.collaborator.collaborators.show.title', ['id' => $collaborator->id])"
    :heading="__('view.admin.collaborator.collaborators.show.heading', ['id' => $collaborator->id, 'name' => $collaborator->full_name])">
    <x-slot name="pageHeaderActions">
        <x-ui.buttons.edit href="{{ route('admin.collaborators.edit', $collaborator->id) }}" class="me-1" />
        <form action="{{ route('admin.collaborators.destroy', $collaborator->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ui.buttons.delete class="deleteCollaboratorButton" />
        </form>
    </x-slot>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.collaborator.collaborators.show.id') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ $collaborator->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.collaborator.collaborators.show.full_name') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ $collaborator->full_name }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.collaborator.collaborators.show.contact') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ $collaborator->contact }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.collaborator.collaborators.show.role') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ __('app.collaborator.role.' . (optional($collaborator->role)?->value ?? \App\Enums\Collaborator\CollaboratorRole::EXTERNAL->value)) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.collaborator.collaborators.show.blocked') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($collaborator->blocked == 1)
                                        {{ __('view.admin.collaborator.collaborators.show.yes') }}
                                    @else
                                        {{ __('view.admin.collaborator.collaborators.show.no') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.collaborator.collaborators.show.created_at') }}
                            </h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($collaborator->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.collaborator.collaborators.show.updated_at') }}
                            </h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($collaborator->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <h5 class="card-header d-flex justify-content-between">
                        {{ __('view.admin.collaborator.collaborators.show.items_heading') }}
                    </h5>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($collaborator->items()->paginate(15) as $item)
                                <li class="list-group-item">
                                    <strong>{{ __('view.admin.collaborator.collaborators.show.item_id') }}: </strong>
                                    <p class="ms-3">{{ $item->id }}</p>
                                    <strong>{{ __('view.admin.collaborator.collaborators.show.item_name') }}: </strong>
                                    <p class="card-text">{{ $item->name }}</p>
                                    @if ($item->image_url)
                                        <img src="{{ $item->image_url }}" class="img-thumbnail clickable-image"
                                            alt="{{ __('view.admin.collaborator.collaborators.show.item_image_alt') }}"
                                            style="aspect-ratio: 3 / 2; width: 100%; max-height: 100%; object-fit: cover">
                                    @endif
                                    <strong>{{ __('view.admin.collaborator.collaborators.show.item_description') }}: </strong>
                                    <p class="ms-3">{{ $item->description }}</p>
                                    <strong>{{ __('view.admin.collaborator.collaborators.show.item_history') }}: </strong>
                                    <p class="card-text">{{ $item->history }}</p>
                                    <strong>{{ __('view.admin.collaborator.collaborators.show.item_detail') }}: </strong>
                                    <p class="ms-3">{!! nl2br($item->detail) !!}</p>
                                    <strong>{{ __('view.admin.collaborator.collaborators.show.item_date') }}: </strong>
                                    <p class="card-text">{{ date('d-m-Y', strtotime($item->date)) }}</p>
                                    <strong>{{ __('view.admin.collaborator.collaborators.show.item_code') }}: </strong>
                                    <p class="ms-3">{{ $item->identification_code }}</p>
                                    <strong>{{ __('view.admin.collaborator.collaborators.show.item_validated') }}: </strong>
                                    <p class="ms-3">
                                        @if ($item->validation == 1)
                                            {{ __('view.admin.collaborator.collaborators.show.yes') }}
                                        @else
                                            {{ __('view.admin.collaborator.collaborators.show.no') }}
                                        @endif
                                    </p>
                                    <strong>Categoria de Item: </strong>
                                    <p class="card-text">{{ $item->itemCategory?->name }}</p>
                                    <strong>Colaborador: </strong>
                                    <p class="card-text">{{ $item->collaborator->full_name }}</p>
                                    <strong>Criado em: </strong>
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($item->created_at)) }}</p>
                                    <strong>Atualizado em: </strong>
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($item->updated_at)) }}</p>
                                    <div class="d-flex">
                                        <x-ui.buttons.view href="{{ route('admin.catalog.items.show', $item->id) }}"
                                            class="me-1" />
                                        <x-ui.buttons.edit href="{{ route('admin.catalog.items.edit', $item->id) }}"
                                            class="me-1" />
                                        <form action="{{ route('admin.catalog.items.destroy', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.buttons.delete class="deleteItemButton"
                                                data-confirm-message="{{ __('view.admin.catalog.items.index.delete_confirm') }}" />
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                {{ $collaborator->items()->paginate(15)->links('pagination::bootstrap-5') }}
            </div>
        </div>

    <x-ui.image-modal />

</x-layouts.admin>
