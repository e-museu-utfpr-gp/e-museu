<x-layouts.admin :title="__('view.admin.catalog.items.show.title') . ' ' . $item->
    id" :heading="__('view.admin.catalog.items.show.heading', ['id' => $item->id, 'name' => $item->name])">
    <x-slot name="pageHeaderActions">
        <x-ui.buttons.edit href="{{ route('admin.catalog.items.edit', $item->id) }}" class="me-1" />
        <form action="{{ route('admin.catalog.items.destroy', $item->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ui.buttons.delete id="deleteItemButton" class="deleteItemButton"
                data-confirm-message="{{ __('view.admin.catalog.items.show.delete_confirm') }}" />
        </form>
    </x-slot>
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.items.show.id') }}</h5>
                                <div class="card-body">
                                    <p class="card-text">{{ $item->id }}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.items.show.name') }}</h5>
                                <div class="card-body">
                                    <p class="card-text">{{ $item->name }}</p>
                                </div>
                            </div>
                            @php
                                $sortedForShow = $item->images->sortBy('sort_order')->values();
                                $coverImage = $sortedForShow->first(fn ($img) => $img->type->value === 'cover') ?? $sortedForShow->first();
                                $galleryImages = $coverImage ? $sortedForShow->filter(fn ($img) => $img->id !== $coverImage->id) : $sortedForShow;
                            @endphp
                            @if ($coverImage)
                                <div class="card mb-3">
                                    <h5 class="card-header">{{ __('app.catalog.item_image.cover') }}</h5>
                                    <div class="card-body">
                                        <img src="{{ $coverImage->image_url }}" class="img-thumbnail clickable-image myImg" alt=""
                                            style="max-height: 200px; max-width: 100%; object-fit: contain;">
                                    </div>
                                </div>
                            @endif
                            @if ($galleryImages->isNotEmpty())
                                <div class="card mb-3">
                                    <h5 class="card-header">{{ __('app.catalog.item_image.gallery') }}</h5>
                                    <div class="card-body d-flex flex-wrap gap-2">
                                        @foreach ($galleryImages as $img)
                                            <div class="position-relative">
                                                <img src="{{ $img->image_url }}" class="img-thumbnail clickable-image myImg" alt=""
                                                    style="max-height: 120px;">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if ($item->images->isEmpty())
                                <div class="card mb-3">
                                    <h5 class="card-header">{{ __('view.admin.catalog.items.show.image') }}</h5>
                                    <div class="card-body">
                                        <p class="text-muted mb-0">{{ __('view.admin.catalog.items.show.no_images') }}</p>
                                    </div>
                                </div>
                            @endif
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.items.show.description') }}</h5>
                                <div class="card-body">
                                    <p class="card-text">{{ $item->description }}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.items.show.detail') }}</h5>
                                <div class="card-body">
                                    <p class="card-text">{!! nl2br($item->detail) !!}</p>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.items.show.date') }}</h5>
                                <div class="card-body">
                                    <p class="card-text">{{ $item->date ? date('d-m-Y', strtotime($item->date)) : '—' }}</p>
                                </div>
                            </div>
                        </div>
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
                                        <x-ui.buttons.view href="{{ route('admin.catalog.item-categories.show', $item->itemCategory?->id) }}"
                                            class="me-1" />
                                        <x-ui.buttons.edit href="{{ route('admin.catalog.item-categories.edit', $item->itemCategory?->id) }}"
                                            class="me-1" />
                                        <form action="{{ route('admin.catalog.item-categories.destroy', $item->itemCategory?->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.buttons.delete id="deleteItemCategoryButton" class="deleteItemCategoryButton"
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
                                        <x-ui.buttons.view href="{{ route('admin.collaborators.show', $item->collaborator->id) }}"
                                            class="me-1" />
                                        <x-ui.buttons.edit href="{{ route('admin.collaborators.edit', $item->collaborator->id) }}"
                                            class="me-1" />
                                        <form action="{{ route('admin.collaborators.destroy', $item->collaborator->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.buttons.delete id="deleteCollaboratorButton" class="deleteCollaboratorButton" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                                <p class="ms-3">{{ $extra->item->name }}</p>
                                                <strong>{{ __('view.admin.catalog.items.show.collaborator') }}: </strong>
                                                <p class="ms-3">{{ $extra->collaborator->full_name }}</p>
                                                <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
                                                <p class="ms-3">{{ date('d-m-Y', strtotime($extra->created_at)) }}</p>
                                                <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                                                <p class="ms-3">{{ date('d-m-Y', strtotime($extra->updated_at)) }}</p>
                                                <div class="d-flex">
                                                    <x-ui.buttons.view href="{{ route('admin.catalog.extras.show', $extra->id) }}"
                                                        class="me-1" />
                                                    <x-ui.buttons.edit href="{{ route('admin.catalog.extras.edit', $extra->id) }}"
                                                        class="me-1" />
                                                    <form action="{{ route('admin.catalog.extras.destroy', $extra->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-ui.buttons.delete class="deleteExtraButton"
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
                                                <p class="ms-3">{{ $tagItem->item->name }}</p>
                                                <strong>{{ __('view.admin.catalog.items.show.tag_label') }}: </strong>
                                                <p class="ms-3">{{ $tagItem->Tag->name }}</p>
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
                                                    <x-ui.buttons.view href="{{ route('admin.catalog.item-tags.show', $tagItem->id) }}"
                                                        class="me-1" />
                                                    <form action="{{ route('admin.catalog.item-tags.update', $tagItem->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <x-ui.buttons.validate-invalidate class="me-1" />
                                                    </form>
                                                    <form action="{{ route('admin.catalog.item-tags.destroy', $tagItem->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-ui.buttons.delete class="deleteItemTagButton"
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
                                                <p class="ms-3">{{ $itemComponent->item->name }}</p>
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
                                                <p class="ms-3">
                                                    {{ date('d-m-Y H:i:s', strtotime($itemComponent->created_at)) }}</p>
                                                <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                                                <p class="ms-3">
                                                    {{ date('d-m-Y H:i:s', strtotime($itemComponent->updated_at)) }}</p>
                                                <div class="d-flex">
                                                    <x-ui.buttons.view href="{{ route('admin.catalog.item-components.show', $itemComponent->id) }}"
                                                        class="me-1" />
                                                    <form action="{{ route('admin.catalog.item-components.update', $itemComponent->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <x-ui.buttons.validate-invalidate class="me-1" />
                                                    </form>
                                                    <form action="{{ route('admin.catalog.item-components.destroy', $itemComponent->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-ui.buttons.delete class="deleteComponentButton"
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
            </div>

        <x-ui.image-modal />
</x-layouts.admin>
