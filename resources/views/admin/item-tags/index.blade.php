@extends('layouts.admin')
@section('title', 'Listar relacionamentos etiqueta-item')

@section('content')
    <div class="mb-auto container-fluid">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="card mb-3">
            <h2 class="card-header">Relacionamentos de Item e Etiqueta - {{ $count }} Cadastrados</h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.item-tags.create') }}" type="button" class="btn btn-success"><i
                        class="bi bi-plus-circle"></i> Relacionar Etiqueta</a>
                <form action="{{ route('admin.item-tags.index') }}" class="d-flex" method="GET">
                    <select class="form-select me-2" id="search_column" name="search_column">
                        <option value="id" @if (request()->query('search_column') == 'id') selected @endif>Id</option>
                        <option value="item_id" @if (request()->query('search_column') == 'item_id') selected @endif>Item</option>
                        <option value="tag_id" @if (request()->query('search_column') == 'tag_id') selected @endif>Etiqueta</option>
                        <option value="validation" @if (request()->query('search_column') == 'validation') selected @endif>Validação</option>
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
                        <form action="{{ route('admin.item-tags.index') }}" method="GET">
                            <tr>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">Id</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="item_id">Item</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="tag_id">Etiqueta</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="validation">Validação</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="created_at">Criado em</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="updated_at">Atualizado em</button></th>
                                <th scope="col"></th>
                            </tr>
                            <input name="order" value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif" hidden>
                            <input name="search_column" value="{{ request()->query('search_column') }}" hidden>
                            <input name="search" value="{{ request()->query('search') }}" hidden>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($itemTags as $itemTag)
                            <tr>
                                <th scope="row">{{ $itemTag->id }}</th>
                                <td>{{ $itemTag->item_name }}</td>
                                <td>{{ $itemTag->tag_name }}</td>
                                <td>
                                    @if ($itemTag->tag_item_validation == 1)
                                        Sim
                                    @else
                                        Não
                                    @endif
                                </td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($itemTag->tag_item_created)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($itemTag->tag_item_updated)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.item-tags.show', $itemTag->id) }}" type="button"
                                            class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i></a>
                                        <form action="{{ route('admin.item-tags.update', $itemTag->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning me-1" data-toggle="tooltip"
                                                data-placement="top" title="Validar / Invalidar Componente"><i
                                                    class="bi bi-check2-circle h6"></i></a>
                                        </form>
                                        <form action="{{ route('admin.item-tags.destroy', $itemTag->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger deleteItemTagButton"><i
                                                    class="bi bi-trash-fill "></i>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $itemTags->links('pagination::bootstrap-5') }}
    </div>

@endsection
