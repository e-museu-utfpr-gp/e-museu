@extends('layouts.admin')
@section('title', 'Etiqueta-Item ' . $itemTag->tag->id . '-' . $itemTag->item->id)

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
                    <h2 class="card-header">Mostrando Relacionamento de Item e Etiqueta: {{ $itemTag->id }}</h2>
                    <div class="card-body d-flex">
                        <form action="{{ route('admin.item-tags.update', $itemTag->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning me-1" data-toggle="tooltip" data-placement="top"
                                title="Validar / Invalidar Componente"><i class="bi bi-check2-circle h6"></i>Validar /
                                Invalidar</a>
                        </form>
                        <form action="{{ route('admin.item-tags.destroy', $itemTag->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteItemTagButton btn btn-danger"><i
                                    class="bi bi-trash-fill"></i> Excluir
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">Id</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $itemTag->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Validado</h5>
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($itemTag->validation == 1)
                                        Sim
                                    @else
                                        Não
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Criado em</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemTag->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Atualizado em</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemTag->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">Etiqueta</h5>
                            <div class="card-body">
                                <strong>Id: </strong>
                                <p class="ms-3">{{ $itemTag->tag->id }}</p>
                                <strong>Nome: </strong>
                                <p class="card-text">{{ $itemTag->tag->name }}</p>
                                <strong>Validado: </strong>
                                <p class="ms-3">
                                    @if ($itemTag->tag->validation == 1)
                                        Sim
                                    @else
                                        Não
                                    @endif
                                </p>
                                <strong>Criado em: </strong>
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemTag->tag->created_at)) }}</p>
                                <strong>Atualizado em: </strong>
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemTag->tag->updated_at)) }}</p>
                                <div class="d-flex">
                                    <a href="{{ route('admin.tags.show', $itemTag->tag->id) }}" type="button"
                                        class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i> Visualizar</a>
                                    <a href="{{ route('admin.tags.edit', $itemTag->tag->id) }}" type="button"
                                        class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> Editar</a>
                                    <form action="{{ route('admin.tags.destroy', $itemTag->tag->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="deleteTagButton btn btn-danger" type="submit"><i
                                                class="bi bi-trash-fill"></i> Excluir
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <h5 class="card-header">Item</h5>
                    <div class="card-body">
                        <strong>Id: </strong>
                        <p class="ms-3">{{ $itemTag->item->id }}</p>
                        <strong>Nome: </strong>
                        <p class="card-text">{{ $itemTag->item->name }}</p>
                        <img src="{{ url("storage/{$itemTag->item->image}") }}" class="img-thumbnail clickable-image"
                            alt="Imagem do item"
                            style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover">
                        <strong>Descrição: </strong>
                        <p class="ms-3">{{ $itemTag->item->description }}</p>
                        <strong>História: </strong>
                        <p class="card-text">{{ $itemTag->item->history }}</p>
                        <strong>Detalhe: </strong>
                        <p class="ms-3">{!! nl2br($itemTag->item->detail) !!}</p>
                        <strong>Data: </strong>
                        <p class="card-text">{{ date('d-m-Y', strtotime($itemTag->item->date)) }}</p>
                        <strong>Código de Identificação: </strong>
                        <p class="ms-3">{{ $itemTag->item->identification_code }}</p>
                        <strong>Validado: </strong>
                        <p class="ms-3">
                            @if ($itemTag->item->validation == 1)
                                Sim
                            @else
                                Não
                            @endif
                        </p>
                        <strong>Categoria do Item: </strong>
                        <p class="card-text">{{ $itemTag->item->section->name }}</p>
                        <strong>Colaborador: </strong>
                        <p class="card-text">{{ $itemTag->item->proprietary->name }}</p>
                        <strong>Criado em: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemTag->item->created_at)) }}</p>
                        <strong>Atualizado em: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($itemTag->item->updated_at)) }}</p>
                        <div class="d-flex">
                            <a href="{{ route('admin.items.show', $itemTag->item->id) }}" type="button"
                                class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i> Visualizar</a>
                            <a href="{{ route('admin.items.edit', $itemTag->item->id) }}" type="button"
                                class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> Editar</a>
                            <form action="{{ route('admin.items.destroy', $itemTag->item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="deleteItemButton btn btn-danger" type="submit"><i
                                        class="bi bi-trash-fill"></i> Excluir
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('image-modal.img-modal')

@endsection
