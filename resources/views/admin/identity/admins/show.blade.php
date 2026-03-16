@extends('layouts.admin')
@section('title', __('view.admin.identity.admins.show.title') . ' ' . $admin->id)

@section('content')
    <div class="mb-auto container-fluid">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <h2 class="card-header">{{ __('view.admin.identity.admins.show.heading', ['id' => $admin->id, 'username' => $admin->username]) }}</h2>
                    <div class="card-body d-flex">
                        <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteAdminButton btn btn-danger" data-confirm-message="{{ __('view.admin.identity.admins.index.delete_confirm') }}"><i class="bi bi-trash-fill"></i>
                                {{ __('view.admin.identity.admins.show.delete') }}
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.identity.admins.show.id') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $admin->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.identity.admins.show.username') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $admin->username }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.identity.admins.show.created_at') }}</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($admin->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.identity.admins.show.updated_at') }}</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($admin->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
