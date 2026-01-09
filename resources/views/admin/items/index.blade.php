@extends('layouts.admin')
@section('title', 'Listar itens')

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
            <h2 class="card-header">Itens - {{ $count }} Cadastrados</h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.items.create') }}" type="button" class="btn btn-success"><i
                        class="bi bi-plus-circle"></i> Adicionar Item</a>
                <form action="{{ route('admin.items.index') }}" class="d-flex" method="GET">
                    <select class="form-select me-2" id="search_column" name="search_column">
                        <option value="id" @if (request()->query('search_column') == 'id') selected @endif>Id</option>
                        <option value="name" @if (request()->query('search_column') == 'name') selected @endif>Nome</option>
                        <option value="description" @if (request()->query('search_column') == 'description') selected @endif>Descrição</option>
                        <option value="history" @if (request()->query('search_column') == 'history') selected @endif>História</option>
                        <option value="detalhes" @if (request()->query('search_column') == 'detalhes') selected @endif>Detalhes</option>
                        <option value="date" @if (request()->query('search_column') == 'date') selected @endif>Data</option>
                        <option value="description" @if (request()->query('search_column') == 'description') selected @endif>Descrição</option>
                        <option value="identification_code" @if (request()->query('search_column') == 'identification_code') selected @endif>Código de
                            Identificação</option>
                        <option value="validation" @if (request()->query('search_column') == 'validation') selected @endif>Validação</option>
                        <option value="proprietary_id" @if (request()->query('search_column') == 'proprietary_id') selected @endif>Colaborador
                        </option>
                        <option value="section_id" @if (request()->query('search_column') == 'section_id') selected @endif>Categoria do Item</option>
                        <option value="created_at" @if (request()->query('search_column') == 'created_at') selected @endif>Criado em</option>
                        <option value="updated_at" @if (request()->query('search_column') == 'updated_at') selected @endif>Atualizado em
                        </option>
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
                        <form action="{{ route('admin.items.index') }}" method="GET">
                            <tr>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">Id</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="name">Nome</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="description">Descrição</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="history">História</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="detail">Detalhe</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="date">Data</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="identification_code">Código</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="validation">Validado</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="section_id">Categoria do Item</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="proprietary_id">Colaborador</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="created_at">Criado em</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="updated_at">Atualizado em</button></th>
                                <th scope="col"></th>
                            </tr>
                            <input name="order" value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif"
                                hidden>
                            <input name="search_column" value="{{ request()->query('search_column') }}" hidden>
                            <input name="search" value="{{ request()->query('search') }}" hidden>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr class="@if (!$item->locks->isEmpty() && $item->locks->first()->user_id != auth()->user()->id) table-warning @endif">
                                <th scope="row">{{ $item->id }}</th>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->description}}</td>
                                <td>{{ $item->history }}</td>
                                <td>{{ $item->detail }}</td>
                                <td>{{ date('d-m-Y', strtotime($item->date)) }}</td>
                                <td>{{ $item->identification_code }}</td>
                                <td>
                                    @if ($item->item_validation == 1)
                                        Sim
                                    @else
                                        Não
                                    @endif
                                </td>
                                <td>{{ $item->section_name }}</td>
                                <td>{{ $item->proprietary_contact }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($item->item_created)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($item->item_updated)) }}</td>
                                <td>
                                    <a href="{{ route('admin.items.show', $item->id) }}" type="button"
                                        class="btn btn-primary"><i class="bi bi-eye-fill"></i></a>
                                    <a href="{{ route('admin.items.edit', $item->id) }}" type="button"
                                        class="btn btn-warning my-1"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger deleteItemButton"><i
                                                class="bi bi-trash-fill"></i>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $items->links('pagination::bootstrap-5') }}
    </div>

@endsection
