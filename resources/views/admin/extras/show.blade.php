@extends('layouts.admin')
@section('title', 'Informação Extra ' . $extra->id)

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
                    <h2 class="card-header">Mostrando curiosidade Extra: {{ $extra->id }}</h2>
                    <div class="card-body d-flex">
                        <a href="{{ route('admin.extras.edit', $extra->id) }}" type="button" class="btn btn-warning me-1"><i
                                class="bi bi-pencil-fill"></i> Editar</a>
                        <form action="{{ route('admin.extras.destroy', $extra->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteExtraButton btn btn-danger"><i class="bi bi-trash-fill"></i>
                                Excluir
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">Id</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $extra->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Curiosidade</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $extra->info }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Validado</h5>
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($extra->validation == 1)
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
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($extra->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Atualizado em</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($extra->updated_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Colaborador</h5>
                            <div class="card-body">
                                <strong>Id: </strong>
                                <p class="ms-3">{{ $extra->proprietary->id }}</p>
                                <strong>Nome completo: </strong>
                                <p class="ms-3">{{ $extra->proprietary->full_name }}</p>
                                <strong>Contato: </strong>
                                <p class="ms-3">{{ $extra->proprietary->contact }}</p>
                                <strong>Bloqueado: </strong>
                                <p class="ms-3">
                                    @if ($extra->proprietary->blocked == 1)
                                        Sim
                                    @else
                                        Não
                                    @endif
                                </p>
                                <strong>Criado em: </strong>
                                <p class="ms-3">{{ date('d-m-Y H:i:s', strtotime($extra->proprietary->created_at)) }}</p>
                                <strong>Atualizado em: </strong>
                                <p class="ms-3">{{ date('d-m-Y H:i:s', strtotime($extra->proprietary->updated_at)) }}</p>
                                <div class="d-flex">
                                    <a href="{{ route('admin.proprietaries.show', $extra->proprietary->id) }}"
                                        type="button" class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i>
                                        Visualizar</a>
                                    <a href="{{ route('admin.proprietaries.edit', $extra->proprietary->id) }}"
                                        type="button" class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i>
                                        Editar</a>
                                    <form action="{{ route('admin.proprietaries.destroy', $extra->proprietary->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="deleteProprietaryButton btn btn-danger" type="submit"><i
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
                        <p class="ms-3">{{ $extra->item->id }}</p>
                        <strong>Nome: </strong>
                        <p class="card-text">{{ $extra->item->name }}</p>
                        <img src="{{ url("storage/{$extra->item->image}") }}" class="img-thumbnail clickable-image"
                            alt="Imagem do item"
                            style="aspect-ratio: 1 / 1; width: 100%; max-height: 100%; object-fit: cover">
                        <strong>Descrição: </strong>
                        <p class="ms-3">{{ $extra->item->description }}</p>
                        <strong>História: </strong>
                        <p class="card-text">{{ $extra->item->history }}</p>
                        <strong>Detalhe: </strong>
                        <p class="ms-3">{!! nl2br($extra->item->detail) !!}</p>
                        <strong>Data: </strong>
                        <p class="card-text">{{ date('d-m-Y', strtotime($extra->item->date)) }}</p>
                        <strong>Código de Identificação: </strong>
                        <p class="ms-3">{{ $extra->item->identification_code }}</p>
                        <strong>Validado: </strong>
                        <p class="ms-3">
                            @if ($extra->item->validation == 1)
                                Sim
                            @else
                                Não
                            @endif
                        </p>
                        <strong>Categoria do Item: </strong>
                        <p class="card-text">{{ $extra->item->section->name }}</p>
                        <strong>Colaborador: </strong>
                        <p class="card-text">{{ $extra->item->proprietary->name }}</p>
                        <strong>Criado em: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($extra->item->created_at)) }}</p>
                        <strong>Atualizado em: </strong>
                        <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($extra->item->updated_at)) }}</p>
                        <div class="d-flex">
                            <a href="{{ route('admin.items.show', $extra->item->id) }}" type="button"
                                class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i> Visualizar</a>
                            <a href="{{ route('admin.items.edit', $extra->item->id) }}" type="button"
                                class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> Editar</a>
                            <form action="{{ route('admin.items.destroy', $extra->item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="deleteExtraButton btn btn-danger" type="submit"><i
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
