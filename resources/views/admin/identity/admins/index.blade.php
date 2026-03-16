@extends('layouts.admin')
@section('title', __('view.admin.identity.admins.index.title'))

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
            <h2 class="card-header">{{ __('view.admin.identity.admins.index.heading', ['count' => $count]) }}</h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.admins.create') }}" type="button" class="btn btn-success"><i
                        class="bi bi-plus-circle"></i> {{ __('view.admin.identity.admins.index.add_admin') }}</a>
                @php
                    $searchOptions = [
                        ['value' => 'id', 'label' => __('view.admin.identity.admins.index.id')],
                        ['value' => 'username', 'label' => __('view.admin.identity.admins.index.username')],
                        ['value' => 'created_at', 'label' => __('view.admin.identity.admins.index.created_at')],
                        ['value' => 'updated_at', 'label' => __('view.admin.identity.admins.index.updated_at')],
                    ];
                @endphp
                <x-admin.search-form
                    :action="route('admin.admins.index')"
                    :options="$searchOptions"
                    :placeholder="__('view.admin.identity.admins.index.search_placeholder')"
                    :buttonLabel="__('view.admin.identity.admins.index.search_button')"
                />
            </div>
        </nav>
        <div class="row">
            <div class="col">
                <table class="table table-hover table-bordered">
                    <thead>
                        <form action="{{ route('admin.admins.index') }}" method="GET">
                            <tr>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">{{ __('view.admin.identity.admins.index.id') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="username">{{ __('view.admin.identity.admins.index.username') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="created_at">{{ __('view.admin.identity.admins.index.created_at') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="updated_at">{{ __('view.admin.identity.admins.index.updated_at') }}</button></th>
                            </tr>
                            <input name="order" value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif" hidden>
                            <input name="search_column" value="{{ request()->query('search_column') }}" hidden>
                            <input name="search" value="{{ request()->query('search') }}" hidden>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($admins as $admin)
                            <tr class="@if (!$admin->locks->isEmpty()) table-warning @endif">
                                <th scope="row">{{ $admin->id }}</th>
                                <td>{{ $admin->username }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($admin->created_at)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($admin->updated_at)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.admins.show', $admin->id) }}" type="button"
                                            class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i></a>
                                        <form class="me-1" action="{{ route('admin.admins.destroy', $admin->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger deleteAdminButton" data-confirm-message="{{ __('view.admin.identity.admins.index.delete_confirm') }}"><i
                                                    class="bi bi-trash-fill"></i></button>
                                        </form>
                                        <form action="{{ route('admin.admins.delete-lock', $admin->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-warning deleteLockButton" title="{{ __('view.admin.identity.admins.index.unlock_tooltip') }}"><i
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
        {{ $admins->links('pagination::bootstrap-5') }}
    </div>

@endsection
