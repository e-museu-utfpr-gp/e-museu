@extends('layouts.admin')
@section('title', __('view.admin.collaborator.collaborators.show.title', ['id' => $collaborator->id]))

@section('content')
    @php use App\Enums\CollaboratorRole; @endphp
    <div class="mb-auto container-fluid">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <h2 class="card-header">
                        {{ __('view.admin.collaborator.collaborators.show.heading', ['id' => $collaborator->id, 'name' => $collaborator->full_name]) }}
                    </h2>
                    <div class="card-body d-flex">
                        <a href="{{ route('admin.collaborators.edit', $collaborator->id) }}" type="button"
                            class="btn btn-warning me-1">
                            <i class="bi bi-pencil-fill"></i>
                            {{ __('view.admin.collaborator.collaborators.show.edit') }}
                        </a>
                        <form action="{{ route('admin.collaborators.destroy', $collaborator->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteCollaboratorButton btn btn-danger">
                                <i class="bi bi-trash-fill"></i>
                                {{ __('view.admin.collaborator.collaborators.show.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
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
                                <p class="card-text">{{ __('app.collaborator.role.' . (optional($collaborator->role)?->value ?? CollaboratorRole::EXTERNAL->value)) }}</p>
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
                                    <p class="card-text">{{ $item->category?->name }}</p>
                                    <strong>Colaborador: </strong>
                                    <p class="card-text">{{ $item->collaborator->full_name }}</p>
                                    <strong>Criado em: </strong>
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($item->created_at)) }}</p>
                                    <strong>Atualizado em: </strong>
                                    <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($item->updated_at)) }}</p>
                                    <div class="d-flex">
                                        <a href="{{ route('admin.items.show', $item->id) }}" type="button"
                                            class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i> Visualizar</a>
                                        <a href="{{ route('admin.items.edit', $item->id) }}" type="button"
                                            class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> Editar</a>
                                        <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="deleteItemButton btn btn-danger" type="submit"><i
                                                    class="bi bi-trash-fill"></i> Excluir
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
    </div>

    @include('image-modal.img-modal')

@endsection
