@extends('layouts.admin')
@section('title', __('view.admin.identity.users.create.title'))

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">{{ __('view.admin.identity.users.create.heading') }}</h2>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">{{ __('view.admin.identity.users.create.username') }}</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                            name="username" value="{{ old('username') }}">
                        @error('username')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('view.admin.identity.users.create.password') }}</label>
                        <input type="text" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password">
                        @error('password')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> {{ __('view.admin.identity.users.create.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
