@extends('layouts.admin')
@section('title', 'Listar componentes')

@section('content')
    <div class="mb-auto container-fluid">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="card mb-3">
            <h2 class="card-header">Componentes - {{ $count }} Cadastrados</h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.components.create') }}" type="button" class="btn btn-success"><i
                        class="bi bi-plus-circle"></i> Adicionar Componente</a>
                <form action="{{ route('admin.components.index') }}" class="d-flex" method="GET">
                    <select class="form-select me-2" id="search_column" name="search_column">
                        <option value="id" @if (request()->query('search_column') == 'id') selected @endif>Id</option>
                        <option value="item_id" @if (request()->query('search_column') == 'item_id') selected @endif>Item principal</option>
                        <option value="component_id" @if (request()->query('search_column') == 'component_id') selected @endif>Componente</option>
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
                        <form action="{{ route('admin.components.index') }}" method="GET">
                            <tr>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">Id</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="item_id">Item Principal</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="component_id">Componente</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="validation">Validação</button></th>
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
                        @foreach ($components as $component)
                            <tr>
                                <th scope="row">{{ $component->id }}</th>
                                <td>{{ $component->item_name }}</td>
                                <td>{{ $component->component_name }}</td>
                                <td>
                                    @if ($component->item_component_validation == 1)
                                        Sim
                                    @else
                                        Não
                                    @endif
                                </td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($component->item_component_created)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($component->item_component_updated)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.components.show', $component->id) }}" type="button"
                                            class="btn btn-primary me-1" data-toggle="tooltip" data-placement="top"
                                            title="Visualizar Componente"><i class="bi bi-eye-fill"></i></a>
                                        <form action="{{ route('admin.components.update', $component->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning me-1" data-toggle="tooltip"
                                                data-placement="top" title="Validar / Invalidar Componente"><i
                                                    class="bi bi-check2-circle h6"></i></a>
                                        </form>
                                        <form action="{{ route('admin.components.destroy', $component->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger deleteComponentButton"
                                                data-toggle="tooltip" data-placement="top" title="Excluir componente"><i
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
        {{ $components->links('pagination::bootstrap-5') }}
    </div>

@endsection
