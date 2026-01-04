@extends('layouts.admin')
@section('title', 'Listar categorias de item')

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
            <h2 class="card-header">Categorias de Item - {{ $count }} Cadastrados</h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.sections.create') }}" type="button" class="btn btn-success"><i
                        class="bi bi-plus-circle"></i> Adicionar Categoria de Item</a>
                <form action="{{ route('admin.sections.index') }}" class="d-flex" method="GET">
                    <select class="form-select me-2" id="search_column" name="search_column">
                        <option value="id" @if (request()->query('search_column') == 'id') selected @endif>Id</option>
                        <option value="name" @if (request()->query('search_column') == 'name') selected @endif>Nome</option>
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
                        <form action="{{ route('admin.sections.index') }}" method="GET">
                            <tr>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">Id</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="name">Nome</button></th>
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
                        @foreach ($sections as $section)
                            <tr class="@if (!$section->locks->isEmpty() && $section->locks->first()->user_id != auth()->user()->id) table-warning @endif">
                                <th scope="row">{{ $section->id }}</th>
                                <td>{{ $section->name }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($section->created_at)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($section->updated_at)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.sections.show', $section->id) }}" type="button"
                                            class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('admin.sections.edit', $section->id) }}" type="button"
                                            class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i></a>
                                        <form action="{{ route('admin.sections.destroy', $section->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger deleteSectionButton"><i
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
        {{ $sections->links('pagination::bootstrap-5') }}
    </div>

@endsection
