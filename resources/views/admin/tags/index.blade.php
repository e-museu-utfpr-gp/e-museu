@extends('layouts.admin')
@section('title', 'Listar etiquetas')

@section('content')
    <div class="mb-auto container-fluid">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <div class="card mb-3">
            <h2 class="card-header">Etiquetas - {{ $count }} Cadastrados</h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.tags.create') }}" type="button" class="btn btn-success"><i
                        class="bi bi-plus-circle"></i> Adicionar Etiqueta</a>
                <form action="{{ route('admin.tags.index') }}" class="d-flex" method="GET">
                    <select class="form-select me-2" id="search_column" name="search_column">
                        <option value="id" @if (request()->query('search_column') == 'id') selected @endif>Id</option>
                        <option value="name" @if (request()->query('search_column') == 'name') selected @endif>Nome</option>
                        <option value="validation" @if (request()->query('search_column') == 'validation') selected @endif>Validação</option>
                        <option value="category_id" @if (request()->query('search_column') == 'category_id') selected @endif>Categoria</option>
                        <option value="created_at" @if (request()->query('search_column') == 'created_at') selected @endif>Criado em</option>
                        <option value="updated_at" @if (request()->query('search_column') == 'updated_at') selected @endif>Atualizado em</option>
                    </select>
                    <input id="search" name="search" class="form-control me-2" type="search" placeholder="Buscar"
                        aria-label="Search">
                    <button class="btn btn-secondary" type="submit">Buscar</button>
                </form>
            </div>
        </nav>
        <div class="row">
            <div class="col">
                <table class="table table-hover table-bordered">
                    <thead>
                        <form action="{{ route('admin.tags.index') }}" method="GET">
                            <tr>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">Id</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="name">Nome</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="validation">Validado</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="category_id">Categoria</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="created_at">Criado em</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="updated_at">Atualizado em</button></th>
                            </tr>
                            <input name="order" value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif" hidden>
                            <input name="search_column" value="{{ request()->query('search_column') }}" hidden>
                            <input name="search" value="{{ request()->query('search') }}" hidden>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($tags as $tag)
                            <tr class="@if (!$tag->locks->isEmpty() && $tag->locks->first()->user_id != auth()->user()->id) table-warning @endif">
                                <th scope="row">{{ $tag->id }}</th>
                                <td>{{ $tag->tag_name }}</td>
                                <td>
                                    @if ($tag->validation == 1)
                                        Sim
                                    @else
                                        Não
                                    @endif
                                </td>
                                <td>{{ $tag->category_name }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($tag->tag_created)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($tag->tag_updated)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.tags.show', $tag->id) }}" type="button"
                                            class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('admin.tags.edit', $tag->id) }}" type="button"
                                            class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i></a>
                                        <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger deleteTagButton"><i
                                                    class="bi bi-trash-fill"></i>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $tags->links('pagination::bootstrap-5') }}
    </div>

@endsection
