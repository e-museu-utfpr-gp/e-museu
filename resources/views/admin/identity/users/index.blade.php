@extends('layouts.admin')
@section('title', __('view.admin.identity.users.index.title'))

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
            <h2 class="card-header">{{ __('view.admin.identity.users.index.heading', ['count' => $count]) }}</h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.users.create') }}" type="button" class="btn btn-success"><i
                        class="bi bi-plus-circle"></i> {{ __('view.admin.identity.users.index.add_user') }}</a>
                <form action="{{ route('admin.users.index') }}" class="d-flex" method="GET">
                    <select class="form-select me-2" id="search_column" name="search_column">
                        <option value="id" @if (request()->query('search_column') == 'id') selected @endif>{{ __('view.admin.identity.users.index.id') }}</option>
                        <option value="username" @if (request()->query('search_column') == 'username') selected @endif>{{ __('view.admin.identity.users.index.username') }}</option>
                        <option value="created_at" @if (request()->query('search_column') == 'created_at') selected @endif>{{ __('view.admin.identity.users.index.created_at') }}</option>
                        <option value="updated_at" @if (request()->query('search_column') == 'updated_at') selected @endif>{{ __('view.admin.identity.users.index.updated_at') }}</option>
                    </select>
                    <input id="search" name="search" class="form-control me-2" type="search" placeholder="{{ __('view.admin.identity.users.index.search_placeholder') }}"
                        aria-label="Search">
                    <button class="btn btn-secondary" type="submit">{{ __('view.admin.identity.users.index.search_button') }}</button>
                </form>
            </div>
        </nav>
        <div class="row">
            <div class="col">
                <table class="table table-hover table-bordered">
                    <thead>
                        <form action="{{ route('admin.users.index') }}" method="GET">
                            <tr>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">{{ __('view.admin.identity.users.index.id') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="username">{{ __('view.admin.identity.users.index.username') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="created_at">{{ __('view.admin.identity.users.index.created_at') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="updated_at">{{ __('view.admin.identity.users.index.updated_at') }}</button></th>
                            </tr>
                            <input name="order" value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif" hidden>
                            <input name="search_column" value="{{ request()->query('search_column') }}" hidden>
                            <input name="search" value="{{ request()->query('search') }}" hidden>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="@if (!$user->locks->isEmpty()) table-warning @endif">
                                <th scope="row">{{ $user->id }}</th>
                                <td>{{ $user->username }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($user->created_at)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($user->updated_at)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.users.show', $user->id) }}" type="button"
                                            class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i></a>
                                        <form class="me-1" action="{{ route('admin.users.destroy', $user->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger deleteUserButton" data-confirm-message="{{ __('view.admin.identity.users.index.delete_confirm') }}"><i
                                                    class="bi bi-trash-fill"></i></button>
                                        </form>
                                        <form action="{{ route('admin.users.delete-lock', $user->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-warning deleteLockButton" title="{{ __('view.admin.identity.users.index.unlock_tooltip') }}"><i
                                                    class="bi bi-unlock-fill"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $users->links('pagination::bootstrap-5') }}
    </div>

@endsection
