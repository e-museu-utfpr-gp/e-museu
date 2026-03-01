@extends('layouts.admin')
@section('title', __('view.admin.catalog.items.index.title'))

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
            <h2 class="card-header">{{ __('view.admin.catalog.items.index.heading', ['count' => $count]) }}</h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.items.create') }}" type="button" class="btn btn-success"><i
                        class="bi bi-plus-circle"></i> {{ __('view.admin.catalog.items.index.add_item') }}</a>
                <form action="{{ route('admin.items.index') }}" class="d-flex" method="GET">
                    <select class="form-select me-2" id="search_column" name="search_column">
                        <option value="id" @if (request()->query('search_column') == 'id') selected @endif>{{ __('view.admin.catalog.items.index.search_option_id') }}</option>
                        <option value="name" @if (request()->query('search_column') == 'name') selected @endif>{{ __('view.admin.catalog.items.index.search_option_name') }}</option>
                        <option value="description" @if (request()->query('search_column') == 'description') selected @endif>{{ __('view.admin.catalog.items.index.search_option_description') }}</option>
                        <option value="history" @if (request()->query('search_column') == 'history') selected @endif>{{ __('view.admin.catalog.items.index.search_option_history') }}</option>
                        <option value="detalhes" @if (request()->query('search_column') == 'detalhes') selected @endif>{{ __('view.admin.catalog.items.index.search_option_detail') }}</option>
                        <option value="date" @if (request()->query('search_column') == 'date') selected @endif>{{ __('view.admin.catalog.items.index.search_option_date') }}</option>
                        <option value="identification_code" @if (request()->query('search_column') == 'identification_code') selected @endif>{{ __('view.admin.catalog.items.index.search_option_identification_code') }}</option>
                        <option value="validation" @if (request()->query('search_column') == 'validation') selected @endif>{{ __('view.admin.catalog.items.index.search_option_validation') }}</option>
                        <option value="proprietary_id" @if (request()->query('search_column') == 'proprietary_id') selected @endif>{{ __('view.admin.catalog.items.index.search_option_proprietary') }}</option>
                        <option value="section_id" @if (request()->query('search_column') == 'section_id') selected @endif>{{ __('view.admin.catalog.items.index.search_option_section') }}</option>
                        <option value="created_at" @if (request()->query('search_column') == 'created_at') selected @endif>{{ __('view.admin.catalog.items.index.search_option_created_at') }}</option>
                        <option value="updated_at" @if (request()->query('search_column') == 'updated_at') selected @endif>{{ __('view.admin.catalog.items.index.search_option_updated_at') }}</option>
                    </select>
                    <input id="search" name="search" class="form-control me-2" type="search" placeholder="{{ __('view.admin.catalog.items.index.search_placeholder') }}"
                        aria-label="Search">
                    <button class="btn btn-secondary" type="submit">{{ __('view.admin.catalog.items.index.search_button') }}</button>
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
                                        name="sort" value="id">{{ __('view.admin.catalog.items.index.sort_id') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="name">{{ __('view.admin.catalog.items.index.sort_name') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="description">{{ __('view.admin.catalog.items.index.sort_description') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="history">{{ __('view.admin.catalog.items.index.sort_history') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="detail">{{ __('view.admin.catalog.items.index.sort_detail') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="date">{{ __('view.admin.catalog.items.index.sort_date') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="identification_code">{{ __('view.admin.catalog.items.index.sort_code') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="validation">{{ __('view.admin.catalog.items.index.sort_validation') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="section_id">{{ __('view.admin.catalog.items.index.sort_section') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="proprietary_id">{{ __('view.admin.catalog.items.index.sort_collaborator') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="created_at">{{ __('view.admin.catalog.items.index.created_at') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="updated_at">{{ __('view.admin.catalog.items.index.updated_at') }}</button></th>
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
                                        {{ __('view.admin.catalog.items.index.yes') }}
                                    @else
                                        {{ __('view.admin.catalog.items.index.no') }}
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
                                        <button type="submit" class="btn btn-danger deleteItemButton" data-confirm-message="{{ __('view.admin.catalog.items.index.delete_confirm') }}"><i
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
