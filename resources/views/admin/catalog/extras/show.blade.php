@extends('layouts.admin')
@section('title', __('view.admin.catalog.extras.show.title') . ' ' . $extra->id)

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
                    <h2 class="card-header">{{ __('view.admin.catalog.extras.show.heading', ['id' => $extra->id]) }}</h2>
                    <div class="card-body d-flex">
                        <a href="{{ route('admin.extras.edit', $extra->id) }}" type="button" class="btn btn-warning me-1"><i
                                class="bi bi-pencil-fill"></i> {{ __('view.admin.catalog.extras.show.edit') }}</a>
                        <form action="{{ route('admin.extras.destroy', $extra->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteExtraButton btn btn-danger" data-confirm-message="{{ __('view.admin.catalog.extras.show.delete_confirm') }}"><i class="bi bi-trash-fill"></i>
                                {{ __('view.admin.catalog.extras.show.delete') }}
                        </form>
                    </div>
                </div>
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
                                    <a href="{{ route('admin.collaborators.show', $extra->collaborator->id) }}"
                                        type="button" class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i>
                                        {{ __('view.admin.catalog.extras.show.view') }}</a>
                                    <a href="{{ route('admin.collaborators.edit', $extra->collaborator->id) }}"
                                        type="button" class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i>
                                        {{ __('view.admin.catalog.extras.show.edit') }}</a>
                                    <form action="{{ route('admin.collaborators.destroy', $extra->collaborator->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="deleteCollaboratorButton btn btn-danger" type="submit"><i
                                                class="bi bi-trash-fill"></i> {{ __('view.admin.catalog.extras.show.delete') }}
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
                        <p class="card-text">{{ date('d-m-Y', strtotime($extra->item->date)) }}</p>
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
                        <p class="card-text">{{ $extra->item->category?->name }}</p>
                        <strong>{{ __('view.admin.catalog.items.show.collaborator') }}: </strong>
                        <p class="card-text">{{ $extra->item->proprietary->name }}</p>
                        <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($extra->item->created_at)) }}</p>
                        <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($extra->item->updated_at)) }}</p>
                        <div class="d-flex">
                            <a href="{{ route('admin.items.show', $extra->item->id) }}" type="button"
                                class="btn btn-primary me-1">
                                <i class="bi bi-eye-fill"></i>
                                {{ __('view.admin.catalog.items.show.view') }}
                            </a>
                            <a href="{{ route('admin.items.edit', $extra->item->id) }}" type="button"
                                class="btn btn-warning me-1">
                                <i class="bi bi-pencil-fill"></i>
                                {{ __('view.admin.catalog.items.show.edit') }}
                            </a>
                            <form action="{{ route('admin.items.destroy', $extra->item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="deleteExtraButton btn btn-danger" type="submit" data-confirm-message="{{ __('view.admin.catalog.extras.show.delete_confirm') }}"><i
                                        class="bi bi-trash-fill"></i> {{ __('view.admin.catalog.extras.show.delete') }}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('image-modal.img-modal')

@endsection
