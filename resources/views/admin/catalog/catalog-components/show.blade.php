@extends('layouts.admin')
@section('title', __('view.admin.catalog.components.show.title') . ' ' . $component->id)

@section('content')
    <div class="mb-auto container-fluid">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <h2 class="card-header">{{ __('view.admin.catalog.components.show.heading', ['id' => $component->id]) }}</h2>
                    <div class="card-body d-flex">
                        <form action="{{ route('admin.components.update', $component->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning me-1" data-toggle="tooltip" data-placement="top"
                                title="{{ __('view.admin.catalog.components.index.validate_tooltip') }}"><i class="bi bi-check2-circle h6"></i> {{ __('view.admin.catalog.components.show.validate_invalidate') }}</button>
                        </form>
                        <form action="{{ route('admin.components.destroy', $component->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteComponentButton btn btn-danger" data-confirm-message="{{ __('view.admin.catalog.components.index.delete_confirm') }}"><i
                                    class="bi bi-trash-fill"></i> {{ __('view.admin.catalog.components.show.delete') }}
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.components.show.id') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $component->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.components.show.validated') }}</h5>
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($component->validation == 1)
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
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($component->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.components.show.updated_at') }}</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($component->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <h5 class="card-header">{{ __('view.admin.catalog.components.show.main_item') }}</h5>
                    <div class="card-body">
                        <strong>{{ __('view.admin.catalog.components.show.id') }}: </strong>
                        <p class="ms-3">{{ $component->item->id }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.name') }}: </strong>
                        <p class="card-text">{{ $component->item->name }}</p>
                        @if ($component->item->image_url)
                            <img src="{{ $component->item->image_url }}" class="img-thumbnail clickable-image"
                                alt="{{ __('view.admin.catalog.components.show.image_alt_item') }}"
                                style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover">
                        @endif
                        <strong>{{ __('view.admin.catalog.components.show.description') }}: </strong>
                        <p class="ms-3">{{ $component->item->description }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.history') }}: </strong>
                        <p class="card-text">{{ $component->item->history }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.detail') }}: </strong>
                        <p class="ms-3">{!! nl2br($component->item->detail) !!}</p>
                        <strong>{{ __('view.admin.catalog.components.show.date') }}: </strong>
                        <p class="card-text">{{ date('d-m-Y', strtotime($component->item->date)) }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.identification_code') }}: </strong>
                        <p class="ms-3">{{ $component->item->identification_code }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.validated') }}: </strong>
                        <p class="ms-3">
                            @if ($component->item->validation == 1)
                                {{ __('view.admin.catalog.components.index.yes') }}
                            @else
                                {{ __('view.admin.catalog.components.index.no') }}
                            @endif
                        </p>
                        <strong>{{ __('view.admin.catalog.components.show.item_category') }}: </strong>
                        <p class="card-text">{{ $component->item->section->name }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.collaborator') }}: </strong>
                        <p class="card-text">{{ $component->item->proprietary->name }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.created_at') }}: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($component->item->created_at)) }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.updated_at') }}: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($component->item->updated_at)) }}</p>
                        <div class="d-flex">
                            <a href="{{ route('admin.items.show', $component->item->id) }}" type="button"
                                class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i> {{ __('view.admin.catalog.components.show.view') }}</a>
                            <a href="{{ route('admin.items.edit', $component->item->id) }}" type="button"
                                class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> {{ __('view.admin.catalog.components.show.edit') }}</a>
                            <form action="{{ route('admin.items.destroy', $component->item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="deleteItemButton btn btn-danger" type="submit" data-confirm-message="{{ __('view.admin.catalog.items.index.delete_confirm') }}"><i
                                        class="bi bi-trash-fill"></i> {{ __('view.admin.catalog.components.show.delete') }}
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
                        <p class="ms-3">{{ $component->component->id }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.name') }}: </strong>
                        <p class="card-text">{{ $component->component->name }}</p>
                        @if ($component->component->image_url)
                            <img src="{{ $component->component->image_url }}"
                                class="img-thumbnail clickable-image" alt="{{ __('view.admin.catalog.components.show.image_alt_component') }}"
                                style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover">
                        @endif
                        <strong>{{ __('view.admin.catalog.components.show.description') }}: </strong>
                        <p class="ms-3">{{ $component->component->description }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.history') }}: </strong>
                        <p class="card-text">{{ $component->component->history }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.detail') }}: </strong>
                        <p class="ms-3">{!! nl2br($component->component->detail) !!}</p>
                        <strong>{{ __('view.admin.catalog.components.show.date') }}: </strong>
                        <p class="card-text">{{ date('d-m-Y', strtotime($component->component->date)) }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.identification_code') }}: </strong>
                        <p class="ms-3">{{ $component->component->identification_code }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.validated') }}: </strong>
                        <p class="ms-3">
                            @if ($component->component->validation == 1)
                                {{ __('view.admin.catalog.components.index.yes') }}
                            @else
                                {{ __('view.admin.catalog.components.index.no') }}
                            @endif
                        </p>
                        <strong>{{ __('view.admin.catalog.components.show.item_category') }}: </strong>
                        <p class="card-text">{{ $component->component->section->name }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.collaborator') }}: </strong>
                        <p class="card-text">{{ $component->component->proprietary->name }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.created_at') }}: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($component->component->created_at)) }}</p>
                        <strong>{{ __('view.admin.catalog.components.show.updated_at') }}: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($component->component->updated_at)) }}</p>
                        <div class="d-flex">
                            <a href="{{ route('admin.items.show', $component->component->id) }}" type="button"
                                class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i> {{ __('view.admin.catalog.components.show.view') }}</a>
                            <a href="{{ route('admin.items.edit', $component->component->id) }}" type="button"
                                class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> {{ __('view.admin.catalog.components.show.edit') }}</a>
                            <form action="{{ route('admin.items.destroy', $component->component->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="deleteItemButton btn btn-danger" type="submit" data-confirm-message="{{ __('view.admin.catalog.items.index.delete_confirm') }}"><i
                                        class="bi bi-trash-fill"></i> {{ __('view.admin.catalog.components.show.delete') }}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('image-modal.img-modal')

@endsection
