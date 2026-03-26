<div class="col-md-6">
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.identification_code') }}</h5>
        <div class="card-body">
            <p class="card-text">{{ $item->identification_code }}</p>
        </div>
    </div>
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.validated') }}</h5>
        <div class="card-body">
            <p class="card-text">
                @if ($item->validation == 1)
                    {{ __('view.admin.catalog.items.show.yes') }}
                @else
                    {{ __('view.admin.catalog.items.show.no') }}
                @endif
            </p>
        </div>
    </div>
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.created_at') }}</h5>
        <div class="card-body">
            <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($item->created_at)) }}</p>
        </div>
    </div>
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.updated_at') }}</h5>
        <div class="card-body">
            <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($item->updated_at)) }}</p>
        </div>
    </div>
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.item_category') }}</h5>
        <div class="card-body">
            <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
            <p class="ms-3">{{ $item->itemCategory?->id }}</p>
            <strong>{{ __('view.admin.catalog.items.show.name') }}: </strong>
            <p class="card-text">{{ $item->itemCategory?->name }}</p>
            <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
            <p class="ms-2">{{ date('d-m-Y', strtotime($item->created_at)) }}</p>
            <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
            <p class="ms-2">{{ date('d-m-Y', strtotime($item->updated_at)) }}</p>

            <div class="d-flex">
                <x-ui.buttons.admin.view href="{{ route('admin.catalog.item-categories.show', $item->itemCategory?->id) }}"
                    class="me-1" />
                <x-ui.buttons.admin.edit href="{{ route('admin.catalog.item-categories.edit', $item->itemCategory?->id) }}"
                    class="me-1" />
                <form action="{{ route('admin.catalog.item-categories.destroy', $item->itemCategory?->id) }}"
                    method="POST">
                    @csrf
                    @method('DELETE')
                    <x-ui.buttons.admin.delete id="deleteItemCategoryButton" class="deleteItemCategoryButton"
                        data-confirm-message="{{ __('view.admin.catalog.item_categories.delete_confirm') }}" />
                </form>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.collaborator') }}</h5>
        <div class="card-body">
            <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
            <p class="ms-3">{{ $item->collaborator->id }}</p>
            <strong>{{ __('view.admin.catalog.items.show.full_name') }}: </strong>
            <p class="ms-3">{{ $item->collaborator->full_name }}</p>
            <strong>{{ __('view.admin.catalog.items.show.contact') }}: </strong>
            <p class="ms-3">{{ $item->collaborator->contact }}</p>
            <strong>{{ __('view.admin.catalog.items.show.blocked') }}: </strong>
            <p class="ms-3">
                @if ($item->collaborator->blocked == 1)
                    {{ __('view.admin.catalog.items.show.yes') }}
                @else
                    {{ __('view.admin.catalog.items.show.no') }}
                @endif
            </p>

            <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
            <p class="ms-3">{{ date('d-m-Y', strtotime($item->collaborator->created_at)) }}</p>
            <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
            <p class="ms-3">{{ date('d-m-Y', strtotime($item->collaborator->updated_at)) }}</p>

            <div class="d-flex">
                <x-ui.buttons.admin.view href="{{ route('admin.collaborators.show', $item->collaborator->id) }}"
                    class="me-1" />
                <x-ui.buttons.admin.edit href="{{ route('admin.collaborators.edit', $item->collaborator->id) }}"
                    class="me-1" />
                <form action="{{ route('admin.collaborators.destroy', $item->collaborator->id) }}"
                    method="POST">
                    @csrf
                    @method('DELETE')
                    <x-ui.buttons.admin.delete id="deleteCollaboratorButton" class="deleteCollaboratorButton" />
                </form>
            </div>
        </div>
    </div>
</div>

