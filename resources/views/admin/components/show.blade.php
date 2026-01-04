@extends('layouts.admin')
@section('title', 'Componente ' . $component->id)

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
                    <h2 class="card-header">Mostrando Componente: {{ $component->id }}</h2>
                    <div class="card-body d-flex">
                        <form action="{{ route('admin.components.update', $component->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning me-1" data-toggle="tooltip" data-placement="top"
                                title="Validar / Invalidar Componente"><i class="bi bi-check2-circle h6"></i> Validar /
                                Invalidar</a>
                        </form>
                        <form action="{{ route('admin.components.destroy', $component->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteComponentButton btn btn-danger"><i
                                    class="bi bi-trash-fill"></i> Excluir
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">Id</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $component->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Validado</h5>
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($component->validation == 1)
                                        Sim
                                    @else
                                        Não
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">Criado em</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($component->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Atualizado em</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($component->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <h5 class="card-header">Item Principal</h5>
                    <div class="card-body">
                        <strong>Id: </strong>
                        <p class="ms-3">{{ $component->item->id }}</p>
                        <strong>Nome: </strong>
                        <p class="card-text">{{ $component->item->name }}</p>
                        <img src="{{ url("storage/{$component->item->image}") }}" class="img-thumbnail clickable-image"
                            alt="Imagem do item"
                            style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover">
                        <strong>Descrição: </strong>
                        <p class="ms-3">{{ $component->item->description }}</p>
                        <strong>História: </strong>
                        <p class="card-text">{{ $component->item->history }}</p>
                        <strong>Detalhe: </strong>
                        <p class="ms-3">{!! nl2br($component->item->detail) !!}</p>
                        <strong>Data: </strong>
                        <p class="card-text">{{ date('d-m-Y', strtotime($component->item->date)) }}</p>
                        <strong>Código de Identificação: </strong>
                        <p class="ms-3">{{ $component->item->identification_code }}</p>
                        <strong>Validado: </strong>
                        <p class="ms-3">
                            @if ($component->item->validation == 1)
                                Sim
                            @else
                                Não
                            @endif
                        </p>
                        <strong>Categoria do Item: </strong>
                        <p class="card-text">{{ $component->item->section->name }}</p>
                        <strong>Colaborador: </strong>
                        <p class="card-text">{{ $component->item->proprietary->name }}</p>
                        <strong>Criado em: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($component->item->created_at)) }}</p>
                        <strong>Atualizado em: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($component->item->updated_at)) }}</p>
                        <div class="d-flex">
                            <a href="{{ route('admin.items.show', $component->item->id) }}" type="button"
                                class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i> Visualizar</a>
                            <a href="{{ route('admin.items.edit', $component->item->id) }}" type="button"
                                class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> Editar</a>
                            <form action="{{ route('admin.items.destroy', $component->item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="deleteItemButton btn btn-danger" type="submit"><i
                                        class="bi bi-trash-fill"></i> Excluir
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card mb-3">
                    <h5 class="card-header">Componente</h5>
                    <div class="card-body">
                        <strong>Id: </strong>
                        <p class="ms-3">{{ $component->component->id }}</p>
                        <strong>Nome: </strong>
                        <p class="card-text">{{ $component->component->name }}</p>
                        <img src="{{ url("storage/{$component->component->image}") }}"
                            class="img-thumbnail clickable-image" alt="Imagem do componente"
                            style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover">
                        <strong>Descrição: </strong>
                        <p class="ms-3">{{ $component->component->description }}</p>
                        <strong>História: </strong>
                        <p class="card-text">{{ $component->component->history }}</p>
                        <strong>Detalhe: </strong>
                        <p class="ms-3">{!! nl2br($component->component->detail) !!}</p>
                        <strong>Data: </strong>
                        <p class="card-text">{{ date('d-m-Y', strtotime($component->component->date)) }}</p>
                        <strong>Código de Identificação: </strong>
                        <p class="ms-3">{{ $component->component->identification_code }}</p>
                        <strong>Validado: </strong>
                        <p class="ms-3">
                            @if ($component->component->validation == 1)
                                Sim
                            @else
                                Não
                            @endif
                        </p>
                        <strong>Categoria do Item: </strong>
                        <p class="card-text">{{ $component->component->section->name }}</p>
                        <strong>Colaborador: </strong>
                        <p class="card-text">{{ $component->component->proprietary->name }}</p>
                        <strong>Criado em: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($component->component->created_at)) }}</p>
                        <strong>Atualizado em: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($component->component->updated_at)) }}</p>
                        <div class="d-flex">
                            <a href="{{ route('admin.items.show', $component->component->id) }}" type="button"
                                class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i> Visualizar</a>
                            <a href="{{ route('admin.items.edit', $component->component->id) }}" type="button"
                                class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> Editar</a>
                            <form action="{{ route('admin.items.destroy', $component->component->id) }}" method="POST">
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
