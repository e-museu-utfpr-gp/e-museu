@extends('layouts.admin')
@section('title', __('view.admin.catalog.extras.index.title'))

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
            <h2 class="card-header">{{ __('view.admin.catalog.extras.index.heading', ['count' => $count]) }}</h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.extras.create') }}" type="button" class="btn btn-success"><i
                        class="bi bi-plus-circle"></i> {{ __('view.admin.catalog.extras.index.add_extra') }}</a>
                <form action="{{ route('admin.extras.index') }}" class="d-flex" method="GET">
                    <select class="form-select me-2" id="search_column" name="search_column">
                        <option value="id" @if (request()->query('search_column') == 'id') selected @endif>{{ __('view.admin.catalog.extras.index.id') }}</option>
                        <option value="info" @if (request()->query('search_column') == 'info') selected @endif>{{ __('view.admin.catalog.extras.index.info') }}</option>
                        <option value="item_id" @if (request()->query('search_column') == 'item_id') selected @endif>{{ __('view.admin.catalog.extras.index.item') }}</option>
                        <option value="proprietary_id" @if (request()->query('search_column') == 'proprietary_id') selected @endif>{{ __('view.admin.catalog.extras.index.collaborator') }}
                        </option>
                        <option value="validation" @if (request()->query('search_column') == 'validation') selected @endif>{{ __('view.admin.catalog.extras.index.validation') }}</option>
                        <option value="created_at" @if (request()->query('search_column') == 'created_at') selected @endif>{{ __('view.admin.catalog.extras.index.created_at') }}</option>
                        <option value="updated_at" @if (request()->query('search_column') == 'updated_at') selected @endif>{{ __('view.admin.catalog.extras.index.updated_at') }}</option>
                    </select>
                    <input id="search" name="search" class="form-control me-2" type="search" placeholder="{{ __('view.admin.catalog.extras.index.search_placeholder') }}"
                        aria-label="Search">
                    <button class="btn btn-secondary" type="submit">{{ __('view.admin.catalog.extras.index.search_button') }}</button>
                </form>
            </div>
        </nav>
        <div class="row">
            <div class="col">
                <table class="table table-hover table-bordered">
                    <thead>
                        <form action="{{ route('admin.extras.index') }}" method="GET">
                            <tr>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">{{ __('view.admin.catalog.extras.index.id') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="info">{{ __('view.admin.catalog.extras.index.info') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="item_id">{{ __('view.admin.catalog.extras.index.item') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="proprietary_id">{{ __('view.admin.catalog.extras.index.collaborator') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="validation">{{ __('view.admin.catalog.extras.index.validation') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="created_at">{{ __('view.admin.catalog.extras.index.created_at') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="updated_at">{{ __('view.admin.catalog.extras.index.updated_at') }}</button></th>
                            </tr>
                            <input name="order" value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif" hidden>
                            <input name="search_column" value="{{ request()->query('search_column') }}" hidden>
                            <input name="search" value="{{ request()->query('search') }}" hidden>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($extras as $extra)
                            <tr class="@if (!$extra->locks->isEmpty() && $extra->locks->first()->user_id != auth()->user()->id) table-warning @endif">
                                <th scope="row">{{ $extra->id }}</th>
                                <td>{{ $extra->info }}</td>
                                <td>{{ $extra->item_name }}</td>
                                <td>{{ $extra->proprietary_contact }}</td>
                                <td>
                                    @if ($extra->extra_validation == 1)
                                        {{ __('view.admin.catalog.extras.index.yes') }}
                                    @else
                                        {{ __('view.admin.catalog.extras.index.no') }}
                                    @endif
                                </td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($extra->extra_created)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($extra->extra_updated)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.extras.show', $extra->id) }}" type="button"
                                            class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('admin.extras.edit', $extra->id) }}" type="button"
                                            class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i></a>
                                        <form action="{{ route('admin.extras.destroy', $extra->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger deleteExtraButton" data-confirm-message="{{ __('view.admin.catalog.extras.index.delete_confirm') }}"><i
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
        {{ $extras->links('pagination::bootstrap-5') }}
    </div>

@endsection
