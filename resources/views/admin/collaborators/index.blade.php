@extends('layouts.admin')
@section('title', __('view.admin.collaborator.collaborators.index.title'))

@section('content')
    @php use App\Enums\CollaboratorRole; @endphp
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
                {{ __('view.admin.collaborator.collaborators.index.heading', ['count' => $count]) }}
            </h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.collaborators.create') }}" type="button" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i>
                    {{ __('view.admin.collaborator.collaborators.index.add') }}
                </a>
                <form action="{{ route('admin.collaborators.index') }}" class="d-flex" method="GET">
                    <select class="form-select me-2" id="search_column" name="search_column">
                        <option value="id" @if (request()->query('search_column') == 'id') selected @endif>
                            {{ __('view.admin.collaborator.collaborators.index.search_option_id') }}
                        </option>
                        <option value="full_name" @if (request()->query('search_column') == 'full_name') selected @endif>
                            {{ __('view.admin.collaborator.collaborators.index.search_option_full_name') }}
                        </option>
                        <option value="contact" @if (request()->query('search_column') == 'contact') selected @endif>
                            {{ __('view.admin.collaborator.collaborators.index.search_option_contact') }}
                        </option>
                        <option value="role" @if (request()->query('search_column') == 'role') selected @endif>
                            {{ __('view.admin.collaborator.collaborators.index.search_option_role') }}
                        </option>
                        <option value="blocked" @if (request()->query('search_column') == 'blocked') selected @endif>
                            {{ __('view.admin.collaborator.collaborators.index.search_option_blocked') }}
                        </option>
                        <option value="created_at" @if (request()->query('search_column') == 'created_at') selected @endif>
                            {{ __('view.admin.collaborator.collaborators.index.search_option_created_at') }}
                        </option>
                        <option value="updated_at" @if (request()->query('search_column') == 'updated_at') selected @endif>
                            {{ __('view.admin.collaborator.collaborators.index.search_option_updated_at') }}
                        </option>
                    </select>
                    <input
                        id="search"
                        name="search"
                        class="form-control me-2"
                        type="search"
                        placeholder="{{ __('view.admin.collaborator.collaborators.index.search_placeholder') }}"
                        aria-label="Search"
                    >
                    <button class="btn btn-secondary" type="submit">
                        {{ __('view.admin.collaborator.collaborators.index.search_button') }}
                    </button>
                </form>
            </div>
        </nav>
        <div class="row">
            <div class="col">
                <table class="table table-hover table-bordered">
                    <thead>
                        <form action="{{ route('admin.collaborators.index') }}" method="GET">
                            <tr>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">
                                        {{ __('view.admin.collaborator.collaborators.index.sort_id') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="full_name">
                                        {{ __('view.admin.collaborator.collaborators.index.sort_full_name') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="contact">
                                        {{ __('view.admin.collaborator.collaborators.index.sort_contact') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="role">
                                        {{ __('view.admin.collaborator.collaborators.index.sort_role') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="blocked">
                                        {{ __('view.admin.collaborator.collaborators.index.sort_blocked') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="created_at">
                                        {{ __('view.admin.collaborator.collaborators.index.sort_created_at') }}
                                    </button>
                                </th>
                                <th scope="col">
                                    <button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="updated_at">
                                        {{ __('view.admin.collaborator.collaborators.index.sort_updated_at') }}
                                    </button>
                                </th>
                            </tr>
                            <input name="order" value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif" hidden>
                            <input name="search_column" value="{{ request()->query('search_column') }}" hidden>
                            <input name="search" value="{{ request()->query('search') }}" hidden>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($collaborators as $collaborator)
                            <tr
                                class="@if (!$collaborator->locks->isEmpty() && $collaborator->locks->first()->admin_id != auth()->user()->id) table-warning @endif">
                                <th scope="row">{{ $collaborator->id }}</th>
                                <td>{{ $collaborator->full_name }}</td>
                                <td>{{ $collaborator->contact }}</td>
                                <td>{{ __('app.collaborator.role.' . (optional($collaborator->role)?->value ?? CollaboratorRole::EXTERNAL->value)) }}</td>
                                <td>
                                    @if ($collaborator->blocked == 1)
                                        {{ __('view.admin.collaborator.collaborators.index.yes') }}
                                    @else
                                        {{ __('view.admin.collaborator.collaborators.index.no') }}
                                    @endif
                                </td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($collaborator->created_at)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($collaborator->updated_at)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.collaborators.show', $collaborator->id) }}" type="button"
                                            class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('admin.collaborators.edit', $collaborator->id) }}" type="button"
                                            class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i></a>
                                        <form action="{{ route('admin.collaborators.destroy', $collaborator->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger deleteCollaboratorButton"><i
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
        {{ $collaborators->links('pagination::bootstrap-5') }}
    </div>

@endsection
