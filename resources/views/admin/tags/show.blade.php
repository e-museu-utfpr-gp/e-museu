@extends('layouts.admin')
@section('title', 'Etiqueta ' . $tag->id)

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
                    <h2 class="card-header">Mostrando Etiqueta: {{ $tag->id }} - {{ $tag->name }}</h2>
                    <div class="card-body d-flex">
                        <a href="{{ route('admin.tags.edit', $tag->id) }}" type="button" class="btn btn-warning me-1"><i
                                class="bi bi-pencil-fill"></i> Editar</a>
                        <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteTagButton btn btn-danger"><i class="bi bi-trash-fill"></i>
                                Excluir
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">Id</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $tag->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Nome</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $tag->name }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Validado</h5>
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($tag->validation == 1)
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
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tag->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Atualizado em</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tag->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">Categoria</h5>
                            <div class="card-body">
                                <strong>Id: </strong>
                                <p class="ms-3">{{ $tag->category->id }}</p>
                                <strong>Nome: </strong>
                                <p class="card-text">{{ $tag->category->name }}</p>
                                <strong>Criado em: </strong>
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tag->category->created_at)) }}</p>
                                <strong>Atualizado em: </strong>
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tag->category->updated_at)) }}</p>
                                <div class="d-flex">
                                    <a href="{{ route('admin.categories.show', $tag->category->id) }}" type="button"
                                        class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i> Visualizar</a>
                                    <a href="{{ route('admin.categories.edit', $tag->category->id) }}" type="button"
                                        class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> Editar</a>
                                    <form action="{{ route('admin.categories.destroy', $tag->category->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="deleteCategoryButton btn btn-danger" type="submit"><i
                                                class="bi bi-trash-fill"></i> Excluir
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
