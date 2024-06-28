@extends('layouts.admin')
@section('title', 'Categoria de etiqueta' . $category->id)


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
                    <h2 class="card-header">Mostrando Categoria: {{ $category->id }} - {{ $category->name }}</h2>
                    <div class="card-body d-flex">
                        <a href="{{ route('admin.categories.edit', $category->id) }}" type="button"
                            class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> Editar</a>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteCategoryButton btn btn-danger"><i
                                    class="bi bi-trash-fill"></i> Excluir
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">Id</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $category->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Nome</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $category->name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">Criado em</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($category->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">Atualizado em</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($category->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('script/deleteCategoryWarning.js') }}"></script>
@endsection
