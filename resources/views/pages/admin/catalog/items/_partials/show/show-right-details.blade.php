<div class="col-md-6">
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.identification_code') }}</h5>
        <div class="card-body">
            <p class="card-text">{{ $item->identification_code }}</p>
        </div>
    </div>
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.qrcode') }}</h5>
        <div class="card-body">
            @if ($qrCodeImage)
                <img
                    src="{{ $qrCodeImage->image_url }}"
                    class="img-thumbnail mb-2 js-admin-qrcode-image"
                    alt="{{ __('view.admin.catalog.items.show.qrcode') }}"
                    style="max-height: 180px;"
                >
            @else
                <p class="text-muted small mb-2">{{ __('view.admin.catalog.items.show.qrcode_missing') }}</p>
            @endif
            <div class="small text-break mb-2">
                <strong>{{ __('view.admin.catalog.items.show.qrcode_target_url') }}:</strong>
                <a href="{{ $qrCodeTargetUrl }}" target="_blank" rel="noopener noreferrer" class="js-admin-qrcode-target-url">{{ $qrCodeTargetUrl }}</a>
            </div>
            @if ($qrDomainInvalid ?? false)
                <div class="alert alert-warning py-2 px-3 small mb-2">
                    <i class="bi bi-exclamation-triangle-fill me-1" aria-hidden="true"></i>
                    {{ __('view.admin.catalog.items.show.qrcode_domain_invalid') }}
                </div>
            @endif
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm js-admin-qrcode-copy-link">
                    <i class="bi bi-link-45deg me-1" aria-hidden="true"></i>{{ __('view.admin.catalog.items.show.qrcode_copy_link') }}
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm js-admin-qrcode-copy-image" @disabled($qrCodeImage === null)>
                    <i class="bi bi-image me-1" aria-hidden="true"></i>{{ __('view.admin.catalog.items.show.qrcode_copy_image') }}
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm js-admin-qrcode-print" @disabled($qrCodeImage === null)>
                    <i class="bi bi-printer me-1" aria-hidden="true"></i>{{ __('view.admin.catalog.items.show.qrcode_print') }}
                </button>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <h5 class="card-header">{{ __('view.admin.catalog.items.show.location') }}</h5>
        <div class="card-body">
            @if ($item->location)
                <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
                <p class="ms-3">{{ $item->location->id }}</p>
                <strong>{{ __('view.admin.catalog.items.show.location_code') }}: </strong>
                <p class="ms-3">{{ $item->location->code }}</p>
                <strong>{{ __('view.admin.catalog.items.show.name') }}: </strong>
                <p class="card-text">{{ $item->location->localized_label }}</p>
            @else
                <p class="text-muted mb-0">—</p>
            @endif
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
            <strong>{{ __('view.admin.catalog.items.show.email') }}: </strong>
            <p class="ms-3">{{ $item->collaborator->email }}</p>
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

