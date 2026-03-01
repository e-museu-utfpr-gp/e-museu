@extends('layouts.admin')
@section('title', __('view.admin.catalog.item_categories.index.title'))

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
            <h2 class="card-header">
                {{ __('view.admin.catalog.item_categories.index.heading', ['count' => $count]) }}
            </h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.item-categories.create') }}" type="button" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i>
                    {{ __('view.admin.catalog.item_categories.index.add_item_category') }}
                </a>
                <form action="{{ route('admin.item-categories.index') }}" class="d-flex" method="GET">
                    <select class="form-select me-2" id="search_column" name="search_column">
                        <option value="id" @if (request()->query('search_column') == 'id') selected @endif>
                            {{ __('view.admin.catalog.item_categories.index.search_option_id') }}
                        </option>
                        <option value="name" @if (request()->query('search_column') == 'name') selected @endif>
                            {{ __('view.admin.catalog.item_categories.index.search_option_name') }}
                        </option>
                        <option value="created_at" @if (request()->query('search_column') == 'created_at') selected @endif>
                            {{ __('view.admin.catalog.item_categories.index.search_option_created_at') }}
                        </option>
                        <option value="updated_at" @if (request()->query('search_column') == 'updated_at') selected @endif>
                            {{ __('view.admin.catalog.item_categories.index.search_option_updated_at') }}
                        </option>
                    </select>
                    <input
                        id="search"
                        name="search"
                        class="form-control me-2"
                        type="search"
                        placeholder="{{ __('view.admin.catalog.item_categories.index.search_placeholder') }}"
                        aria-label="Search"
                    >
                    <button class="btn btn-secondary" type="submit">
                        {{ __('view.admin.catalog.item_categories.index.search_button') }}
                    </button>
                </form>
            </div>
        </nav>
        <div class="row">
            <div class="col">
                <table class="table table-hover table-bordered">
                    <thead>
                        <form action="{{ route('admin.item-categories.index') }}" method="GET">
                            <tr>
                                <th scope="col">
                                    <button
                                        class="btn border-0 bg-transparent px-0 py-0"
                                        type="submit"
                                        name="sort"
                                        value="id"
                                    >
                                        {{ __('view.admin.catalog.item_categories.index.sort_id') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button
                                        class="btn border-0 bg-transparent px-0 py-0"
                                        type="submit"
                                        name="sort"
                                        value="name"
                                    >
                                        {{ __('view.admin.catalog.item_categories.index.sort_name') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button
                                        class="btn border-0 bg-transparent px-0 py-0"
                                        type="submit"
                                        name="sort"
                                        value="created_at"
                                    >
                                        {{ __('view.admin.catalog.item_categories.index.sort_created_at') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button
                                        class="btn border-0 bg-transparent px-0 py-0"
                                        type="submit"
                                        name="sort"
                                        value="updated_at"
                                    >
                                        {{ __('view.admin.catalog.item_categories.index.sort_updated_at') }}
                                    </button>
                                </th>
                            </tr>
                            <input name="order" value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif" hidden>
                            <input name="search_column" value="{{ request()->query('search_column') }}" hidden>
                            <input name="search" value="{{ request()->query('search') }}" hidden>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($itemCategories as $itemCategory)
                            <tr class="@if (!$itemCategory->locks->isEmpty() && $itemCategory->locks->first()->admin_id != auth()->user()->id) table-warning @endif">
                                <th scope="row">{{ $itemCategory->id }}</th>
                                <td>{{ $itemCategory->name }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($itemCategory->created_at)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($itemCategory->updated_at)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.item-categories.show', $itemCategory->id) }}" type="button"
                                            class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('admin.item-categories.edit', $itemCategory->id) }}" type="button"
                                            class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i></a>
                                        <form action="{{ route('admin.item-categories.destroy', $itemCategory->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger deleteItemCategoryButton" data-confirm-message="{{ __('view.admin.catalog.item_categories.delete_confirm') }}"><i
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
        {{ $itemCategories->links('pagination::bootstrap-5') }}
    </div>

@endsection
