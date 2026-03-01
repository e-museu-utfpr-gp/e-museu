@extends('layouts.admin')
@section('title', __('view.admin.proprietary.proprietaries.show.title', ['id' => $proprietary->id]))

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
                    <h2 class="card-header">
                        {{ __('view.admin.proprietary.proprietaries.show.heading', ['id' => $proprietary->id, 'name' => $proprietary->full_name]) }}
                    </h2>
                    <div class="card-body d-flex">
                        <a href="{{ route('admin.proprietaries.edit', $proprietary->id) }}" type="button"
                            class="btn btn-warning me-1">
                            <i class="bi bi-pencil-fill"></i>
                            {{ __('view.admin.proprietary.proprietaries.show.edit') }}
                        </a>
                        <form action="{{ route('admin.proprietaries.destroy', $proprietary->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteProprietaryButton btn btn-danger">
                                <i class="bi bi-trash-fill"></i>
                                {{ __('view.admin.proprietary.proprietaries.show.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.proprietary.proprietaries.show.id') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ $proprietary->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.proprietary.proprietaries.show.full_name') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ $proprietary->full_name }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.proprietary.proprietaries.show.contact') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ $proprietary->contact }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.proprietary.proprietaries.show.blocked') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($proprietary->blocked == 1)
                                        {{ __('view.admin.proprietary.proprietaries.show.yes') }}
                                    @else
                                        {{ __('view.admin.proprietary.proprietaries.show.no') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.proprietary.proprietaries.show.is_admin') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($proprietary->is_admin == 1)
                                        {{ __('view.admin.proprietary.proprietaries.show.yes') }}
                                    @else
                                        {{ __('view.admin.proprietary.proprietaries.show.no') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.proprietary.proprietaries.show.created_at') }}
                            </h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($proprietary->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.proprietary.proprietaries.show.updated_at') }}
                            </h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($proprietary->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <h5 class="card-header d-flex justify-content-between">
                        {{ __('view.admin.proprietary.proprietaries.show.items_heading') }}
                    </h5>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($proprietary->items()->paginate(15) as $item)
                                <li class="list-group-item">
                                    <strong>{{ __('view.admin.proprietary.proprietaries.show.item_id') }}: </strong>
                                    <p class="ms-3">{{ $item->id }}</p>
                                    <strong>{{ __('view.admin.proprietary.proprietaries.show.item_name') }}: </strong>
                                    <p class="card-text">{{ $item->name }}</p>
                                    @if ($item->image_url)
                                        <img src="{{ $item->image_url }}" class="img-thumbnail clickable-image"
                                            alt="{{ __('view.admin.proprietary.proprietaries.show.item_image_alt') }}"
                                            style="aspect-ratio: 3 / 2; width: 100%; max-height: 100%; object-fit: cover">
                                    @endif
                                    <strong>{{ __('view.admin.proprietary.proprietaries.show.item_description') }}: </strong>
                                    <p class="ms-3">{{ $item->description }}</p>
                                    <strong>{{ __('view.admin.proprietary.proprietaries.show.item_history') }}: </strong>
                                    <p class="card-text">{{ $item->history }}</p>
                                    <strong>{{ __('view.admin.proprietary.proprietaries.show.item_detail') }}: </strong>
                                    <p class="ms-3">{!! nl2br($item->detail) !!}</p>
                                    <strong>{{ __('view.admin.proprietary.proprietaries.show.item_date') }}: </strong>
                                    <p class="card-text">{{ date('d-m-Y', strtotime($item->date)) }}</p>
                                    <strong>{{ __('view.admin.proprietary.proprietaries.show.item_code') }}: </strong>
                                    <p class="ms-3">{{ $item->identification_code }}</p>
                                    <strong>{{ __('view.admin.proprietary.proprietaries.show.item_validated') }}: </strong>
                                    <p class="ms-3">
                                        @if ($item->validation == 1)
                                            {{ __('view.admin.proprietary.proprietaries.show.yes') }}
                                        @else
                                            {{ __('view.admin.proprietary.proprietaries.show.no') }}
                                        @endif
                                    </p>
                                    <strong>Categoria de Item: </strong>
                                    <p class="card-text">{{ $item->section->name }}</p>
                                    <strong>Colaborador: </strong>
                                    <p class="card-text">{{ $item->proprietary->full_name }}</p>
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
                {{ $proprietary->items()->paginate(15)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @include('image-modal.img-modal')

@endsection
