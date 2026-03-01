@extends('layouts.admin')
@section('title', __('view.admin.proprietary.proprietaries.index.title'))

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
                {{ __('view.admin.proprietary.proprietaries.index.heading', ['count' => $count]) }}
            </h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.proprietaries.create') }}" type="button" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i>
                    {{ __('view.admin.proprietary.proprietaries.index.add') }}
                </a>
                <form action="{{ route('admin.proprietaries.index') }}" class="d-flex" method="GET">
                    <select class="form-select me-2" id="search_column" name="search_column">
                        <option value="id" @if (request()->query('search_column') == 'id') selected @endif>
                            {{ __('view.admin.proprietary.proprietaries.index.search_option_id') }}
                        </option>
                        <option value="full_name" @if (request()->query('search_column') == 'full_name') selected @endif>
                            {{ __('view.admin.proprietary.proprietaries.index.search_option_full_name') }}
                        </option>
                        <option value="contact" @if (request()->query('search_column') == 'contact') selected @endif>
                            {{ __('view.admin.proprietary.proprietaries.index.search_option_contact') }}
                        </option>
                        <option value="blocked" @if (request()->query('search_column') == 'blocked') selected @endif>
                            {{ __('view.admin.proprietary.proprietaries.index.search_option_blocked') }}
                        </option>
                        <option value="is_admin" @if (request()->query('search_column') == 'is_admin') selected @endif>
                            {{ __('view.admin.proprietary.proprietaries.index.search_option_is_admin') }}
                        </option>
                        <option value="created_at" @if (request()->query('search_column') == 'created_at') selected @endif>
                            {{ __('view.admin.proprietary.proprietaries.index.search_option_created_at') }}
                        </option>
                        <option value="updated_at" @if (request()->query('search_column') == 'updated_at') selected @endif>
                            {{ __('view.admin.proprietary.proprietaries.index.search_option_updated_at') }}
                        </option>
                    </select>
                    <input
                        id="search"
                        name="search"
                        class="form-control me-2"
                        type="search"
                        placeholder="{{ __('view.admin.proprietary.proprietaries.index.search_placeholder') }}"
                        aria-label="Search"
                    >
                    <button class="btn btn-secondary" type="submit">
                        {{ __('view.admin.proprietary.proprietaries.index.search_button') }}
                    </button>
                </form>
            </div>
        </nav>
        <div class="row">
            <div class="col">
                <table class="table table-hover table-bordered">
                    <thead>
                        <form action="{{ route('admin.proprietaries.index') }}" method="GET">
                            <tr>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">
                                        {{ __('view.admin.proprietary.proprietaries.index.sort_id') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="full_name">
                                        {{ __('view.admin.proprietary.proprietaries.index.sort_full_name') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="contact">
                                        {{ __('view.admin.proprietary.proprietaries.index.sort_contact') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="blocked">
                                        {{ __('view.admin.proprietary.proprietaries.index.sort_blocked') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="is_admin">
                                        {{ __('view.admin.proprietary.proprietaries.index.sort_is_admin') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="created_at">
                                        {{ __('view.admin.proprietary.proprietaries.index.sort_created_at') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="updated_at">
                                        {{ __('view.admin.proprietary.proprietaries.index.sort_updated_at') }}
                                    </button>
                                </th>
                            </tr>
                            <input name="order" value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif" hidden>
                            <input name="search_column" value="{{ request()->query('search_column') }}" hidden>
                            <input name="search" value="{{ request()->query('search') }}" hidden>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($proprietaries as $proprietary)
                            <tr
                                class="@if (!$proprietary->locks->isEmpty() && $proprietary->locks->first()->user_id != auth()->user()->id) table-warning @endif">
                                <th scope="row">{{ $proprietary->id }}</th>
                                <td>{{ $proprietary->full_name }}</td>
                                <td>{{ $proprietary->contact }}</td>
                                <td>
                                    @if ($proprietary->blocked == 1)
                                        {{ __('view.admin.proprietary.proprietaries.index.yes') }}
                                    @else
                                        {{ __('view.admin.proprietary.proprietaries.index.no') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($proprietary->is_admin == 1)
                                        {{ __('view.admin.proprietary.proprietaries.index.yes') }}
                                    @else
                                        {{ __('view.admin.proprietary.proprietaries.index.no') }}
                                    @endif
                                </td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($proprietary->created_at)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($proprietary->updated_at)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.proprietaries.show', $proprietary->id) }}" type="button"
                                            class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('admin.proprietaries.edit', $proprietary->id) }}" type="button"
                                            class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i></a>
                                        <form action="{{ route('admin.proprietaries.destroy', $proprietary->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger deleteProprietaryButton"><i
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
        {{ $proprietaries->links('pagination::bootstrap-5') }}
    </div>

@endsection
